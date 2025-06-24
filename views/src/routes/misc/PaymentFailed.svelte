<script>
  import { Button } from "$lib/components/ui/button";
  import { Card, CardContent, CardFooter } from "$lib/components/ui/card";
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest } from "$lib/helpers/request";
  import { XCircle, Loader2, X } from "lucide-svelte";
  import { onMount } from "svelte";
  import { isDebug } from "$lib/stores/settings-store";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  let token;
  let invoice;
  let paymentMethod;

  const getInvoiceDetail = async () => {
    try {
      const response = await createGetRequest(`invoice/detail?invoizeToken=${token}`);
      invoice = response.data;
      $isDebug && console.log(invoice);
    } catch (err) {
      handleError(err, "Failed to get invoice detail");
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

  onMount(() => {
    checkPaymentMethod();
    const url = new URLSearchParams(window.location.search);
    token = url.get("invoizeToken");
    getInvoiceDetail();
  });
</script>

<div class="w-full h-screen bg-gradient-to-tr from-primary to-primary-600 flex justify-center items-center">
  <Card
    class="bg-gradient-to-tr from-slate-100 to-white flex flex-col justify-center items-center sm:w-1/2 w-10/12 h-1/2 relative">
    {#if !invoice}
      <Loader2 class="h-40 w-40 text-primary animate-spin" strokeWidth={1} />
    {:else}
      <XCircle class="h-40 w-40 text-destructive mb-2" />
      <CardContent class="flex flex-col justify-center items-center text-center">
        <div class="text-2xl font-bold">Payment Failed</div>
        <div class="">Your payment for <b>Invoice {invoice.invoiceNumber}</b> was not successful.</div>
        <div>Total Transaction: <b>{currencyFormatter(invoice?.currency?.name, invoice.total)}</b></div>
        <div>Payment Method: <b>{paymentMethod}</b></div>
        <div class="flex flex-nowrap items-center justify-center gap-2 mt-3">
          <X class="h-4 w-4 text-destructive" />
          <div class="text-sm">Invoice payment status was not updated</div>
        </div>
      </CardContent>
      <CardFooter>
        <Button on:click={goToInvoicePreviewPage}>Return to Invoice</Button>
      </CardFooter>
      <div class="absolute bottom-2 right-2">
        <img alt="Invoize Logo" src="logohere" />
      </div>
    {/if}
  </Card>
</div>
