import { writable } from 'svelte/store';

const defaultPagination = { page: 0, perPage: 0, totalItems: 0, totalPages: 0 };

/**
 * @typedef {Object} Selected
 * @property {string} label
 * @property {string | number} value
 */
/**
 * @type {Object}
 * @property {string} name
 * @property {Selected} interval
 * @property {string} start_date
 * @property {Selected} end
 */
export let recurring = writable({
  name: null,
  interval: { value: null, label: null },
  start_date: null,
  end: null,
});
export let isCreatingNewRecurring = writable(false);

export let activeTabPagination = writable({ ...defaultPagination });
export let inactiveTabPagination = writable({ ...defaultPagination });
export let invoiceTabPagination = writable({ ...defaultPagination });
export let searchTabPagination = writable({ ...defaultPagination });

export let activeTabData = writable([]);
export let inactiveTabData = writable([]);
export let invoiceTabData = writable([]);
export let searchTabData = writable([]);

export let hasActiveTabData = writable(false);
export let hasInactiveTabData = writable(false);
export let hasInvoiceTabData = writable(false);
export let hasSearchTabData = writable(false);

export let searchQuery = writable('');