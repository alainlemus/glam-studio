<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { MessageSquareQuote, Star } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    testimonial?: any;
}>();

const form = useForm({
    client_name: props.testimonial?.client_name || '',
    quote: props.testimonial?.quote || '',
    rating: props.testimonial?.rating || 5,
    sort_order: props.testimonial?.sort_order ?? 0,
    is_active: props.testimonial?.is_active ?? true,
    photo: null as File | null,
});

const photoPreview = ref<string | null>(null);

const onPhotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] || null;
    form.photo = file;
    photoPreview.value = file ? URL.createObjectURL(file) : null;
};

const currentPhoto = () => photoPreview.value || props.testimonial?.photo_url || null;

const submit = () => {
    if (props.testimonial) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/admin/testimonials/${props.testimonial.id}`, { forceFormData: true });
    } else {
        form.post('/admin/testimonials', { forceFormData: true });
    }
};
</script>

<template>
    <Head :title="testimonial ? 'Editar testimonio' : 'Nuevo testimonio'" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/testimonials" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Crecimiento</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ testimonial ? 'Editar' : 'Nuevo' }} testimonio</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Foto</label>
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full border border-smoke bg-graphite">
                        <img v-if="currentPhoto()" :src="currentPhoto()!" class="h-full w-full object-cover" alt="Foto" />
                        <MessageSquareQuote v-else class="h-5 w-5 text-mercury" />
                    </div>
                    <input type="file" accept="image/*" class="input-elegant" @change="onPhotoChange" />
                </div>
                <p v-if="form.errors.photo" class="mt-1 text-xs text-red-400">{{ form.errors.photo }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre de la clienta *</label>
                    <input v-model="form.client_name" required class="input-elegant" />
                    <p v-if="form.errors.client_name" class="mt-1 text-xs text-red-400">{{ form.errors.client_name }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Calificación *</label>
                    <div class="flex h-[46px] items-center gap-1">
                        <button
                            v-for="i in 5"
                            :key="i"
                            type="button"
                            @click="form.rating = i"
                            class="p-0.5"
                        >
                            <Star class="h-6 w-6 transition" :class="i <= form.rating ? 'fill-gold-bright text-gold-bright' : 'text-smoke hover:text-mercury'" />
                        </button>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Testimonio *</label>
                    <textarea v-model="form.quote" required rows="4" class="input-elegant" placeholder="&quot;El mejor salón que he visitado...&quot;"></textarea>
                    <p v-if="form.errors.quote" class="mt-1 text-xs text-red-400">{{ form.errors.quote }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Orden</label>
                    <input v-model="form.sort_order" type="number" class="input-elegant" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-3">
                        <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver" />
                        <span class="text-sm font-medium text-cream">Mostrar en el sitio</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/testimonials" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
