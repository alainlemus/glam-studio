<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Award,
    Search,
    Gift,
    Sparkles,
    Plus,
    Crown,
    Minus,
    Trophy,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    cards: any;
    filters: any;
    summary: any;
}>();

const search = ref(props.filters.search || '');
const repeatOnly = ref(props.filters.repeat_only === '1' || props.filters.repeat_only === true);

const filter = () => {
    router.get('/admin/loyalty', {
        search: search.value || undefined,
        repeat_only: repeatOnly.value ? '1' : undefined,
    }, { preserveState: true });
};

const showStampModal = ref(false);
const stamping = ref<any>(null);
const stampQty = ref(1);

const addStamp = (card: any) => {
    stamping.value = card;
    stampQty.value = 1;
    showStampModal.value = true;
};

const submitStamp = () => {
    if (stampQty.value < 1) return;
    router.post(`/admin/loyalty/${stamping.value.id}/add-stamp`, { quantity: stampQty.value }, {
        onSuccess: () => (showStampModal.value = false),
    });
};

const redeem = async (card: any) => {
    const isRepeat = card.total_rewards_claimed >= 1;
    if (await confirmDialog({
        title: '¿Canjear recompensa?',
        description: isRepeat
            ? `Esta será la recompensa #${card.total_rewards_claimed + 1} de ${card.client?.name}. Al ser una clienta que ya completó la tarjeta antes, recuerda entregarle el regalo especial.`
            : `Se reiniciará el progreso de la tarjeta de ${card.client?.name}.`,
        confirmText: 'Canjear',
    })) router.post(`/admin/loyalty/${card.id}/redeem`);
};
</script>

<template>
    <Head title="Tarjeta de Lealtad" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">CRM Premium</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Tarjetas de Lealtad</h2>
            <p class="mt-1 text-sm text-mercury">{{ summary.total }} tarjetas · {{ summary.completed }} completas</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card-glow p-6">
                <div class="text-eyebrow">Total tarjetas</div>
                <div class="mt-2 font-serif text-4xl font-semibold text-glitter">{{ summary.total }}</div>
            </div>
            <div class="card-glow p-6">
                <div class="text-eyebrow">Completas</div>
                <div class="mt-2 font-serif text-4xl font-semibold text-emerald-400">{{ summary.completed }}</div>
            </div>
            <div class="card-glow p-6">
                <div class="text-eyebrow">Recompensas canjeadas</div>
                <div class="mt-2 font-serif text-4xl font-semibold text-silver-bright">{{ summary.rewards_claimed }}</div>
            </div>
            <button type="button" class="card-glow p-6 text-left transition hover:border-gold/30" @click="repeatOnly = !repeatOnly; filter()">
                <div class="flex items-center gap-1.5 text-eyebrow">
                    <Trophy class="h-3 w-3 text-gold-bright" />
                    Clientas recurrentes
                </div>
                <div class="mt-2 font-serif text-4xl font-semibold text-gold-bright">{{ summary.repeat_clients }}</div>
                <p class="mt-1 text-[11px] text-mercury">Ya llenaron la tarjeta antes · merecen el regalo especial</p>
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-smoke bg-card p-4">
            <div class="relative flex-1 min-w-[200px]">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                <input v-model="search" type="text" placeholder="Buscar cliente por nombre o teléfono..." class="input-elegant pl-10" @input="filter" />
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-pearl">
                <input v-model="repeatOnly" type="checkbox" class="h-4 w-4 rounded border-smoke bg-graphite text-gold focus:ring-gold" @change="filter" />
                Solo recurrentes
            </label>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="card in cards.data" :key="card.id" class="card-glow relative overflow-hidden p-6">
                <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-silver/10 blur-2xl"></div>

                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border border-silver/30 bg-gradient-to-br from-silver-bright/20 to-graphite text-silver-bright">
                            <Award class="h-5 w-5" />
                        </div>
                        <span v-if="card.stamps_current >= card.stamps_required" class="chip bg-silver/20 text-silver-bright">
                            <Sparkles class="h-3 w-3" />
                            Completa
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="text-eyebrow">Tarjeta</div>
                        <div class="font-mono text-xs text-pearl">{{ card.code }}</div>
                    </div>

                    <Link :href="`/admin/clients/${card.client_id}`" class="mt-2 block font-serif text-xl font-semibold text-cream transition hover:text-silver-bright">
                        {{ card.client?.name }}
                    </Link>

                    <div class="mt-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-mercury">Progreso</span>
                            <span class="font-mono text-cream">
                                <span class="text-lg font-semibold text-silver-bright">{{ card.stamps_current }}</span>
                                <span class="text-mercury">/{{ card.stamps_required }}</span>
                            </span>
                        </div>
                        <div class="mt-2 grid grid-cols-10 gap-1">
                            <div
                                v-for="i in card.stamps_required"
                                :key="i"
                                :class="[
                                    'h-8 rounded transition',
                                    i <= card.stamps_current
                                        ? 'bg-gradient-to-br from-silver-bright-bright to-white shadow-gold'
                                        : 'border border-smoke bg-graphite',
                                ]"
                            >
                                <Crown v-if="i === card.stamps_required && i <= card.stamps_current" class="m-auto mt-1 h-4 w-4 text-ink" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs">
                        <span class="text-mercury">
                            Canjeados: <span class="font-semibold text-cream">{{ card.total_rewards_claimed }}</span>
                        </span>
                        <span v-if="card.total_rewards_claimed >= 1" class="chip bg-gold/15 text-gold-bright" title="Clienta recurrente: entregar regalo especial">
                            <Trophy class="h-3 w-3" />
                            Regalo especial
                        </span>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button
                            v-if="card.stamps_current < card.stamps_required"
                            @click="addStamp(card)"
                            class="flex-1 rounded-md bg-silver/15 px-3 py-2 text-xs font-medium text-silver-bright hover:bg-gold/25"
                        >
                            <Plus class="mr-1 inline h-3.5 w-3.5" />
                            Sello
                        </button>
                        <button
                            @click="redeem(card)"
                            :disabled="card.stamps_current < card.stamps_required"
                            :class="[
                                'rounded-md px-3 py-2 text-xs font-medium disabled:cursor-not-allowed disabled:opacity-40',
                                card.stamps_current < card.stamps_required ? 'flex-1 bg-silver/15 text-silver-bright hover:bg-silver/25' : 'w-full bg-gold/20 text-gold-bright hover:bg-gold/30',
                            ]"
                        >
                            <Gift class="mr-1 inline h-3.5 w-3.5" />
                            Canjear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="cards.data.length === 0" class="card-elegant p-16 text-center">
            <Award class="mx-auto h-12 w-12 text-mercury/30" />
            <p class="mt-3 text-sm text-mercury">No hay tarjetas de lealtad</p>
        </div>

        <div v-if="cards.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in cards.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>

        <Dialog v-model:open="showStampModal">
            <DialogContent class="border-smoke bg-card sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle class="font-serif text-xl font-medium text-cream">Agregar sello</DialogTitle>
                    <DialogDescription class="pt-1 text-sm text-mercury">
                        {{ stamping?.client?.name }} · Progreso actual: <span class="text-cream font-medium">{{ stamping?.stamps_current }}/{{ stamping?.stamps_required }}</span>
                    </DialogDescription>
                </DialogHeader>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Cantidad de sellos</label>
                    <div class="flex items-center gap-2">
                        <button type="button" class="btn-ghost-elegant h-11 w-11 shrink-0 px-0" :disabled="stampQty <= 1" @click="stampQty--">
                            <Minus class="h-4 w-4" />
                        </button>
                        <input v-model.number="stampQty" type="number" min="1" class="input-elegant text-center" />
                        <button type="button" class="btn-ghost-elegant h-11 w-11 shrink-0 px-0" @click="stampQty++">
                            <Plus class="h-4 w-4" />
                        </button>
                    </div>
                </div>
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" class="btn-ghost-elegant h-11 px-6" @click="showStampModal = false">Cancelar</button>
                    <button type="button" :disabled="stampQty < 1" class="btn-primary-elegant h-11 px-6 disabled:opacity-50" @click="submitStamp">
                        Agregar
                    </button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>