<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import {
    Bell,
    CalendarPlus,
    CalendarX,
    UserX,
    PackageX,
    CheckCheck,
    BellOff,
} from '@lucide/vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type NotificationItem = {
    id: string;
    title: string;
    message: string;
    url: string | null;
    icon: string | null;
    color: string | null;
    read: boolean;
    createdAt: string;
};

const page = usePage();
const notifications = computed(() => (page.props.notifications as any) as { unreadCount: number; recent: NotificationItem[] } | null);

const icons: Record<string, any> = {
    'calendar-plus': CalendarPlus,
    'calendar-x': CalendarX,
    'user-x': UserX,
    'package-x': PackageX,
};

const colorClasses: Record<string, string> = {
    blue: 'bg-blue-500/15 text-blue-400',
    red: 'bg-red-500/15 text-red-400',
    orange: 'bg-orange-500/15 text-orange-400',
    amber: 'bg-amber-500/15 text-amber-400',
};

const iconFor = (key: string | null) => icons[key || ''] || Bell;
const colorFor = (key: string | null) => colorClasses[key || ''] || 'bg-silver/15 text-silver-bright';

const openNotification = (item: NotificationItem) => {
    if (!item.read) {
        router.post(`/admin/notifications/${item.id}/read`, {}, { preserveScroll: true, preserveState: true });
    }
    if (item.url) {
        router.visit(item.url);
    }
};

const markAllAsRead = () => {
    router.post('/admin/notifications/read-all', {}, { preserveScroll: true, preserveState: true });
};

let interval: ReturnType<typeof setInterval> | undefined;
onMounted(() => {
    interval = setInterval(() => {
        router.reload({ only: ['notifications'] });
    }, 30000);
});
onBeforeUnmount(() => interval && clearInterval(interval));
</script>

<template>
    <DropdownMenu v-if="notifications">
        <DropdownMenuTrigger as-child>
            <button class="relative flex h-9 w-9 items-center justify-center rounded-full text-mercury transition hover:bg-silver/10 hover:text-cream">
                <Bell class="h-5 w-5" />
                <span
                    v-if="notifications.unreadCount > 0"
                    class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white"
                >
                    {{ notifications.unreadCount > 9 ? '9+' : notifications.unreadCount }}
                </span>
            </button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" :side-offset="8" class="w-96 max-w-[calc(100vw-2rem)] border-smoke bg-card p-0">
            <div class="flex items-center justify-between border-b border-smoke px-4 py-3">
                <span class="font-serif text-sm font-semibold text-cream">Notificaciones</span>
                <button
                    v-if="notifications.unreadCount > 0"
                    @click="markAllAsRead"
                    class="flex items-center gap-1 text-xs font-medium text-silver-bright hover:text-white"
                >
                    <CheckCheck class="h-3.5 w-3.5" />
                    Marcar todas
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <button
                    v-for="item in notifications.recent"
                    :key="item.id"
                    @click="openNotification(item)"
                    class="flex w-full items-start gap-3 border-b border-smoke/50 px-4 py-3 text-left transition last:border-0 hover:bg-graphite/50"
                    :class="!item.read && 'bg-silver/[0.04]'"
                >
                    <div :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-full', colorFor(item.color)]">
                        <component :is="iconFor(item.icon)" class="h-4 w-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-sm font-medium text-cream">{{ item.title }}</p>
                            <span v-if="!item.read" class="h-1.5 w-1.5 shrink-0 rounded-full bg-silver-bright"></span>
                        </div>
                        <p class="mt-0.5 line-clamp-2 text-xs text-mercury">{{ item.message }}</p>
                        <p class="mt-1 text-[11px] text-mercury/70">{{ item.createdAt }}</p>
                    </div>
                </button>

                <div v-if="notifications.recent.length === 0" class="flex flex-col items-center gap-2 px-4 py-10 text-center">
                    <BellOff class="h-8 w-8 text-mercury/30" />
                    <p class="text-sm text-mercury">No hay notificaciones</p>
                </div>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
