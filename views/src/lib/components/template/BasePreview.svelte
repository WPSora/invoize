<script>
  import { Card, CardContent, CardFooter, CardHeader } from "$lib/components/ui/card";
  import { quotationContent, invoiceContent, receiptContent } from "$lib/common/class-constant";
  import InvoiceStatus from "$lib/components/preview/status.svelte";
  import HeaderSeparator from "$lib/components/preview/header-separator.svelte";
  import Payment from "$lib/components/preview/payment.svelte";
  import Summary from "$lib/components/preview/summary.svelte";
  import Note from "$lib/components/preview/note.svelte";
  import BusinessLogo from "$lib/components/preview/business-logo.svelte";
  import Issuer from "$lib/components/preview/issuer.svelte";
  import Customer from "$lib/components/preview/customer.svelte";
  import Product from "$lib/components/preview/product.svelte";
  import BilledTo from "../preview/billed-to.svelte";

  export let isReceipt = false;
  export let isRecurring = false;
  export let isQuotation = false;
  export let publicLink = null;
  export let isPaid = null;
  export let invoice;
  export let note;

</script>

<Card class="sm:p-8 p-3 print:p-2 md:w-[800px] min-h-[900px] invoice-content-wrapper {isReceipt && 'hidden'} print:border-none print:shadow-none print:w-full">
  <!-- this ID used by html2pdf -->
  <div id={isReceipt ? receiptContent : (isQuotation ? quotationContent : invoiceContent)}>
    <CardHeader class="flex flex-row justify-between items-center p-0 mb-4">
      
      <!-- Left side Header -->
      <BusinessLogo logo={invoice.business.logo} />

      <!-- Right side Header -->
      <slot name="header-title" />
    </CardHeader>

    <HeaderSeparator />

    <CardContent class="sm:py-2 px-0 space-y-6">
      <!-- Content Top -->
      <div class="flex flex-nowrap justify-between">
        <!-- Content Top-Left -->
        <div class="w-1/2 space-y-6">
          <!-- Issued by / Business -->
          <Issuer business={invoice.business} />

          <!-- Customer -->
          <Customer client={invoice.client} hasBilledTo={!invoice.billedToSameAsClient && invoice?.billedTo}/>
          
          {#if !invoice?.billedToSameAsClient && invoice?.billedTo}
            <!-- Billed to -->
            <BilledTo billedTo={invoice.billedTo} />
          {/if}

        </div>

        <!-- Content Top-Right -->
        <div class="w-1/2 flex flex-col gap-y-8 items-end justify-center text-sm">
          <!-- Status & Type -->
           <slot name="status">
            <InvoiceStatus {isRecurring} {isPaid} paymentStatus={invoice.paymentStatus} />
           </slot>

          <!-- Date -->
          <slot name="date" />
        </div>
      </div>

      <!-- Content Middle -->
      <Product products={invoice.products} currency={invoice.currency} />

      <!-- Content Bottom -->
      <div class="flex flex-row flex-nowrap">
        <!-- Content Bottom-Left -->
        <Payment payments={invoice.payments} {isPaid} {isRecurring} {isQuotation} link={invoice.payment_link}/>

        <!-- Content Bottom-Right -->
        <Summary
          currency={invoice.currency}
          discounts={invoice.discount}
          taxes={invoice.tax}
          subtotal={invoice.subtotal}
          total={invoice.total} />
      </div>
    </CardContent>

    <!-- Note & TC -->
    <CardFooter class="block px-0 pb-0 mt-4 break-inside-avoid">
      <Note invoiceNote={note} link={publicLink} />
      {#if publicLink}
        <!-- for jsPDF when downloading, change font, because if default font, it will have weird spacing -->
        <div class="w-full text-gray-400 text-[0.65rem] italic text-right mt-6">
          {publicLink}
        </div>
      {/if}
      {#if !invoize.can_use_premium_code}
        <div class="text-[0.65rem] text-gray-400">
          Invoize by <a href="https://wpsora.com" target="_blank" class="underline font-bold">wpsora.com</a>
        </div>
      {/if}
    </CardFooter>
  </div>
</Card>

<style>
  @media print {
    

    :global(.has-billed-to) {
      display: none;
    }
  
    :global(body) {
      visibility: hidden !important;
      background-color: white !important;
      height: auto !important;
    }

    /* Page size and margin adjustments */
    @page {
      size: auto; /* No !important for Firefox compatibility */
      margin: 0.5cm;
    }

    /* Invoice-specific styles */
    :global(*) {
      -webkit-print-color-adjust: exact !important; /* WebKit-based browsers */
      print-color-adjust: exact !important; /* Firefox and others */
    }

    :global(#invoice-content-wrap) {
      box-shadow: none !important;
      margin: 0.5cm !important;
      padding: 0 !important;
    }


    :global(#invoice-content),
    :global(#quotation-content),
    :global(#receipt-content) {
      visibility: visible !important;
      width: 100%;
      height: 97vh;
      /* border: 1px solid black !important; */
    }

    /* Admin panel and toolbar removal */
    :global(#wpcontent) {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      margin: 0 !important;
      width: 100%;
    }

    :global(.wp-toolbar) {
      padding: 0 !important;
    }

    :global(#adminmenumain),
    :global(#wpadminbar),
    :global(#wpfooter),
    :global(.notice) {
      display: none !important;
    }

  }
</style>
