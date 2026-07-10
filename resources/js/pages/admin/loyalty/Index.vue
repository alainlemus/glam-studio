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
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    cards: any;
    filters: any;
    summary: any;
}>();

const search = ref(props.filters.search || '');

const filter = () => {
    router.get('/admin/loyalty', {
        search: search.value || undefined,
    }, { preserveState: true });
};

const addStamp = (id: number) => {
    const qty = prompt('¿Cuántos sellos agregar?', '1');
    if (qty) router.post(`/admin/loyalty/${id}/add-stamp`, { quantity: parseInt(qty) });
};

const redeem = (id: number) => {
    if (confirm('¿Canjear recompensa?')) router.post(`/admin/loyalty/${id}/redeem`);
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

        <div class="grid gap-4 sm:grid-cols-3">
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
        </div>

        <div class="flex gap-3 rounded-xl border border-smoke bg-card p-4">
            <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                <input v-model="search" type="text" placeholder="Buscar cliente por nombre o teléfono..." class="input-elegant pl-10" @input="filter" />
            </div>
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
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button @click="addStamp(card.id)" class="flex-1 rounded-md bg-silver/15 px-3 py-2 text-xs font-medium text-silver-bright hover:bg-gold/25">
                            <Plus class="mr-1 inline h-3.5 w-3.5" />
                            Sello
                        </button>
                        <button
                            @click="redeem(card.id)"
                            :disabled="card.stamps_current < card.stamps_required"
                            class="flex-1 rounded-md bg-silver/15 px-3 py-2 text-xs font-medium text-silver-bright hover:bg-silver/25 disabled:cursor-not-allowed disabled:opacity-40"
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
    </div>
</template>