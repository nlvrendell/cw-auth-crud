<script>
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: AuthLayout,
};
</script>
<script setup></script>
<template>
    <div class="shadow bg-white rounded-md p-6">
        <p>Name: {{ $page.props.auth.user.name }}</p>
        <p>Scope: {{ $page.props.auth.user.scope }}</p>
        <p>Territory: {{ $page.props.auth.user.territory }}</p>
        <p>Username: {{ $page.props.auth.user.username }}</p>
        <p>Domain: {{ $page.props.auth.user.domain }}</p>
    </div>
</template>
