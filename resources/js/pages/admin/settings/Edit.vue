<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Globe, X } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    settings: {
        site_name: string;
        tagline: string | null;
        footer_description: string | null;
        notification_email: string | null;
        logo_url: string | null;
        instagram_url: string | null;
        facebook_url: string | null;
        tiktok_url: string | null;
    };
}>();

const form = useForm({
    site_name: props.settings.site_name || '',
    tagline: props.settings.tagline || '',
    footer_description: props.settings.footer_description || '',
    notification_email: props.settings.notification_email || '',
    instagram_url: props.settings.instagram_url || '',
    facebook_url: props.settings.facebook_url || '',
    tiktok_url: props.settings.tiktok_url || '',
    logo: null as File | null,
    remove_logo: false,
});

const logoPreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const onLogoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] || null;
    form.logo = file;
    form.remove_logo = false;
    logoPreview.value = file ? URL.createObjectURL(file) : null;
};

const removeLogo = () => {
    form.logo = null;
    form.remove_logo = true;
    logoPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const currentLogo = () => logoPreview.value || (!form.remove_logo ? props.settings.logo_url : null);

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'put' })).post('/admin/settings', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.logo = null;
            form.remove_logo = false;
            logoPreview.value = null;
        },
    });
};
</script>

<template>
    <Head title="Configuración del sitio" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Sistema</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Configuración del sitio</h2>
            <p class="mt-1 text-sm text-mercury">Marca, logo y redes sociales que se muestran en el sitio público.</p>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-6 p-6">
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Logo</label>
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-smoke bg-graphite">
                        <img v-if="currentLogo()" :src="currentLogo()!" alt="Logo actual" class="h-full w-full object-contain" />
                        <span v-else class="text-[10px] uppercase tracking-wider text-mercury">Sin logo</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <input ref="fileInput" type="file" accept="image/*" class="input-elegant" @change="onLogoChange" />
                        <button
                            v-if="currentLogo()"
                            type="button"
                            class="inline-flex w-fit items-center gap-1 text-xs text-mercury hover:text-cream"
                            @click="removeLogo"
                        >
                            <X class="h-3 w-3" />
                            Quitar logo (usar el predeterminado)
                        </button>
                        <p v-if="form.errors.logo" class="text-xs text-red-400">{{ form.errors.logo }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre del sitio *</label>
                    <input v-model="form.site_name" required class="input-elegant" />
                    <p v-if="form.errors.site_name" class="mt-1 text-xs text-red-400">{{ form.errors.site_name }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Tagline</label>
                    <input v-model="form.tagline" placeholder="Beauty & More" class="input-elegant" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción del footer</label>
                    <textarea v-model="form.footer_description" rows="3" class="input-elegant"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Correo para notificaciones</label>
                    <input v-model="form.notification_email" type="email" placeholder="reservas@tunegocio.com" class="input-elegant" />
                    <p class="mt-1.5 text-xs text-mercury">A este correo llegará copia de cada cita reservada desde el sitio. Si se deja vacío, se enviará a todos los administradores.</p>
                    <p v-if="form.errors.notification_email" class="mt-1 text-xs text-red-400">{{ form.errors.notification_email }}</p>
                </div>
            </div>

            <div class="border-t border-smoke pt-6">
                <p class="mb-3 text-xs font-medium uppercase tracking-wider text-mercury">Redes sociales</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Instagram</label>
                        <input v-model="form.instagram_url" type="url" placeholder="https://instagram.com/..." class="input-elegant" />
                        <p v-if="form.errors.instagram_url" class="mt-1 text-xs text-red-400">{{ form.errors.instagram_url }}</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Facebook</label>
                        <input v-model="form.facebook_url" type="url" placeholder="https://facebook.com/..." class="input-elegant" />
                        <p v-if="form.errors.facebook_url" class="mt-1 text-xs text-red-400">{{ form.errors.facebook_url }}</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">TikTok</label>
                        <input v-model="form.tiktok_url" type="url" placeholder="https://tiktok.com/@..." class="input-elegant" />
                        <p v-if="form.errors.tiktok_url" class="mt-1 text-xs text-red-400">{{ form.errors.tiktok_url }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-mercury">Deja vacío el campo de una red social para ocultar su ícono en el footer.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Globe class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
