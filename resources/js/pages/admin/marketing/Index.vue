<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Megaphone,
    Crown,
    Send,
    Power,
    Trash2,
    Sparkles,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    campaigns: any;
    filters: any;
}>();

const status = ref(props.filters.status || '');

const filter = () => {
    router.get('/admin/marketing', {
        status: status.value || undefined,
    }, { preserveState: true });
};

const statusColors: Record<string, string> = {
    draft: 'bg-zinc-500/15 text-zinc-400',
    scheduled: 'bg-blue-500/15 text-blue-400',
    active: 'bg-emerald-500/15 text-emerald-400',
    finished: 'bg-purple-500/15 text-purple-400',
    cancelled: 'bg-red-500/15 text-red-400',
};

const typeIcons: Record<string, string> = {
    whatsapp: '📱',
    sms: '💬',
    email: '✉️',
    promotion: '🎁',
};

const activate = (id: number) => router.post(`/admin/marketing/${id}/activate`);
const send = async (id: number) => {
    if (await confirmDialog({
        title: '¿Enviar campaña a todos los clientes?',
        description: 'El mensaje se enviará de inmediato a todos los clientes activos.',
        confirmText: 'Enviar',
    })) router.post(`/admin/marketing/${id}/send`);
};
const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar campaña?',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/marketing/${id}`);
};
</script>

<template>
    <Head title="Marketing" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Crecimiento</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Marketing</h2>
                <p class="mt-1 text-sm text-mercury">Campañas y promociones</p>
            </div>
            <Link href="/admin/marketing/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nueva campaña
            </Link>
        </div>

        <div class="flex gap-3 rounded-xl border border-smoke bg-card p-4">
            <select v-model="status" class="input-elegant max-w-xs appearance-none pr-9" @change="filter">
                <option value="">Todos los estatus</option>
                <option value="draft">Borrador</option>
                <option value="scheduled">Programada</option>
                <option value="active">Activa</option>
                <option value="finished">Finalizada</option>
                <option value="cancelled">Cancelada</option>
            </select>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div v-for="campaign in campaigns.data" :key="campaign.id" class="card-glow relative overflow-hidden p-6">
                <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-silver/10 blur-2xl"></div>

                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ typeIcons[campaign.type] || '📣' }}</span>
                            <span class="chip border border-silver/30 bg-silver/10 text-[10px] uppercase tracking-widest text-silver-bright">
                                {{ campaign.type }}
                            </span>
                        </div>
                        <Crown v-if="campaign.status === 'active'" class="h-5 w-5 text-silver-bright" />
                    </div>

                    <h3 class="mt-4 font-serif text-xl font-semibold text-cream">{{ campaign.name }}</h3>
                    <p class="mt-2 text-sm text-mercury line-clamp-3">{{ campaign.description }}</p>

                    <div v-if="campaign.discount_percentage" class="mt-5">
                        <div class="font-serif text-5xl font-semibold text-glitter">-{{ campaign.discount_percentage }}%</div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 border-t border-smoke pt-4 text-xs">
                        <div>
                            <div class="text-mercury">Inicio</div>
                            <div class="font-medium text-cream">{{ campaign.start_date }}</div>
                        </div>
                        <div>
                            <div class="text-mercury">Fin</div>
                            <div class="font-medium text-cream">{{ campaign.end_date || '—' }}</div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-mercury">Mensajes enviados</div>
                            <div class="font-medium text-cream">
                                <span class="text-silver-bright">{{ campaign.messages_sent }}</span> / {{ campaign.target_audience || '∞' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span :class="['chip', statusColors[campaign.status]]">{{ campaign.status }}</span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <Link :href="`/admin/marketing/${campaign.id}/edit`" class="flex-1 rounded-md border border-smoke bg-graphite py-2 text-center text-xs font-medium text-pearl hover:border-silver/40">
                            Editar
                        </Link>
                        <button v-if="campaign.status !== 'active'" @click="activate(campaign.id)" class="rounded-md bg-emerald-500/15 px-3 py-2 text-xs font-medium text-emerald-400 hover:bg-emerald-500/25">
                            <Power class="h-3.5 w-3.5" />
                        </button>
                        <button @click="send(campaign.id)" class="rounded-md bg-silver/15 px-3 py-2 text-xs font-medium text-silver-bright hover:bg-gold/25">
                            <Send class="h-3.5 w-3.5" />
                        </button>
                        <button @click="destroy(campaign.id)" class="rounded-md bg-red-500/10 px-3 py-2 text-xs text-red-400 hover:bg-red-500/20">
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="campaigns.data.length === 0" class="card-elegant p-16 text-center lg:col-span-2">
                <Megaphone class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">No hay campañas creadas</p>
            </div>
        </div>

        <div v-if="campaigns.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in campaigns.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>