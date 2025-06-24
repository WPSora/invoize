// @ts-nocheck

import CircleLoading from "./routes/misc/CircleLoading.svelte";
import SkeletonLoading from "./routes/misc/SkeletonLoading.svelte";
import { wrap } from "svelte-spa-router/wrap";

export default {
  "/": wrap({
    asyncComponent: () => import("./routes/dashboard/Dashboard.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/setting": wrap({
    asyncComponent: () => import("./routes/setting/Setting.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/invoices": wrap({
    asyncComponent: () => import("./routes/invoice/InvoiceList.svelte"),
    loadingComponent: SkeletonLoading,
  }),
  "/quotations": wrap({
    asyncComponent: () => import("./routes/quotation/QuotationList.svelte"),
    loadingComponent: SkeletonLoading,
  }),
  "/quotation/create": wrap({
    asyncComponent: () => import("./routes/quotation/CreateQuotation.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/quotation/:token": wrap({
    asyncComponent: () => import("./routes/quotation/PreviewQuotation.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/invoice/create": wrap({
    asyncComponent: () =>
      import("./routes/invoice/create/CreateInvoice.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/invoice/edit/:invoizeToken": wrap({
    asyncComponent: () => import("./routes/invoice/edit/EditInvoice.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/invoice/:invoizeToken": wrap({
    asyncComponent: () => import("./routes/invoice/preview/Preview.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/recurrings": wrap({
    asyncComponent: () =>
      import("./routes/recurring/RecurringClientList.svelte"),
    loadingComponent: SkeletonLoading,
  }),
  "/recurring/create": wrap({
    asyncComponent: () =>
      import("./routes/recurring/create/CreateRecurring.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/recurring/:clientId/create": wrap({
    asyncComponent: () =>
      import("./routes/recurring/create/CreateRecurring.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/recurring/edit/:invoizeToken": wrap({
    asyncComponent: () =>
      import("./routes/recurring/edit/EditRecurring.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/recurring/:clientId": wrap({
    asyncComponent: () =>
      import("./routes/recurring/detail/RecurringList.svelte"),
    loadingComponent: SkeletonLoading,
  }),
  "/recurring/:clientId/:invoizeToken": wrap({
    asyncComponent: () =>
      import("./routes/recurring/detail/preview/RecurringPreview.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/receipts": wrap({
    asyncComponent: () => import("./routes/receipt/ReceiptList.svelte"),
    loadingComponent: SkeletonLoading,
  }),
  "/receipt/:invoizeToken": wrap({
    asyncComponent: () => import("./routes/invoice/preview/Preview.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/customers": wrap({
    asyncComponent: () => import("./routes/customer/client.svelte"),
    loadingComponent: CircleLoading,
  }),
  "/products": wrap({
    asyncComponent: () => import("./routes/product/product.svelte"),
    loadingComponent: CircleLoading,
  }),
  "*": wrap({
    asyncComponent: () => import("./routes/misc/NotFound.svelte"),
    loadingComponent: CircleLoading,
  }),
};
