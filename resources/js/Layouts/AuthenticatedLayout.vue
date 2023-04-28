<script setup>
import { DownOutlined } from "@ant-design/icons-vue";
import { Link, router, usePage } from "@inertiajs/vue3";

import { computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";

const handleLogout = () => {
    router.get(route("login"));
};

const navigations = computed(() => {
    return [
        { name: "Domain", route: "dashboard" },
        { name: "Users", route: "users" },
    ];
});

// impersonate
const handleImpersonate = (url) => {
    router.get(url);
};
</script>

<template>
    <a-layout>
        <a-layout-header style="background: gray" class="px-0">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                <div class="relative flex justify-between h-16">
                    <div
                        class="flex-1 flex items-center sm:items-stretch justify-start"
                    >
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo
                                    class="block h-9 w-auto fill-current"
                                />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
                            <NavLink
                                v-for="nav in navigations"
                                :key="nav.route"
                                :href="route(nav.route)"
                                :active="route().current(nav.route)"
                            >
                                {{ nav.name }}
                            </NavLink>
                        </div>
                    </div>
                    <div class="flex justify-end items-center space-x-4">
                        <div>
                            <a-button
                                v-if="$page.props.impersonator"
                                type="primary"
                                danger
                            >
                                Stop Impersonating
                            </a-button>
                        </div>

                        <!-- Account dropdown -->
                        <a-dropdown
                            placement="bottomRight"
                            :trigger="['click']"
                        >
                            <span
                                class="cursor-pointer inline-flex items-center space-x-2 text-white"
                            >
                                <span class="font-semibold">{{
                                    $page.props.auth.user.name
                                }}</span>
                                <DownOutlined />
                            </span>

                            <template #overlay>
                                <a-menu style="min-width: 200px">
                                    <a-menu-item key="1">
                                        <Link :href="route('profile')">
                                            Account Profile
                                        </Link>
                                    </a-menu-item>
                                    <a-menu-item key="2" @click="handleLogout">
                                        Signout
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                    </div>
                </div>
            </div>
        </a-layout-header>
        <a-layout-content class="min-h-screen">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                <div class="mt-4">
                    <slot />
                </div>
            </div>
        </a-layout-content>
        <a-layout-footer>
            <p class="text-center">2023© CandidateRank</p>
        </a-layout-footer>
    </a-layout>
</template>
