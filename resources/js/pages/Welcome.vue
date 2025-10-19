<script setup lang="ts">
import { dashboard, login, logout, register } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { USER_ROLES } from '@/constants/appConstants';
import AdminHome from '@/pages/home/AdminHome.vue';
import AccountOwnerHome from '@/pages/home/AccountOwnerHome.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const user = computed(() => usePage().props.auth.user)
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <template v-if="user">
                    <Link
                        v-if="user.role === USER_ROLES.ADMIN"
                        :href="dashboard()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </Link>
                    <Link :href="logout()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Log Out
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div class="container">
            <div class="row">
                <div class="col-10">
                    <AdminHome v-if="user?.role === USER_ROLES.ADMIN"/>
                    <AccountOwnerHome v-else-if="user?.role === USER_ROLES.ACCOUNT_OWNER"
                                      :userId="user.id"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
