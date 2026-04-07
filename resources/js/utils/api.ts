/**
 * Returns the stored Sanctum bearer token.
 */
export function getToken(): string | null {
    return localStorage.getItem('api_token');
}

/**
 * Stores the Sanctum bearer token.
 */
export function setToken(token: string): void {
    localStorage.setItem('api_token', token);
}

/**
 * Removes the stored token.
 */
export function clearToken(): void {
    localStorage.removeItem('api_token');
}

/**
 * Fetch wrapper that injects the Sanctum bearer token.
 */
export async function apiFetch<T>(
    url: string,
    options: RequestInit = {},
): Promise<T> {
    const token = getToken();
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...(options.headers as Record<string, string>),
    };

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(url, { ...options, headers });

    if (!response.ok) {
        throw new Error(`API error: ${response.status.toString()}`);
    }

    if (response.status === 204) {
        return undefined as T;
    }

    return (await response.json()) as T;
}
