<script>
  import { PaymentStatus, InvoiceStatus } from "$lib/common/enum";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let isPublic;
  export let paymentStatus;
  export let invoiceStatus;

  $: capitalPaymentStatus = capitalizeFirstLetter(paymentStatus);
  $: capitalInvoiceStatus = capitalizeFirstLetter(invoiceStatus);
</script>

<div class="flex flex-col justify-center items-center">
  <div class="font-medium">Status:</div>
  {#if isPublic}
    <div
      class="text-white font-bold px-2 rounded-md {paymentStatus === PaymentStatus.PAID
        ? 'bg-green-600'
        : 'bg-destructive'}">
      {capitalPaymentStatus}
    </div>
  {:else}
    <div
      class="text-white font-bold px-2 rounded-md {invoiceStatus === InvoiceStatus.ACTIVE
        ? 'bg-green-600'
        : invoiceStatus === InvoiceStatus.ARCHIVED
          ? 'bg-amber-400'
          : 'bg-destructive'}">
      {capitalInvoiceStatus}
    </div>
  {/if}
</div>
