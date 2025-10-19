<template>
    <h3>User accounts:</h3>
    <div v-if="userAccounts.length">
        <table class="table">
            <thead>
            <tr>
                <th class="col">#</th>
                <th class="col">Created at</th>
                <th class="col">Account Number</th>
                <th class="col">Balance</th>
                <th class="col">History link</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(acc, index) in userAccounts" :key="acc.id">
                <th scope="row">{{ index }}</th>
                <td>{{ acc.created_at }}</td>
                <td>{{ acc.account_number }}</td>
                <td>{{ acc.balance }}</td>
                <td>
                    <Link :href="`/account/${acc.id}/summary`" class="text-blue-500">Show summary</Link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div v-else>No accounts found.</div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import api from '@/services/api';
import { Link } from '@inertiajs/vue3'
import { Account } from '@/types/models/account';

const props = defineProps<{
    userId: number;
}>();

const userAccounts = ref([] as Account[]);

onMounted(async () => {
    const { data: accounts } = await api.get(`/accounts/${props.userId}`);

    userAccounts.value = accounts.data;
});
</script>

<style scoped></style>
