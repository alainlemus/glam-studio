<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ShieldCheck, ExternalLink } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    settings: {
        privacy_policy: string | null;
        privacy_policy_updated_at: string | null;
    };
}>();

const form = useForm({
    privacy_policy: props.settings.privacy_policy || '',
});

const formattedDate = computed(() => {
    if (!props.settings.privacy_policy_updated_at) return null;
    return new Date(props.settings.privacy_policy_updated_at).toLocaleDateString('es-MX', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const submit = () => {
    form.put('/admin/privacy-policy', { preserveScroll: true });
};
</script>

<template>
    <Head title="Aviso de privacidad" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 lg:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-eyebrow">Sistema</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Aviso de privacidad</h2>
                <p class="mt-1 text-sm text-mercury">Contenido que se muestra en la página pública de aviso de privacidad.</p>
            </div>
            <a
                href="/aviso-de-privacidad"
                target="_blank"
                rel="noopener"
                class="btn-ghost-elegant inline-flex items-center gap-2 text-sm"
            >
                <ExternalLink class="h-3.5 w-3.5" />
                Ver página pública
            </a>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label class="block text-xs font-medium uppercase tracking-wider text-mercury">Contenido (HTML)</label>
                    <span v-if="formattedDate" class="text-xs text-mercury">Última actualización: {{ formattedDate }}</span>
                </div>
                <textarea
                    v-model="form.privacy_policy"
                    rows="24"
                    class="input-elegant font-mono text-xs leading-relaxed"
                    placeholder="<h2>Título</h2><p>Contenido...</p>"
                ></textarea>
                <p class="mt-1.5 text-xs text-mercury">
                    Puedes usar etiquetas HTML básicas: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;.
                </p>
                <p v-if="form.errors.privacy_policy" class="mt-1 text-xs text-red-400">{{ form.errors.privacy_policy }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <ShieldCheck class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
