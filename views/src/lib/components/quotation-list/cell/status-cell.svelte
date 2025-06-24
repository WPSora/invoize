<script>
  import { Check, Clock } from "radix-icons-svelte";
  export let paymentStatus;
  export let invoiceStatus;

  let displayPaymentStatus = paymentStatus.toUpperCase();
  let displayInvoiceStatus = invoiceStatus.toUpperCase();
  const badgeStyle = "flex justify-center items-center text-[10px] w-24 rounded-md text-[13px] px-4 my-0.5";
</script>

<div class="flex flex-col items-center">
  {#if paymentStatus === "paid"}
    <div class="{badgeStyle} text-green-500 font-medium">
      <Check />
      <span class="ml-1 text-xs">{displayPaymentStatus}</span>
    </div>
  {:else}
    <div class="{badgeStyle} text-red-500 font-medium">
      <Clock />
      <span class="ml-1 text-xs">{displayPaymentStatus}</span>
    </div>
  {/if}

  <!-- {#if invoiceStatus === "active"}
    <div class="{badgeStyle} bg-teal-100">
      {displayInvoiceStatus}
    </div>
  {:else if invoiceStatus === "archived"}
    <div class="{badgeStyle} bg-yellow-100">
      {displayInvoiceStatus}
    </div>
  {:else if invoiceStatus === "cancelled"}
    <div class="{badgeStyle} bg-gray-200">
      {displayInvoiceStatus}
    </div>
  {:else if invoiceStatus === "trashed"}
    <div class="{badgeStyle} bg-orange-100">
      {displayInvoiceStatus}
    </div>
  {/if} -->
</div>
