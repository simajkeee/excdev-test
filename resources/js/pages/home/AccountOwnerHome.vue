<template>
    <div>
        <h1>Transactions for user id: {{ userId }}</h1>
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

        <nav v-if="links" aria-label="Page navigation">
            <ul class="pagination">
                <li
                    v-for="(link, index) in links"
                    :key="index"
                    class="page-item"
                >
                    <button
                        :disabled="!link.url"
                        @click="link.url ? go(link.url) : null"
                        class="page-link"
                        v-html="link.label"
                    />
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { PaginatedEntriesModel } from '@/types/models/pagination';
import api from '@/services/api';

const props = withDefaults(defineProps<{
    userId: number;
    limit?: number
}>(), {
    limit: 5,
});

const paginatedTransactions = ref<PaginatedEntriesModel | null>(null);
const defaultOptions = { params: { limit: props.limit } };

onMounted(() => {
    go(`/users/${props.userId}/transactions`);
});

async function go(url: string, options?: any) {
    const { data } = await api.get(url, { ...defaultOptions, ...options});

    paginatedTransactions.value = data satisfies PaginatedEntriesModel;
}

const transactions = computed(() => paginatedTransactions.value?.data);
const meta = computed(() => paginatedTransactions.value?.meta);
const links = computed(() => meta.value?.links);
</script>