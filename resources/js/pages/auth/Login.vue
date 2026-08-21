<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LogIn } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';

defineOptions({
    layout: {
        title: 'Bienvenida de nuevo',
        description: 'Ingresa tu correo y contraseña para continuar',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-emerald-400"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div>
            <label for="email" class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Correo electrónico</label>
            <input
                id="email"
                type="email"
                name="email"
                required
                autofocus
                :tabindex="1"
                autocomplete="email"
                placeholder="correo@ejemplo.com"
                class="input-elegant"
            />
            <InputError class="mt-1.5" :message="errors.email" />
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-xs font-medium uppercase tracking-wider text-mercury">Contraseña</label>
                <TextLink
                    v-if="canResetPassword"
                    href="/forgot-password"
                    class="text-xs text-silver-bright hover:text-cream"
                    :tabindex="5"
                >
                    ¿Olvidaste tu contraseña?
                </TextLink>
            </div>
            <PasswordInput
                id="password"
                name="password"
                required
                :tabindex="2"
                autocomplete="current-password"
                placeholder="Contraseña"
                class="input-elegant"
            />
            <InputError class="mt-1.5" :message="errors.password" />
        </div>

        <label for="remember" class="inline-flex w-fit items-center gap-2.5 text-sm text-pearl">
            <input id="remember" name="remember" type="checkbox" :tabindex="3" class="h-4 w-4 rounded border-smoke bg-graphite text-silver focus:ring-silver focus:ring-offset-0" />
            Recordarme
        </label>

        <button
            type="submit"
            class="btn-primary-elegant mt-1 h-12 w-full text-sm"
            :tabindex="4"
            :disabled="processing"
            data-test="login-button"
        >
            <Spinner v-if="processing" />
            <LogIn v-else class="h-4 w-4" />
            Iniciar sesión
        </button>
    </Form>
</template>
