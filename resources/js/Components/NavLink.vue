<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? "inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-black focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
        : ""
);
</script>

<template>
    <Link :href="href" :class="classes" style="color: black">
        <slot />
    </Link>
</template>
