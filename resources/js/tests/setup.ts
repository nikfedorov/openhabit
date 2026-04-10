/**
 * Global test setup — polyfills a working localStorage when the native
 * one is broken (e.g. Node's --localstorage-file passed without a valid path).
 */
if (
    typeof globalThis.localStorage === 'undefined' ||
    !globalThis.localStorage?.clear
) {
    const store: Record<string, string> = {};

    globalThis.localStorage = {
        getItem: (key: string) => store[key] ?? null,
        setItem: (key: string, value: string) => {
            store[key] = String(value);
        },
        removeItem: (key: string) => {
            delete store[key];
        },
        clear: () => {
            Object.keys(store).forEach((k) => delete store[k]);
        },
        get length() {
            return Object.keys(store).length;
        },
        key: (index: number) => Object.keys(store)[index] ?? null,
    } as Storage;
}
