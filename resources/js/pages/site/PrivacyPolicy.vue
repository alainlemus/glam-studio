<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { ShieldCheck } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

const props = defineProps<{
    content: string | null;
    updatedAt: string | null;
}>();

const formattedDate = computed(() => {
    if (!props.updatedAt) return null;
    return new Date(props.updatedAt).toLocaleDateString('es-MX', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});
</script>

<template>
    <Head title="Aviso de privacidad" />

    <section class="relative overflow-hidden border-b border-smoke bg-ink py-20 lg:py-24">
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-0 h-96 w-96 -translate-x-1/2 rounded-full blur-3xl" style="background: var(--color-gold); opacity: 0.1;"></div>
        </div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <div class="mb-6 inline-flex items-center gap-2 glass-effect rounded-full px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                <ShieldCheck class="h-3 w-3" />
                Tu privacidad importa
            </div>
            <h1 class="mt-3 font-serif text-4xl font-bold tracking-tight text-cream lg:text-5xl">
                Aviso de <span class="italic" style="color: var(--color-silver-bright)">privacidad</span>
            </h1>
            <p v-if="formattedDate" class="mt-4 text-sm text-mercury">Última actualización: {{ formattedDate }}</p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card-elegant p-8 lg:p-12">
                <div v-if="content" class="privacy-content" v-html="content"></div>
                <p v-else class="text-sm text-mercury">Aún no se ha publicado el aviso de privacidad.</p>
            </div>
        </div>
    </section>
</template>

<style scoped>
.privacy-content :deep(h2) {
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    font-family: var(--font-serif, serif);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-cream);
}
.privacy-content :deep(h2:first-child) {
    margin-top: 0;
}
.privacy-content :deep(p) {
    margin-bottom: 1rem;
    line-height: 1.75;
    color: var(--color-pearl);
    font-size: 0.9375rem;
}
.privacy-content :deep(ul) {
    margin: 0 0 1rem 1.25rem;
    list-style: disc;
    color: var(--color-pearl);
    font-size: 0.9375rem;
    line-height: 1.75;
}
.privacy-content :deep(li) {
    margin-bottom: 0.375rem;
}
.privacy-content :deep(strong) {
    color: var(--color-cream);
}
</style>
