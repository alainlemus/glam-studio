<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    branch: {
        name: string;
        address?: string | null;
        latitude?: string | number | null;
        longitude?: string | number | null;
    };
}>();

const query = computed(() => {
    if (props.branch.latitude && props.branch.longitude) {
        return `${props.branch.latitude},${props.branch.longitude}`;
    }
    return `${props.branch.name} ${props.branch.address ?? ''}`.trim();
});

const mapSrc = computed(() => `https://www.google.com/maps?q=${encodeURIComponent(query.value)}&output=embed`);
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-smoke bg-graphite" style="aspect-ratio: 16 / 9">
        <iframe
            :src="mapSrc"
            :title="`Mapa de ${branch.name}`"
            class="h-full w-full grayscale-[15%]"
            style="border: 0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
    </div>
</template>
