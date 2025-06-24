import { writable } from "svelte/store";

const defaultPagination = { page: 0, perPage: 0, totalItems: 0, totalPages: 0 };

export let searchQuery = writable("");

export let activeQuotationPagination = writable({ ...defaultPagination });
export let archiveQuotationPagination = writable({ ...defaultPagination });
export let searchInvoicePagination = writable({ ...defaultPagination });

export let hasActiveData = writable(false);
export let activeTabData = writable([]);

export let hasArchiveData = writable(false);
export let archiveTabData = writable([]);

export let hasSearchData = writable(false);
export let searchTabData = writable([]);
