import { InvoiceListTab, QuotationListTab, ReceiptListTab, RecurringListTab } from "$lib/common/enum";
import { writable } from "svelte/store";

export let invoiceListPage = writable({
  unpaid: 1,
  paid: 1,
  expired: 1,
  archived: 1,
  cancelled: 1,
  trashed: 1,
  all: 1,
  search: 1,
});
export let recurringClientListPage = writable(1);
export let recurringListPage = writable({
  active: 1,
  inactive: 1,
  invoice: 1,
  search: 1,
});
export let quotationListPage = writable({
  active: 1,
  archived: 1
});
export let receiptListPage = writable({
  paid: 1,
  cancelled: 1,
  archived: 1,
  all: 1,
  search: 1,
});
export let productListPage = writable(1);
export let customerListPage = writable(1)


/**
 * @param {number} pageNumber
 * @param {string} pageName
 * @param {null|string} tab
 */
export const handleActivePage = (pageNumber, pageName, tab = null) => {
  switch (pageName) {
    case 'invoice':
      switch (tab) {
        case InvoiceListTab.UNPAID.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, unpaid: pageNumber }))
          break;
        case InvoiceListTab.PAID.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, paid: pageNumber }))
          break;
        case InvoiceListTab.EXPIRED.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, expired: pageNumber }))
          break;
        case InvoiceListTab.ARCHIVED.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, archived: pageNumber }))
          break;
        case InvoiceListTab.CANCELLED.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, cancelled: pageNumber }))
          break;
        case InvoiceListTab.TRASHED.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, trashed: pageNumber }))
          break;
        case InvoiceListTab.ALL.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, all: pageNumber }))
          break;
        case InvoiceListTab.SEARCH.LOWER_CASE:
          invoiceListPage.update((val) => ({ ...val, search: pageNumber }))
          break;
        default:
          break;
      }
      break;
    case 'recurring':
      switch (tab) {
        case RecurringListTab.ACTIVE.LOWER_CASE:
          recurringListPage.update((val) => ({ ...val, active: pageNumber }));
          break;
        case RecurringListTab.INACTIVE.LOWER_CASE:
          recurringListPage.update((val) => ({ ...val, inactive: pageNumber }));
          break;
        case RecurringListTab.INVOICE.LOWER_CASE:
          recurringListPage.update((val) => ({ ...val, invoice: pageNumber }));
          break;
        case RecurringListTab.SEARCH.LOWER_CASE:
          recurringListPage.update((val) => ({ ...val, search: pageNumber }));
          break;
        default:
          break;
      }
      break;
    case 'recurring-client':
      recurringClientListPage.update((val) => (pageNumber));
      break;
    case 'quotation':
      switch (tab) {
        case QuotationListTab.ACTIVE.LOWER_CASE:
          quotationListPage.update((val) => ({ ...val, active: pageNumber }));
          break;
        case QuotationListTab.ARCHIVED.LOWER_CASE:
          quotationListPage.update((val) => ({ ...val, archived: pageNumber }));
          break;
        default:
          break;
      }
      break;
    case 'receipt':
      switch (tab) {
        case ReceiptListTab.PAID.LOWER_CASE:
          receiptListPage.update((val) => ({ ...val, paid: pageNumber }));
          break;
        case ReceiptListTab.CANCELLED.LOWER_CASE:
          receiptListPage.update((val) => ({ ...val, cancelled: pageNumber }));
          break;
        case ReceiptListTab.ARCHIVED.LOWER_CASE:
          receiptListPage.update((val) => ({ ...val, archived: pageNumber }));
          break;
        case ReceiptListTab.ALL.LOWER_CASE:
          receiptListPage.update((val) => ({ ...val, all: pageNumber }));
          break;
        default:
          break;
      }
      break;
    case 'customer':
      customerListPage.update((val) => pageNumber);
      break;
    case 'product':
      productListPage.update((val) => pageNumber);
      break;
    default:
      break;

  }
}