<template>
    <Layout>
        <h2>Account balance: {{ account.balance }}</h2>
        <h3>Transactions of account: "{{ account.account_number }}":</h3>
        <div class="d-flex">
            <div class="mr-5 flex flex-1">
                <label for="search">Search description:</label>
                <input
                    type="text"
                    id="search"
                    class="form-control"
                    placeholder="Search description"
                    v-model="search"
                />
            </div>
            <button
                type="button"
                class="btn btn-primary"
                @click="handleSearch()"
            >
                Perform search
            </button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="col">#</th>
                    <th
                        class="col"
                        @click="
                            handleSort(
                                'created_at',
                                filters?.direction === 'asc' ? 'desc' : 'asc',
                            )
                        "
                    >
                        <span class="cursor-pointer text-blue-500">Date</span>
                    </th>
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
        <nav v-if="meta.last_page > 1 && links" aria-label="Page navigation">
            <ul class="pagination">
                <li v-for="link in links" class="page-item">
                    <button
                        :disabled="!link.url || link.active"
                        @click="link.url ? go(link.url) : null"
                        class="page-link"
                        v-html="link.label"
                    />
                </li>
            </ul>
        </nav>
        <div>
            <Link :href="`/account/${account.id}/summary`">Back to the summary</Link>
        </div>
    </Layout>
</template>

<script setup lang="ts">
import { PaginatedEntriesModel } from '@/types/models/pagination';
import { Account } from '@/types/models/account';
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Layout from '@/layouts/Layout.vue';

interface Filters {
    direction: 'asc' | 'desc';
    order_by: string;
    search: string;
    limit: number;
}

const props = defineProps<{
    account: Account;
    transactions: PaginatedEntriesModel;
    filters?: Filters;
}>();

const url = `/account/${props.account.id}/history`;
function handleSort(column: string, direction: string) {
    go(url, { order_by: column, direction: direction });
}

function handleSearch() {
    go(url, { search: search.value });
}

const defaultOptions = { preserveState: true, replace: true };
function go(url: string, data?: any, options?: any) {
    router.get(url, data, { ...defaultOptions, ...options });
}

const transactions = computed(() => props?.transactions?.data);
const meta = computed(() => props?.transactions?.meta);
const links = computed(() => meta.value?.links);

const search = ref('');
</script>

<style scoped></style>
