<template>
    <Layout>
        <h2>Account balance: {{ account.balance }}</h2>
        <h3>Transactions of account: "{{ account.account_number }}":</h3>
        <template v-if="transactions.length">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col">#</th>
                        <th class="col">Date</th>
                        <th class="col">Description</th>
                        <th class="col">Amount</th>
                    </tr>
                </thead>
                <tbody v-if="transactions">
                    <tr v-for="(tx, index) in transactions" :key="tx.id">
                        <th scope="row">{{ index }}</th>
                        <td>{{ tx.created_at }}</td>
                        <td>{{ tx.description }}</td>
                        <td>{{ tx.amount }}</td>
                    </tr>
                </tbody>
            </table>
            <div>
                <Link :href="`/account/${account.id}/history`">Show account history</Link>
            </div>
        </template>
        <template v-else class="mb-2">
            No transactions yet.
        </template>
        <Link href="/">Back to the account list</Link>
    </Layout>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import Layout from '@/layouts/Layout.vue';
import { Account } from '@/types/models/account';
import { Link, router } from '@inertiajs/vue3';
import { Transaction } from '@/types/models/transaction';

const props = defineProps<{
    userId: number;
    account: Account;
    transactions: Transaction[];
}>();

let timer: number | undefined;

async function fetchTransactions() {
    router.get(`/account/${props.account.id}/summary`);
}

onMounted(() => {
    timer = window.setInterval(fetchTransactions, 5000);
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>