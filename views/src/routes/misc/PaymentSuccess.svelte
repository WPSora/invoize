<script>
  // THIS IS FOR XENDIT PAYMENT SUCCESS PAGE
  import { Button } from "$lib/components/ui/button";
  import { Card, CardContent, CardFooter } from "$lib/components/ui/card";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { BadgeCheck, Loader2, Check, X, Hourglass } from "lucide-svelte";
  import { onMount } from "svelte";
  import { Status } from "$lib/common/xendit-enum";
  import { handleError } from "$lib/helpers/errorHelper";
  import { isDebug } from "$lib/stores/settings-store";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  let token;
  let invoice;
  let checkout;
  let paymentMethod;
  let isSuccessUpdateInvoiceStatus = false;
  let isPaymentPending = false;
  let isPaymentExpired = false;
  let isPaymentError = false;

  const getInvoiceDetail = async () => {
    try {
      const response = await createGetRequest(`invoice/detail?invoizeToken=${token}`);
      invoice = response.data;
      $isDebug && console.log(invoice);
    } catch (err) {
      handleError(err);
    }
  };

  const updateInvoicePaymentStatus = async () => {
    try {
      await createPostRequest(`invoice/update?token=${token}&paymentStatus=paid&email=true`);
      isSuccessUpdateInvoiceStatus = true;
    } catch (err) {
      isSuccessUpdateInvoiceStatus = false;
      handleError(err);
    } finally {
      await createPostRequest(`xendit/update?invoizeToken=${token}`);
      getInvoiceDetail();
    }
  };

  const goToInvoicePreviewPage = () => {
    window.location.href = `${invoize.base_url}/invoize-preview/${token}`;
  };

  const checkPaymentMethod = () => {
    const url = window.location.href;
    if (url.includes("xendit")) {
      paymentMethod = "Xendit";
    } else if (url.includes("paypal")) {
      paymentMethod = "Paypal";
    }
  };

  const checkIsPaidFromXendit = async () => {
    try {
      const res = await createGetRequest(`xendit/detail?invoizeToken=${token}`);
      checkout = res.data.data;
      const paymentStatus = res.data.paymentStatus;
      if (paymentStatus === Status.PAID) {
        isSuccessUpdateInvoiceStatus = true;
        getInvoiceDetail();
        return;
      }
      if (checkout.status === Status.PAID || checkout.status === Status.SETTLED) {
        updateInvoicePaymentStatus();
      } else if (checkout.status === Status.PENDING) {
        isPaymentPending = true;
      } else if (checkout.status === Status.EXPIRED) {
        isPaymentExpired = true;
      }
    } catch (err) {
      isPaymentError = true;
      handleError(err);
    }
  };

  onMount(() => {
    checkPaymentMethod();
    const url = new URLSearchParams(window.location.search);
    token = url.get("invoizeToken");
    checkIsPaidFromXendit();
  });
</script>

<div class="w-full h-screen bg-gradient-to-tr from-primary to-primary-600 flex justify-center items-center">
  <Card
    class="bg-gradient-to-tr from-slate-100 to-white flex flex-col justify-center items-center sm:w-1/2 w-10/12 h-1/2 relative">
    {#if !invoice && !isPaymentPending && !isPaymentExpired && !isPaymentError}
      <Loader2 class="h-40 w-40 text-primary animate-spin" strokeWidth="{1}" />
      <div class="text-center">Don't close this page while we process your payment...</div>
    {:else if isPaymentPending}
      <Hourglass class="h-40 w-40 text-yellow-500" />
      <div class="text-2xl font-bold mb-2">Waiting for Payment</div>
      <a href="{checkout.invoice_url}" target="_self">
        <Button>Pay now</Button>
      </a>
    {:else if isPaymentExpired}
      <X class="h-40 w-40 text-destructive" />
      <div class="text-2xl font-bold">Payment has Expired</div>
      <div>Please contact your merchant.</div>
    {:else if isPaymentError}
      <X class="h-40 w-40 text-destructive" />
      <div class="text-2xl font-bold">There's something wrong with this payment</div>
      <div>Please contact your merchant.</div>
    {:else}
      <BadgeCheck class="h-40 w-40 text-green-600 mb-2" />
      <CardContent class="flex flex-col justify-center items-center text-center">
        <div class="text-2xl font-bold">Payment Successful</div>
        <div>Your payment for <b>Invoice {invoice.invoiceNumber}</b> has been successfully accepted.</div>
        <div>Total Paid: <b>{currencyFormatter(invoice?.currency?.name, invoice.total)}</b></div>
        <div>Payment Method: <b>{paymentMethod}</b></div>
        {#if isSuccessUpdateInvoiceStatus}
          <div class="flex flex-nowrap items-center justify-center gap-2 mt-3">
            <Check class="h-4 w-4 text-green-600" />
            <div class="text-sm">Invoice payment status has been updated</div>
          </div>
        {:else}
          <div class="flex flex-nowrap items-center justify-center gap-2 mt-3">
            <X class="h-4 w-4 text-destructive" />
            <div class="text-sm">Failed to update your invoice payment status. Please contact your merchant.</div>
          </div>
        {/if}
      </CardContent>
      <CardFooter>
        <Button on:click="{goToInvoicePreviewPage}">Return to Invoice</Button>
      </CardFooter>
      <!-- <div class="absolute bottom-2 right-2"> -->
      <!-- <img alt="Invoize Logo" src="logohere" /> -->
      <!-- </div> -->
    {/if}
  </Card>
</div>
