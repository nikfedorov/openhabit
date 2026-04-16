/**
 * Returns true when the user has requested reduced motion via their OS
 * setting (`prefers-reduced-motion: reduce`). Safely returns false in
 * environments without `matchMedia` (SSR, legacy browsers).
 *
 * Use this to gate JavaScript-driven animations (Web Animations API,
 * timed effects). CSS transitions and @keyframes animations are handled
 * globally via the `prefers-reduced-motion` media query in transitions.css.
 */
/* v8 ignore start */
export function prefersReducedMotion(): boolean {
    if (typeof window === 'undefined' || !window.matchMedia) {
        return false;
    }
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}
/* v8 ignore stop */
