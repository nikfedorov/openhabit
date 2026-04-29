import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { useLoadingBar } from '@/composables/useLoadingBar';
import { apiFetch, clearToken, getToken, setToken } from '../../utils/api';

const mockLocalStorage: Record<string, string> = {};

beforeEach(() => {
    vi.stubGlobal('localStorage', {
        getItem: vi.fn((key: string) => mockLocalStorage[key] ?? null),
        setItem: vi.fn((key: string, value: string) => {
            mockLocalStorage[key] = value;
        }),
        removeItem: vi.fn((key: string) => {
            delete mockLocalStorage[key];
        }),
    });
});

afterEach(() => {
    vi.restoreAllMocks();
    vi.unstubAllGlobals();
    Object.keys(mockLocalStorage).forEach((k) => delete mockLocalStorage[k]);
});

describe('getToken', () => {
    it('returns null when no token stored', () => {
        expect(getToken()).toBeNull();
    });

    it('returns stored token', () => {
        mockLocalStorage['api_token'] = 'test-token';
        expect(getToken()).toBe('test-token');
    });
});

describe('setToken', () => {
    it('stores token in localStorage', () => {
        setToken('my-token');
        expect(localStorage.setItem).toHaveBeenCalledWith(
            'api_token',
            'my-token',
        );
    });
});

describe('clearToken', () => {
    it('removes token from localStorage', () => {
        clearToken();
        expect(localStorage.removeItem).toHaveBeenCalledWith('api_token');
    });
});

describe('apiFetch', () => {
    it('makes request with JSON headers and returns parsed JSON', async () => {
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({ data: 'test' }),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        const result = await apiFetch<{ data: string }>('/api/test');
        expect(result).toEqual({ data: 'test' });
        expect(fetch).toHaveBeenCalledWith('/api/test', {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        });
    });

    it('includes Authorization header when token exists', async () => {
        mockLocalStorage['api_token'] = 'bearer-token';
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({}),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        await apiFetch('/api/test');
        expect(fetch).toHaveBeenCalledWith('/api/test', {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: 'Bearer bearer-token',
            },
        });
    });

    it('does not include Authorization header without token', async () => {
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({}),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        await apiFetch('/api/test');
        const headers = (fetch as ReturnType<typeof vi.fn>).mock.calls[0][1]
            .headers;
        expect(headers).not.toHaveProperty('Authorization');
    });

    it('throws on non-ok response', async () => {
        const mockResponse = { ok: false, status: 401 };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        await expect(apiFetch('/api/test')).rejects.toThrow('API error: 401');
    });

    it('returns undefined for 204 No Content', async () => {
        const mockResponse = { ok: true, status: 204 };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        const result = await apiFetch('/api/test');
        expect(result).toBeUndefined();
    });

    it('passes additional options through', async () => {
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({}),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        await apiFetch('/api/test', {
            method: 'POST',
            body: JSON.stringify({ key: 'value' }),
        });
        expect(fetch).toHaveBeenCalledWith('/api/test', {
            method: 'POST',
            body: JSON.stringify({ key: 'value' }),
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        });
    });

    it('merges custom headers with defaults', async () => {
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({}),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        await apiFetch('/api/test', {
            headers: { 'X-Custom': 'value' },
        });
        expect(fetch).toHaveBeenCalledWith('/api/test', {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Custom': 'value',
            },
        });
    });

    it('does not trigger loading bar when silent is true', async () => {
        const { reset } = useLoadingBar();
        reset();
        const mockResponse = {
            ok: true,
            status: 200,
            json: vi.fn().mockResolvedValue({}),
        };
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(mockResponse));

        const { loading } = useLoadingBar();
        expect(loading.value).toBe(false);
        await apiFetch('/api/test', {}, { silent: true });
        expect(loading.value).toBe(false);
    });
});
