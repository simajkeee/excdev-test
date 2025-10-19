<script setup lang="ts">
import { USER_ROLES } from '@/constants/appConstants';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AccountList from '@/components/account/AccountList.vue';
import Layout from '@/layouts/Layout.vue';


const user = computed(() => usePage().props.auth.user);
const isAccountOwner = computed(
    () => user.value?.role === USER_ROLES.ACCOUNT_OWNER,
);
</script>

<template>
    <Layout>
        <div class="col-10">
            <div v-if="isAccountOwner">
                <h2>User id: {{ user.id }}; User name: {{ user.name }}</h2>

                <AccountList :userId="user.id" />
            </div>
        </div>
    </Layout>
</template>
