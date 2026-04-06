export function formatDate(d: Date): string {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

export function addDays(dateStr: string, days: number): string {
    const [y, m, d] = dateStr.split('-').map(Number);
    return formatDate(new Date(y, m - 1, d + days));
}

export function todayStr(): string {
    return formatDate(new Date());
}
