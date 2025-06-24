import { InvoiceListTab, QuotationListTab, ReceiptListTab, RecurringListTab } from "$lib/common/enum"
import { writable } from "svelte/store"

export let invoiceTab = writable(InvoiceListTab.UNPAID.LOWER_CASE);
export let receiptTab = writable(ReceiptListTab.PAID.LOWER_CASE);
export let quotationTab = writable(QuotationListTab.ACTIVE.LOWER_CASE);
export let recurringTab = writable(RecurringListTab.ACTIVE.LOWER_CASE);