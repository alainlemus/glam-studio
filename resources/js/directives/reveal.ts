import type { Directive } from 'vue';

/**
 * v-reveal: anima un elemento cuando entra en el viewport.
 * Uso: v-reveal="index * 60" (delay opcional en ms)
 */

let observer: IntersectionObserver | null = null;

if (typeof IntersectionObserver !== 'undefined') {
    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer?.unobserve(entry.target);
                }
            }
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
    );
}

export const vReveal: Directive<HTMLElement, number | undefined> = {
    mounted(el, binding) {
        el.classList.add('reveal');
        if (binding.value) {
            el.style.setProperty('--reveal-delay', `${binding.value}ms`);
        }

        if (!observer) {
            el.classList.add('is-visible');
            return;
        }

        observer.observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
