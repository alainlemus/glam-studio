<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { ShieldCheck } from '@lucide/vue';

const STORAGE_KEY = 'glam_privacy_consent';
const visible = ref(false);

onMounted(() => {
    try {
        if (!localStorage.getItem(STORAGE_KEY)) {
            visible.value = true;
        }
    } catch {
        visible.value = true;
    }
});

const accept = () => {
    try {
        localStorage.setItem(STORAGE_KEY, '1');
    } catch {
        // localStorage no disponible: el banner solo se ocultará por esta sesión.
    }
    visible.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-4 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-4 opacity-0"
    >
        <div
            v-if="visible"
            role="dialog"
            aria-live="polite"
            aria-label="Aviso de cookies y privacidad"
            class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] px-4 pb-4 sm:px-6 sm:pb-6"
        >
            <div class="glass-card pointer-events-auto mx-auto flex max-w-3xl flex-col items-start gap-4 border border-smoke/80 p-5 shadow-2xl shadow-black/40 sm:flex-row sm:items-center sm:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-gold/30 bg-gold/10">
                    <ShieldCheck class="h-5 w-5" style="color: var(--color-gold)" />
                </div>
                <div class="flex-1 text-sm leading-relaxed text-pearl">
                    Usamos los datos que nos compartes al agendar tu cita (nombre, teléfono y correo) para confirmarla y darte
                    seguimiento, conforme a nuestro
                    <a href="/aviso-de-privacidad" target="_blank" rel="noopener" class="font-medium text-silver-bright underline underline-offset-2 hover:text-cream">
                        aviso de privacidad
                    </a>. No usamos cookies de rastreo publicitario.
                </div>
                <div class="flex w-full shrink-0 gap-3 sm:w-auto">
                    <a
                        href="/aviso-de-privacidad"
                        class="btn-ghost-elegant h-11 flex-1 justify-center text-xs sm:flex-none"
                    >
                        Más información
                    </a>
                    <button type="button" class="btn-primary-elegant h-11 flex-1 justify-center text-xs sm:flex-none" @click="accept">
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
