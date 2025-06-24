import { writable } from "svelte/store";

const defaultPagination = { page: 0, perPage: 0, totalItems: 0, totalPages: 0 };

export let searchQuery = writable("");

export let unpaidInvoicePagination = writable({ ...defaultPagination });
export let paidInvoicePagination = writable({ ...defaultPagination });
export let expiredInvoicePagination = writable({ ...defaultPagination });
export let archivedInvoicePagination = writable({ ...defaultPagination });
export let cancelledInvoicePagination = writable({ ...defaultPagination });
export let trashedInvoicePagination = writable({ ...defaultPagination });
export let allInvoicePagination = writable({ ...defaultPagination });
export let searchInvoicePagination = writable({ ...defaultPagination });

export let hasUnpaidData = writable(false);
export let unpaidTabData = writable([]);

export let hasPaidData = writable(false);
export let paidTabData = writable([]);

export let hasExpiredData = writable(false);
export let expiredTabData = writable([]);

export let hasArchivedData = writable(false);
export let archivedTabData = writable([]);

export let hasCancelledData = writable(false);
export let cancelledTabData = writable([]);

export let hasTrashedData = writable(false);
export let trashedTabData = writable([]);

export let hasAllData = writable(false);
export let allTabData = writable([]);

export let hasSearchData = writable(false);
export let searchTabData = writable([]);
