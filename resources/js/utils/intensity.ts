const INTENSITY_COLORS: Record<number, string> = {
    0: 'bg-neutral-200 dark:bg-neutral-700',
    1: 'bg-green-200 dark:bg-green-900',
    2: 'bg-green-400 dark:bg-green-700',
    3: 'bg-green-500 dark:bg-green-600',
    4: 'bg-green-600 dark:bg-green-500',
};

export function getIntensityColor(intensity: number): string {
    return INTENSITY_COLORS[intensity] ?? INTENSITY_COLORS[0];
}
