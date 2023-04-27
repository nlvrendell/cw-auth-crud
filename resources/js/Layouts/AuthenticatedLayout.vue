<script setup>
import { DownOutlined } from '@ant-design/icons-vue'
import { Link, router, usePage } from '@inertiajs/vue3'

import { computed } from 'vue'
import ApplicationLogo from '@/components/ApplicationLogo'
import NavLink from '@/components/NavLink'

const handleLogout = () => {
    router.post(route('logout'))
}

const navigations = computed(() => {
    if (_.find(usePage().props.auth.user.roles, { name: 'Coordinator' })) {
        return [{ name: 'Interview Seasons', route: 'coordinator.interview-seasons.index' }]
    } else if (_.find(usePage().props.auth.user.roles, { name: 'Evaluator' })) {
        return [{ name: 'Interview Seasons', route: 'dashboard' }]
    } else if (_.find(usePage().props.auth.user.roles, { name: 'Candidate' })) {
        return [{ name: 'Interview Seasons', route: 'dashboard' }]
    }

    return [
        { name: 'Dashboard', route: 'admin.dashboard' },
        { name: 'Schedules', route: 'admin.schedules' },
        { name: 'Coordinators', route: 'admin.coordinators' },
        { name: 'Users', route: 'admin.users.index' },
        { name: 'Feedbacks', route: 'admin.feedbacks' }
    ]
})

// impersonate
const handleImpersonate = url => {
    router.get(url)
}
</script>

<template>
    <a-layout>
        <a-layout-header style="background: #1a1a27" class="px-0">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                <div class="relative flex justify-between h-16">
                    <div class="flex-1 flex items-center sm:items-stretch justify-start">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo class="block h-9 w-auto fill-current" />
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
                                @click="handleImpersonate(route('impersonate.leave'))"
                            >
                                Stop Impersonating
                            </a-button>
                        </div>

                        <!-- Account dropdown -->
                        <a-dropdown placement="bottomRight" :trigger="['click']">
                            <span
                                class="cursor-pointer inline-flex items-center space-x-2 text-white"
                            >
                                <span class="font-semibold">{{ $page.props.auth.user.name }}</span>
                                <DownOutlined />
                            </span>

                            <template #overlay>
                                <a-menu style="min-width: 200px">
                                    <a-menu-item key="1">
                                        <Link :href="route('account-settings')">
                                            Account Settings
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
