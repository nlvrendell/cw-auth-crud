<script>
import AuthLayout from "@/Layouts/GuestLayout.vue";
export default {
    layout: AuthLayout,
};
</script>
<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    username: "",
    password: "",
});

const submit = () => {
    form.post(route("login.store"), {
        onFinish: () => form.reset("password"),
    });
};
</script>
<template>
    <div>
        <p class="text-lg">Login</p>
        <a-form layout="vertical" class="mt-5">
            <a-form-item
                name="username"
                label="Username"
                :validateStatus="form.errors.username ? 'error' : null"
                :help="form.errors.username"
            >
                <a-input
                    type="text"
                    name="username"
                    required
                    v-model:value="form.username"
                />
            </a-form-item>
            <a-form-item
                name="password"
                label="Password"
                :validateStatus="form.errors.password ? 'error' : null"
                :help="form.errors.password"
            >
                <a-input
                    type="password"
                    name="password"
                    required
                    v-model:value="form.password"
                />
            </a-form-item>
            <a-button type="primary" @click="submit" :loading="form.processing"> Login </a-button>
        </a-form>
    </div>
</template>
