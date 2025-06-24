export const PaymentStatus = Object.freeze({
  PAID: "paid",
  UNPAID: "unpaid",
});

export const PaymentStatusAction = Object.freeze({
  PAY: "Mark as Paid",
  UNPAY: "Mark as Unpaid",
});

export const InvoiceStatus = Object.freeze({
  ACTIVE: "active",
  EXPIRED: "expired",
  ARCHIVED: "archived",
  CANCELLED: "cancelled",
  TRASHED: "trashed",
  DUPLICATE: 'duplicate',
  SEND: 'send',
  EDIT: 'edit',
  REGENERATE: 'regenerate',
  TO_RECURRING: 'to_recurring',
});

export const InvoiceStatusAction = Object.freeze({
  ARCHIVE: "Archive",
  CANCEL: "Cancel",
  TRASH: "Move to Trash",
  UNARCHIVE: "Unarchive",
  UNCANCEL: "Uncancel",
  RESTORE: "Restore",
  SEND: 'Send',
  DUPLICATE: 'Duplicate as Unpaid',
  EDIT: "Edit",
  REGENERATE: "Regenerate",
  TO_RECURRING: "Set as Recurring",
});

export const InvoiceType = Object.freeze({
  RECURRING: Object.freeze({
    LOWER_CASE: "recurring",
    PASCAL_CASE: "Recurring",
  }),
  ONE_TIME: Object.freeze({
    KEBAB_CASE: "one-time",
    PASCAL_KEBAB_CASE: "One-time",
  }),
});

export const InvoiceListTab = Object.freeze({
  UNPAID: {
    PASCAL_CASE: "Unpaid",
    LOWER_CASE: "unpaid",
  },
  PAID: {
    PASCAL_CASE: "Paid",
    LOWER_CASE: "paid",
  },
  EXPIRED: {
    PASCAL_CASE: "Expired",
    LOWER_CASE: "expired",
  },
  ARCHIVED: {
    PASCAL_CASE: "Archived",
    LOWER_CASE: "archived",
  },
  CANCELLED: {
    PASCAL_CASE: "Cancelled",
    LOWER_CASE: "cancelled",
  },
  TRASHED: {
    PASCAL_CASE: "Trashed",
    LOWER_CASE: "trashed",
  },
  ALL: {
    PASCAL_CASE: "All",
    LOWER_CASE: "all",
  },
  SEARCH: {
    PASCAL_CASE: "Search",
    LOWER_CASE: "search",
  },
});

export const RecurringStatus = Object.freeze({
  ACTIVE: "active",
  INACTIVE: "inactive",
  CREATE: "create",
  EDIT: "edit",
})

export const RecurringStatusAction = Object.freeze({
  ACTIVATE: "Activate",
  INACTIVATE: "Inactivate",
  CREATE: "Create Invoice",
})

export const RecurringListTab = Object.freeze({
  ACTIVE: {
    PASCAL_CASE: "Active",
    LOWER_CASE: "active",
  },
  INACTIVE: {
    PASCAL_CASE: "Inactive",
    LOWER_CASE: "inactive",
  },
  INVOICE: {
    PASCAL_CASE: "Invoice",
    LOWER_CASE: "invoice",
  },
  SEARCH: {
    PASCAL_CASE: "Search",
    LOWER_CASE: "search",
  },
})

export const QuotationListTab = Object.freeze({
  ACTIVE: {
    LOWER_CASE: "active",
    PASCAL_CASE: 'Active'
  },
  ARCHIVED: {
    LOWER_CASE: "archived",
    PASCAL_CASE: "Archived"
  }
})

export const ReceiptListTab = Object.freeze({
  PAID: {
    LOWER_CASE: "paid",
    PASCAL_CASE: "Paid",
  },
  CANCELLED: {
    LOWER_CASE: "cancelled",
    PASCAL_CASE: "Cancelled",
  },
  ARCHIVED: {
    LOWER_CASE: "archived",
    PASCAL_CASE: "Archived",
  },
  ALL: {
    LOWER_CASE: "all",
    PASCAL_CASE: "All",
  },
  SEARCH: {
    LOWER_CASE: "search",
    PASCAL_CASE: "Search",
  }
});

export const SettingsTab1 = Object.freeze({
  BUSINESS: 'business',
  INVOICE: Object.freeze({
    VALUE: 'invoice',
    TAB2: Object.freeze({
      GENERAL: 'general',
      CURRENCY: 'currency',
      TAX: 'tax',
      DISCOUNT: 'discount',
      REMINDER: 'reminder',
    }),
  }),
  RECEIPT: 'receipt',
  EMAIL: Object.freeze({
    VALUE: 'email',
    TAB2: Object.freeze({
      SMTP: 'smtp',
      TEMPLATES: 'templates',
    }),
  }),
  PAYMENT: Object.freeze({
    VALUE: 'payment',
    TAB2: Object.freeze({
      DEFAULT: 'default',
      PAYMENT_METHOD: Object.freeze({
        VALUE: 'payment-method',
        TAB3: Object.freeze({
          BANK: 'bank',
          CREDIT_CARD: 'credit-card',
          PAYPAL: 'paypal',
          XENDIT: 'xendit',
        }),
      }),
    }),
  }),
  CUSTOMER: 'customer',
  PRODUCT: 'product',
});

export const PaypalStatus = Object.freeze({
  COMPLETED: 'COMPLETED',
});

export const PaypalType = Object.freeze({
  AUTO: "auto confirmation",
  DIRECT: "direct payment",
});

export const PaypalTypeLabel = Object.freeze({
  AUTO: "Auto Confirmation",
  DIRECT: "Direct Payment",
});

export const PaymentMethod = Object.freeze({
  BANK: "bank",
  PAYPAL_DIRECT: "paypal-direct",
  PAYPAL_AUTO_CONFIRMATION: "paypal-auto-confirmation",
  PAYPAL: "paypal",
  XENDIT: "xendit",
  WOOCOMMERCE: "woocommerce transaction",
  WOOCOMMERCE_PAYPAL: 'woocommerce paypal',
  WOOCOMMERCE_BANK: 'woocommerce bank',
})