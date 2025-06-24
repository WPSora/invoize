import {
  InvoiceListTab,
  PaymentStatus,
  InvoiceStatus,
  InvoiceStatusAction,
  PaymentStatusAction,
  RecurringStatus,
  RecurringStatusAction
} from "$lib/common/enum";

/**
 * 
 * @param {string} tab 
 * @returns {Array}
 */
export const getInvoiceActions = (tab) => {
  switch (tab) {
    case InvoiceListTab.UNPAID.LOWER_CASE:
      return [
        {
          status: InvoiceStatus.EDIT,
          label: InvoiceStatusAction.EDIT,
        },
        {
          status: PaymentStatus.PAID,
          label: PaymentStatusAction.PAY,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        },
        {
          status: InvoiceStatus.SEND,
          label: InvoiceStatusAction.SEND,
        },
        {
          status: InvoiceStatus.ARCHIVED,
          label: InvoiceStatusAction.ARCHIVE,
        },
        {
          status: InvoiceStatus.CANCELLED,
          label: InvoiceStatusAction.CANCEL,
        },
        {
          status: InvoiceStatus.TRASHED,
          label: InvoiceStatusAction.TRASH,
        },
        {
          status: InvoiceStatus.REGENERATE,
          label: InvoiceStatusAction.REGENERATE,
        },
        {
          status: InvoiceStatus.TO_RECURRING,
          label: InvoiceStatusAction.TO_RECURRING,
        },
      ];
    case InvoiceListTab.PAID.LOWER_CASE:
      return [
        {
          status: PaymentStatus.UNPAID,
          label: PaymentStatusAction.UNPAY,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        },
        {
          status: InvoiceStatus.SEND,
          label: InvoiceStatusAction.SEND,
        },
        {
          status: InvoiceStatus.ARCHIVED,
          label: InvoiceStatusAction.ARCHIVE,
        },
        {
          status: InvoiceStatus.CANCELLED,
          label: InvoiceStatusAction.CANCEL,
        },
        {
          status: InvoiceStatus.TRASHED,
          label: InvoiceStatusAction.TRASH,
        },
        {
          status: InvoiceStatus.TO_RECURRING,
          label: InvoiceStatusAction.TO_RECURRING,
        },
      ];
    case InvoiceListTab.EXPIRED.LOWER_CASE:
      return [
        {
          status: InvoiceStatus.EDIT,
          label: InvoiceStatusAction.EDIT,
        },
        {
          status: PaymentStatus.PAID,
          label: PaymentStatusAction.PAY,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        },
        {
          status: InvoiceStatus.SEND,
          label: InvoiceStatusAction.SEND,
        },
        {
          status: InvoiceStatus.CANCELLED,
          label: InvoiceStatusAction.CANCEL,
        },
        {
          status: InvoiceStatus.TRASHED,
          label: InvoiceStatusAction.TRASH,
        },
      ];
    case InvoiceListTab.ARCHIVED.LOWER_CASE:
      return [
        {
          status: InvoiceStatus.EDIT,
          label: InvoiceStatusAction.EDIT,
        },
        {
          status: InvoiceStatus.ACTIVE,
          label: InvoiceStatusAction.UNARCHIVE,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        },
        {
          status: InvoiceStatus.SEND,
          label: InvoiceStatusAction.SEND,
        },
        {
          status: InvoiceStatus.CANCELLED,
          label: InvoiceStatusAction.CANCEL,
        },
        {
          status: InvoiceStatus.TRASHED,
          label: InvoiceStatusAction.TRASH,
        },
      ];
    case InvoiceListTab.CANCELLED.LOWER_CASE:
      return [
        {
          status: InvoiceStatus.ACTIVE,
          label: InvoiceStatusAction.UNCANCEL,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        },
        {
          status: InvoiceStatus.SEND,
          label: InvoiceStatusAction.SEND,
        },
        {
          status: InvoiceStatus.TRASHED,
          label: InvoiceStatusAction.TRASH,
        }
      ]
    case InvoiceListTab.TRASHED.LOWER_CASE:
      return [
        {
          status: InvoiceStatus.ACTIVE,
          label: InvoiceStatusAction.RESTORE,
        },
        {
          status: InvoiceStatus.DUPLICATE,
          label: InvoiceStatusAction.DUPLICATE,
        }
      ]
    default:
      return [];
  }
}

export const getRecurringAction = (status) => {
  switch (status) {
    case RecurringStatus.ACTIVE:
      return [
        {
          status: InvoiceStatus.EDIT,
          label: InvoiceStatusAction.EDIT,
        },
        {
          status: RecurringStatus.CREATE,
          label: RecurringStatusAction.CREATE,
        },
        {
          status: RecurringStatus.INACTIVE,
          label: RecurringStatusAction.INACTIVATE
        },
      ];
    case RecurringStatus.INACTIVE:
      return [
        {
          status: InvoiceStatus.EDIT,
          label: InvoiceStatusAction.EDIT,
        },
        {
          status: RecurringStatus.ACTIVE,
          label: RecurringStatusAction.ACTIVATE
        }
      ];
    default:
      return [];
  }
}

export const addEditAction = (action, paymentStatus) => {
  if (paymentStatus === PaymentStatus.PAID) {
    return action;
  }

  return [
    {
      status: InvoiceStatus.EDIT,
      label: InvoiceStatusAction.EDIT,
    },
    ...action,
  ]

}