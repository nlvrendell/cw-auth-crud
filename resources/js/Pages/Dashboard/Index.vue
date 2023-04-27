<script setup>
let props = defineProps({
    token: String,
    domains: Object,
});
</script>
<template>
    This is a fucking dashboard!

    <p>token {{ token }}</p>
    <div>
        <p>Domains</p>
        {{ domains }}
    </div>
</template>
