<script setup></script>
<template>
    <div class="flex justify-center text-lg underline">
        <div>
            Testing the tailwind
            {{ $page.props.auth.user }}
        </div>
        <a-button type="primary">Primary Button</a-button>
    </div>
</template>
