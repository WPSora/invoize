import { PaymentStatus, InvoiceStatus } from "$lib/common/enum";
import { hasUnpaidData, hasPaidData, hasExpiredData, hasArchivedData, hasCancelledData, hasTrashedData, hasAllData } from "$lib/stores/invoice-list-store";

export const triggerInvoiceTabUpdate = (tab1, tab2, isRecurring) => {
  // from this tab
  switch (tab1) {
    case PaymentStatus.PAID:
      hasPaidData.set(false);
      hasAllData.set(false);
      break;
    case PaymentStatus.UNPAID:
      hasUnpaidData.set(false);
      hasAllData.set(false);
      break;
    case InvoiceStatus.EXPIRED:
      hasExpiredData.set(false);
      break;
    case InvoiceStatus.ARCHIVED:
      hasArchivedData.set(false);
      break;
    case InvoiceStatus.CANCELLED:
      hasCancelledData.set(false);
      break;
    case InvoiceStatus.TRASHED:
      hasTrashedData.set(false);
      break;
    default:
      break;
  }

  // update to this tab.
  // there's no expired tab here because we can only update from expired to other tabs,
  // not from other tabs to expired
  switch (tab2) {
    case PaymentStatus.PAID:
      hasPaidData.set(false);
      hasAllData.set(false);
      break;
    case PaymentStatus.UNPAID:
      hasUnpaidData.set(false);
      hasAllData.set(false);
      break;
    case InvoiceStatus.ARCHIVED:
      hasArchivedData.set(false);
      break;
    case InvoiceStatus.CANCELLED:
      hasCancelledData.set(false);
      break;
    case InvoiceStatus.TRASHED:
      hasTrashedData.set(false);
      break;
    default:
      break;
  }
}