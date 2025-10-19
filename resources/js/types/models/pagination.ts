import { Transaction } from '@/types/models/transaction';

export interface PaginationLinkModel {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
}

export interface PaginationMetaModel {
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLinkModel[];
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

export interface PaginatedEntriesModel {
    data: Transaction[], // later should be added other models with ||
    links: {
        first: string | null,
        last: string | null,
        prev: string | null,
        next: string | null,
    },
    meta: PaginationMetaModel,
}