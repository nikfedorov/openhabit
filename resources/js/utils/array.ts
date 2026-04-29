/**
 * Toggles a number in a sorted array (immutable).
 * Removes the item if present, adds it in sorted position if absent.
 */
export function toggleSortedItem(array: number[], item: number): number[] {
    const idx = array.indexOf(item);
    if (idx >= 0) {
        return array.filter((_, i) => i !== idx);
    }
    return [...array, item].sort((a, b) => a - b);
}
