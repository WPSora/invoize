import { readable, writable } from "svelte/store";

/**
 * @typedef {Object} Selected
 * @property {string} label
 * @property {Object} value
 * @property {boolean} [disabled]
 */

/**
 * @typedef {Object} Product
 * @property {number} id
 * @property {string} name
 * @property {string} price
 * @property {string} description
 */

/**
 * @typedef {Object} CreatedProduct
 * @property {number} id
 * @property {string} name
 * @property {string | null} description
 * @property {number | null} unitPrice
 * @property {number | null} quantity
 * @property {number | null} amount
 * @property {string | null} note
 */

/**
 * @typedef {Object} Client
 * @property {number} id
 * @property {string} name
 * @property {string} email
 * @property {string} phoneNumber
 * @property {string} address
 * @property {string} website
 * @property {string} zip
 * @property {boolean} preview_access
 * @property {string} [created_at]
 */

/**
 * @typedef {Object} Currency
 * @property {string} name
 * @property {string} symbol
 */

/**
 * @typedef {Object} TaxAndDiscount
 * @property {string} name
 * @property {string} type
 * @property {number} value
 * @property {string} description
 * @property {Currency} currency
 */

/**
 * @typedef {Object} WcClient
 * @property {number} id
 * @property {string} string
 * @property {boolean} isWcClient
 * @property {string | null} phoneNumber
 * @property {string | null} email
 * @property {string} address
 * @property {string | null} customAddress
 */

/**
 * @typedef {Object} BilledTo
 * @property {string} name
 * @property {string} detail
 */

/**
 * for invoice list to fetch new data when user create new invoice
 * set to true to update tab data
 * @type {import('svelte/store').Writable<boolean>}
 */
export let isCreatingNewInvoice = writable(false);

/**
 * @type {import('svelte/store').Writable<string>}
 * only used by edit invoice
 */
export let quotationNumber = writable("");

/**
 * all business list, not just the visible ones in settings
 * @type {import('svelte/store').Writable<Array>}
 */
export let businesses = writable([]);

/** @type {import('svelte/store').Writable<Object>}  */
export let selectedBusiness = writable({
  id: 0,
  business_name: "",
  address: "",
  email: "",
  logo: null,
  phone_number: "",
  web: "",
  zip: "",
});

/** @type {import('svelte/store').Writable<Selected>}  */
export let selectedBusinessInput = writable({ label: null, value: null });

/** @type {import('svelte/store').Writable<Array<Client>>} */
export let clients = writable([]);

/**
 * for new client form
 * @type {import('svelte/store').Writable<Client>}
 */
export let client = writable({
  id: 0,
  name: "",
  email: "",
  phoneNumber: "",
  address: "",
  zip: "",
  website: "",
  preview_access: false,
});

/** @type {import('svelte/store').Readable<Client>} */
export let defaultClient = readable({
  id: 0,
  name: "",
  email: "",
  phoneNumber: "",
  address: "",
  zip: "",
  website: "",
  preview_access: false,
});

/** @type {import('svelte/store').Writable<Array<WcClient>>} */
export let wcClients = writable([]);

export let searchClientsResult = writable([]);
export let searchWcClientsResult = writable([]);

export let selectedClient = writable({
  id: 0,
  name: "",
  created_at: "",
  email: "",
  phoneNumber: "",
  address: "",
  zip: "",
  website: "",
  customAddress: "",
  isWcClient: false,
});
// update this to update the selected customer after newly created
export let selectedClientBind = writable({});
export let dueDateRecurringCustom = writable(null);
// to show custom input & to add the date
export let selectedDueDateInterval = writable(null);

/**
 * list of products payload
 * @type {import('svelte/store').Writable<Array<CreatedProduct>>}
 */
export let createdProductList = writable([]);

/**
 * after product is selected and user able to edit price
 * @type {import('svelte/store').Writable<CreatedProduct>}
 */
export let createdProduct = writable({
  id: 0,
  name: "",
  description: "",
  unitPrice: null,
  quantity: null,
  amount: null,
  note: null,
});

/** @type {import('svelte/store').Writable<Product>} */
export let product = writable({
  id: 0,
  name: "",
  price: "",
  description: "",
});

/**
 * update this to update the selected product after newly created
 * @type {import('svelte/store').Writable<Selected>}
 */
export let selectedProductSelectBind = writable(null);

/** @type {import('svelte/store').Writable<Array<Product>>} */
export let products = writable([]);

/** @type {import('svelte/store').Writable<Array<Product>>} */
export let wcProducts = writable([]);

/** @type {import('svelte/store').Writable<Array<Product>>} */
export let searchProductsResult = writable([]);

/** @type {import('svelte/store').Writable<Array<Product>>} */
export let searchWcProductsResult = writable([]);

/** @type {import('svelte/store').Writable<Currency>} */
export let currencyPayload = writable({ name: "none", symbol: null });

/** @type {import('svelte/store').Writable<Selected>} */
export let selectedBank = writable(null);

/**
 * @typedef {Object} PaypalSelect
 * @property {string} name
 * @property {string} type
 */
/** @type {import('svelte/store').Writable<PaypalSelect>} */
export let selectedPaypal = writable(null);

/** @type {import('svelte/store').Writable<Selected>} */
export let selectedPaypalBind = writable({ label: null, value: null });

export let wcPayment = writable(null);

/** @type {import('svelte/store').Writable<boolean>} */
export let isBankPaymentChecked = writable(false);

/** @type {import('svelte/store').Writable<boolean>} */
export let isPaypalChecked = writable(false);

/** @type {import('svelte/store').Writable<boolean>} */
export let isXenditChecked = writable(false);

/** @type {import('svelte/store').Writable<boolean>} */
export let isWcPaymentChecked = writable(false);

/** @type {import('svelte/store').Writable<Array<string>>} */
export let xenditCurrencies = writable([]);

/** @type {import('svelte/store').Writable<number>} */
export let totalXendit = writable(0);

/** @type {import('svelte/store').Writable<number>} */
export let total = writable(0);

/** @type {import('svelte/store').Writable<number>} */
export let subtotal = writable(0);

/** @type {import('svelte/store').Writable<number>} */
export let totalDiscount = writable(0);

/** @type {import('svelte/store').Writable<Array<TaxAndDiscount>>} */
export let selectedDiscount = writable([]);

/** @type {import('svelte/store').Writable<Array<boolean>>} */
export let checkedDiscount = writable([]);

/** @type {import('svelte/store').Writable<number>} */
export let totalTax = writable(0);

/** @type {import('svelte/store').Writable<Array<TaxAndDiscount>>} */
export let selectedTax = writable([]);

/** @type {import('svelte/store').Writable<Array<boolean>>} */
export let checkedTax = writable([]);

/** @type {import('svelte/store').Writable<string>} */
export let note = writable("");

/** @type {import('svelte/store').Writable<string>} */
export let internalNote = writable("");

/** @type {import('svelte/store').Writable<string>} */
export let termsAndConditionsInvoice = writable("");

/** @type {import('svelte/store').Writable<Array<string> | null>} */
export let selectedReminderBefore = writable([]);

/** @type {import('svelte/store').Writable<Array<string> | null>} */
export let selectedReminderAfter = writable([]);

/** @type {import('svelte/store').Writable<BilledTo>} */
export let billedTo = writable({ name: null, detail: null });

/** @type {import('svelte/store').Writable<boolean>} */
export let billedToSameAsClient = writable(true);
