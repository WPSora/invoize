<script>
  import { PaymentStatus, InvoiceStatus as InvoiceStatusEnum, InvoiceListTab } from "$lib/common/enum";
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { isDebug, enablePaymentPage } from "$lib/stores/settings-store";
  import BasePreview from "$lib/components/template/BasePreview.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import ExpiredNotif from "$lib/components/preview/expired-notif.svelte";
  import NotFoundNotif from "$lib/components/preview/not-found-notif.svelte";
  import LoadingPreview from "$lib/components/preview/loading-preview.svelte";
  import PaymentStatusModal from "$lib/components/preview/payment-status-modal.svelte";
  import BasePreviewAction from "$lib/components/template/BasePreviewInvoiceAction.svelte";
  import HeaderTitle from "$lib/components/preview/header-title.svelte";
  import InvoiceDate from "$lib/components/preview/date.svelte";
  import toast from "svelte-french-toast";

  /**
   * this is to get params from route. see svelte-spa-router.
   * example:
   * http://localhost/wordpress/wp-admin/admin.php?page=invoize#/invoice/bfc4a752df8b1cf0cb7d192c25ffb497
   * params will get the last one (bfc4a752df8b1cf0cb7d192c25ffb497) which is defined in routes.js as invoizeToken
   */
  export let params = {};

  // this is to check which preview page (Invoice/Receipt);
  let page;
  let invoice;
  let token;
  let isPublic = false;
  let isExist = true;
  let isLoading = false;
  let isDialogOpen = false;
  let actionMessage; // action.label
  let actionMessageLowerCase;
  let actionStatus; // action.status || paid/unpaid/send/duplicate/cancelled/archived/trashed
  let selectedStatusType; // paymentStatus/invoiceStatus
  let publicLink = "";
  let isPaymentSuccess = false;
  let isInvoicePaymentStatusUpdated = false;
  let isPaymentStatusModalOpen = false;
  let isCapturingPayment = false;
  let isUpdatingPaymentStatus = false;

  $: nav =
    page === "invoice"
      ? [
          { name: "Invoice", link: "invoices" },
          { name: "Detail", link: "invoice/1" },
        ]
      : page === "receipt"
        ? [
            { name: "Receipt", link: "receipts" },
            { name: "Detail", link: "receipt/1" },
          ]
        : [];
  $: isPaid = invoice?.paymentStatus === "paid" ? true : false;

  const handleInvoiceTabUpdate = () => {
    const isRecurring = invoice?.recurring?.value ? true : false;
    let tab1, tab2;
    if (selectedStatusType === "paymentStatus") {
      // if paymentStatus, then we always updating the tab PAID and UNPAID
      tab1 = PaymentStatus.PAID;
      tab2 = PaymentStatus.UNPAID;
    } else {
      // if invoiceStatus, then we update other tabs.
      // This is the Current tab
      tab1 = invoice?.tab;
      // this is the tab we are switching to.
      tab2 =
        // if active means its either PAID or UNPAID,
        actionStatus === InvoiceStatusEnum.ACTIVE
          ? // so we use paymentStatus value, which is the tab we are updating.
            invoice?.paymentStatus
          : // if its duplicate, update the unpaid tab because duplicate is always unpaid
            actionStatus === InvoiceStatusEnum.DUPLICATE
            ? InvoiceListTab.UNPAID.LOWER_CASE
            : actionStatus;
    }
    $isDebug && console.log({ tab1, tab2 });
    triggerInvoiceTabUpdate(tab1, tab2, isRecurring);
  };

  const updateStatus = (e) => {
    if (isLoading) return;
    if (actionStatus === InvoiceStatusEnum.REGENERATE) {
      regenerateInvoice();
      return;
    }
    if (actionStatus === InvoiceStatusEnum.DUPLICATE) {
      duplicateInvoice();
      return;
    }
    if (actionStatus === InvoiceStatusEnum.SEND) {
      sendInvoice();
      return;
    }
    if (actionStatus === InvoiceStatusEnum.TO_RECURRING) {
      toRecurring(e.detail);
      return;
    }

    isLoading = true;
    const payload = { id: invoice?.id, [selectedStatusType]: actionStatus, email: e?.detail?.isSendEmail };
    toast.promise(createPostRequest(`invoice/update`, payload), {
      loading: "Updating...",
      success: () => {
        handleInvoiceTabUpdate();
        isLoading = false;
        isDialogOpen = false;
        // reset to null to display loading and prevent null error
        invoice = null;
        getInvoiceDetail();
        return "Status updated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to update", false);
      },
    });
  };

  const regenerateInvoice = () => {
    isLoading = true;
    toast.promise(createPostRequest(`invoice/regenerate`, { id: invoice.id }), {
      loading: "Regenerating invoice...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        triggerInvoiceTabUpdate(invoice.tab);
        getInvoiceDetail();
        return "Invoice regenerated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to regenerate invoice", false);
      },
    });
  };

  const duplicateInvoice = () => {
    isLoading = true;
    toast.promise(createPostRequest(`invoice/duplicate`, { id: invoice.id }), {
      loading: "Duplicating...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        handleInvoiceTabUpdate();
        return "Invoice duplicated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to duplicate invoice", false);
      },
    });
  };

  const sendInvoice = () => {
    isLoading = true;
    const payload = { id: invoice.id, status: invoice?.paymentStatus };
    toast.promise(createPostRequest(`invoice/send-mail`, payload), {
      loading: "Sending invoice...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        triggerInvoiceTabUpdate(invoice.tab);
        getInvoiceDetail();
        return "Invoice sent successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, err.message.response?.data?.message, false);
      },
    });
  };

  const toRecurring = (data) => {
    isLoading = true;
    const payload = {
      token,
      dueDateInterval: data.dueDateInterval,
      recurring: data.recurring,
    };
    toast.promise(createPostRequest(`invoice/to-recurring`, payload), {
      loading: "Updating invoice to recurring...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        triggerInvoiceTabUpdate(invoice.tab);
        getInvoiceDetail();
        return "Invoice successfully updated into recurring";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        // console.log(err.message);
        return handleError(err, "Failed to update invoice to recurring", false, true);
      },
    });
  };

  const getInvoiceDetail = async (callback) => {
    let api;
    if (isPublic) {
      api = `invoice/detail?invoizeToken=${token}`;
    } else {
      page === "invoice"
        ? (api = `invoice/detail?invoizeToken=${token}`)
        : page === "receipt"
          ? (api = `receipt/detail?invoizeToken=${token}`)
          : null;
    }
    if (!api) {
      toast.error("API not found");
      return;
    }
    try {
      const response = await createGetRequest(api);
      invoice = response.data;
      callback && callback();
      $isDebug && !isPublic && console.log(invoice);
    } catch (err) {
      isExist = false;
      handleError(err, "Failed to get invoice detail");
    }
  };

  const updateInvoicePaymentStatus = async () => {
    try {
      isUpdatingPaymentStatus = true;
      await createPostRequest("invoice/update", { id: invoice.id, paymentStatus: PaymentStatus.PAID });
      isInvoicePaymentStatusUpdated = true;
    } catch (err) {
      isInvoicePaymentStatusUpdated = false;
      handleError(err, "Failed to update invoice payment status");
    } finally {
      isPaymentStatusModalOpen = true;
      isUpdatingPaymentStatus = false;
    }
  };

  const setPublicLink = () => {
    publicLink = `${invoize.base_url}/invoize-preview/${token}`;
  };

  const checkIsPublic = () => {
    if (window.location.href.includes(invoize.base_url + "/invoize-preview")) {
      isPublic = true;
    }
  };

  // for NOT public preview
  const checkWhichPage = () => {
    const path = location.href.split("/");
    const current = path[path.length - 2];
    page = current === "invoice" ? "invoice" : current === "receipt" ? "receipt" : null;
    getInvoiceDetail();
  };

  const checkIsToCapturePayment = () => {
    const searchParams = new URLSearchParams(window.location.search);
    const paypalToken = searchParams.get("token");
    if (paypalToken && invoice.paymentStatus === PaymentStatus.UNPAID) {
      capturePayment(paypalToken);
    }
  };

  const capturePayment = async (paypalToken) => {
    // we don't need to check the status of response because if there's no error,
    // it always means the payment is successful. Failed payment in inside catch.
    try {
      isPaymentStatusModalOpen = true;
      isCapturingPayment = true;
      await createPostRequest("paypal/capture", { token: paypalToken, invoiceId: invoice.id });
      isPaymentSuccess = true;
      updateInvoicePaymentStatus();
    } catch (err) {
      isPaymentSuccess = false;
      handleError(err);
    } finally {
      isPaymentStatusModalOpen = true;
      isCapturingPayment = false;
    }
  };

  const checkIsReceiptPage = () => {
    const path = window.location.href.split("invoize#/")[1];
    if (path) {
      return path.split("/")[0] === "receipt";
    }
    return false;
  };

  const getToken = () => {
    if (isPublic) {
      const path = window.location.pathname;
      const segments = path.split("/");
      token = segments.pop() || segments.pop();
    } else {
      token = params?.invoizeToken;
    }
  };

  onMount(() => {
    checkIsPublic();
    getToken();
    setPublicLink();
    if (!isPublic) {
      checkWhichPage();
    } else {
      getInvoiceDetail(checkIsToCapturePayment);
    }

    // Get Required Setting in the preview
    createGetRequest("settings/get?key=payment.enablePaymentPage").then((res) => {
      $enablePaymentPage = res.data.value;
    });
  });

  $: invoicePreviewProps = {
    isPaid,
    invoice,
    publicLink,
    isRecurring: invoice?.recurring,
    note:
      page === "invoice" || isPublic
        ? invoice?.invoiceNote
        : page === "receipt"
          ? { terms: invoice?.receiptTerms, note: invoice?.invoiceNote?.note }
          : null,
  };

  $: invoicePreviewActionProps = {
    token,
    page,
    publicLink,
    isPublic,
    invoice,
    isLoading,
    isWoocommerce: invoice?.isWoocommerce ?? false,
  };
</script>

<!-- modal for capture payment -->
<PaymentStatusModal
  bind:isOpen="{isPaymentStatusModalOpen}"
  invoiceId="{invoice?.invoiceNumber}"
  {isPaymentSuccess}
  {isCapturingPayment}
  {isUpdatingPaymentStatus}
  {publicLink}
  {isInvoicePaymentStatusUpdated} />

{#if !isPublic}
  <div class="print:hidden">
    <Breadcrumb to="{nav}" />
  </div>
{/if}

<!-- Not found or Trashed state -->
{#if isPublic && (!isExist || invoice?.tab === InvoiceStatusEnum.TRASHED)}
  <NotFoundNotif />

  <!-- Expired or Cancelled state -->
{:else if isPublic && (invoice?.tab === InvoiceStatusEnum.EXPIRED || invoice?.tab === InvoiceStatusEnum.CANCELLED)}
  <ExpiredNotif tab="{invoice?.tab}" />

  <!-- Normal state -->
{:else}
  <div
    class="flex flex-wrap xl:flex-nowrap justify-center xl:justify-center gap-8 mt-8 print:gap-0"
    id="invoice-content-wrap">
    {#if !invoice}
      <LoadingPreview />
    {:else}
      <BasePreview {...invoicePreviewProps}>
        <svelte:fragment slot="header-title">
          <HeaderTitle {invoice} {page} {isPublic} />
        </svelte:fragment>
        <svelte:fragment slot="date">
          <InvoiceDate
            {isPaid}
            paidDate="{invoice.paidDate}"
            orderDate="{invoice.orderDate}"
            invoiceDate="{invoice.invoiceDate}"
            dueDate="{invoice.dueDate}" />
        </svelte:fragment>
      </BasePreview>
    {/if}

    <!-- Actions -->
    <BasePreviewAction
      {...invoicePreviewActionProps}
      on:updateStatus="{updateStatus}"
      bind:isDialogOpen
      bind:actionMessage
      bind:actionMessageLowerCase
      bind:actionStatus
      bind:selectedStatusType />
  </div>
{/if}
