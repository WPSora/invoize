import { writable } from "svelte/store";

export const dummyReminder = ["1 day", "3 day", "7 day"];

export const dummyRecurring = ["Monthly", "Half-yearly", "Yearly"];

export const dummyDueDateList = [
  { id: 3, name: "After 3 days" },
  { id: 7, name: "After 7 days" },
  { id: 30, name: "After 30 days" },
];

export let dummyBusiness = writable([
  {
    id: 1,
    name: "Dhani",
    address: "Jl. Sana sini No.99",
    city: "Banjarmasin",
    country: "Indonesia",
    postcode: 70248,
    website: "dhaniweb",
    email: "dhani.a.mm@gmail.com",
    taxNumber: "2415.25235-353.354",
    customAddress: null,
  },
  {
    id: 2,
    name: "Dhani 2",
    address: "Jl. Mana ya No.55",
    city: "Jakarta",
    country: "America",
    postcode: 70999,
    website: "webnew",
    email: "dhani.aa.mm@gmail.com",
    taxNumber: "9898.89898-998.888",
    customAddress: null,
  },
]);


export let dummyApiProductList = writable([
  {
    id: 1,
    name: "Computer with a very long name and description here and here and here, Computer with a very long name and description here and here and here, Computer with a very long name and description here and here and here",
    unitPrice: 2000,
    description: null,
  },
  {
    id: 2,
    name: "Monitor",
    unitPrice: 500,
    description:
      "Brand new Samsung Ultrawide 4k monitor, Brand new Samsung Ultrawide 4k monitor, Brand new Samsung Ultrawide 4k monitor, Brand new Samsung Ultrawide 4k monitor, Brand new Samsung Ultrawide 4k monitor",
  },
]);

export const dummyApiBankList = [
  {
    value: 1,
    name: "Bank BCA",
    accountHolder: "Dhani",
    accountNumber: 989898989,
  },
  {
    value: 2,
    name: "Bank Mandiri",
    accountHolder: "Dhani 2",
    accountNumber: 1221221222,
  },
];

export const dummyApiCreditCardList = [
  {
    value: 1,
    name: "Midtrans",
    clientId: 132,
    secretId: 989898989,
  },
  {
    value: 2,
    name: "Stripe",
    clientId: 124,
    secretId: 1221221222,
  },
];

export const dummyApiPaypalList = [
  {
    value: 1,
    name: "Automatic payment",
    link: "Payment link will be generated upon finish",
  },
  { value: 2, name: "Direct payment", link: "www.paypal.me/Dhani" },
];

export const dummyPayload = {
  business: {
    name: "Business 1",
    id: 1,
    address: "Jl. Sana sini No.99",
  },
  client: {
    name: "Client 1",
    id: 2,
    address: "Jl. Situ Sana no.88",
  },
  currency: {
    name: "USD",
    symbol: "&#36;",
  },
  status: "unpaid",
  orderDate: "2024-03-10",
  invoiceDate: "2024-03-10",
  dueDate: "2024-03-17",
  products: [
    {
      amount: 10000,
      description: "new pc",
      id: 1,
      name: "PC",
      quantity: 2,
      unitPrice: 5000,
    },
  ],
  payments: [
    {
      name: "Bank BCA",
      accountHolder: "Dhani",
      accountNumber: 989898989,
    },
  ],
  total: 10000,
  terms: "Payment is due within 15 days",
  recurring: undefined,
  note: "Additional note here",
};