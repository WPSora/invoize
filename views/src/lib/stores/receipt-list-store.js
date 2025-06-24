import { writable } from "svelte/store";

const defaultPagination = { page: 0, perPage: 0, totalItems: 0, totalPages: 0 };

export let searchQuery = writable("");

export let paidReceiptPagination = writable({ ...defaultPagination });
export let archivedReceiptPagination = writable({ ...defaultPagination });
export let cancelledReceiptPagination = writable({ ...defaultPagination });
export let allReceiptPagination = writable({ ...defaultPagination });
export let searchReceiptPagination = writable({ ...defaultPagination });

export let hasPaidData = writable(false);
export let paidTabData = writable([]);

export let hasArchivedData = writable(false);
export let archivedTabData = writable([]);

export let hasCancelledData = writable(false);
export let cancelledTabData = writable([]);

export let hasAllData = writable(false);
export let allTabData = writable([]);

export let hasSearchData = writable(false);
export let searchTabData = writable([]);
