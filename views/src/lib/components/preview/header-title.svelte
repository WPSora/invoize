<script>
  import { CardTitle } from "$lib/components/ui/card";

  export let invoice;
  export let page;
  export let isPublic;
</script>

<div class="flex items-center gap-12">
  <div class="flex flex-col items-end">
    <!-- Title -->
    <CardTitle slot="title" class="sm:text-[32px] text-[24px] font-bold mb-2">
      {#if page === "invoice" || isPublic}
        INVOICE
      {:else if page === "receipt"}
        RECEIPT
      {/if}
    </CardTitle>

    <!-- Invoice/Receipt number -->
    {#if page === "invoice" || isPublic}
      <div class="text-xl font-semibold">{invoice?.invoiceNumber}</div>
    {:else if page === "receipt"}
      <div class="text-lg font-semibold">{invoice?.receiptNumber}</div>
      <div class="text-sm font-medium">{invoice?.invoiceNumber}</div>
    {/if}
    {#if invoice.isWoocommerce}
      <div class="text-xs text-muted-foreground italic">From Woocommerce #{invoice.isWoocommerce}</div>
    {/if}
  </div>
</div>
