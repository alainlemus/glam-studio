<script setup lang="ts">
import { AlertTriangle } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { confirmState, resolveConfirm } from '@/composables/useConfirm';
</script>

<template>
    <Dialog :open="confirmState.open" @update:open="(v) => !v && resolveConfirm(false)">
        <DialogContent class="border-smoke bg-card sm:max-w-md">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <div
                        v-if="confirmState.variant === 'destructive'"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500/15 text-red-400"
                    >
                        <AlertTriangle class="h-5 w-5" />
                    </div>
                    <DialogTitle class="font-serif text-xl font-medium text-cream">{{ confirmState.title }}</DialogTitle>
                </div>
                <DialogDescription v-if="confirmState.description" class="pt-1 text-sm text-mercury">
                    {{ confirmState.description }}
                </DialogDescription>
            </DialogHeader>

            <div class="mt-2 flex justify-end gap-3">
                <button type="button" class="btn-ghost-elegant h-11 px-6" @click="resolveConfirm(false)">
                    {{ confirmState.cancelText }}
                </button>
                <button
                    type="button"
                    class="h-11 rounded-full px-6 text-sm font-semibold tracking-wide transition-all active:scale-[0.98]"
                    :class="confirmState.variant === 'destructive'
                        ? 'bg-red-500 text-white hover:bg-red-400'
                        : 'btn-primary-elegant'"
                    @click="resolveConfirm(true)"
                >
                    {{ confirmState.confirmText }}
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
