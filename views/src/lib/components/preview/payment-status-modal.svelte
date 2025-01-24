<script>
  import { Dialog, DialogHeader, DialogTitle, DialogContent, DialogDescription } from "$lib/components/ui/dialog";
  import { BadgeCheck, XCircle, Loader2, Check, X } from "lucide-svelte";
  import { fade } from "svelte/transition";
  import Button from "$lib/components/ui/button/button.svelte";

  export let invoiceId;
  export let isOpen = false;
  export let isCapturingPayment;
  export let isPaymentSuccess;
  export let isUpdatingPaymentStatus;
  export let publicLink;
  export let isInvoicePaymentStatusUpdated;

  const goToInvoicePage = () => {
    window.location.href = publicLink;
  };
</script>

<Dialog bind:open={isOpen} closeOnOutsideClick={false} closeOnEscape={false} onOpenChange={goToInvoicePage}>
  <DialogContent
    class="flex flex-col justify-center items-center bg-gradient-to-tr from-slate-100 to-white shadow-2xl p-12">
    {#if isCapturingPayment}
      <div class="h-60 flex flex-col items-center justify-center gap-3">
        <DialogHeader>Don't close this page while we process your payment...</DialogHeader>
        <Loader2 class="h-20 w-20 animate-spin text-primary" strokeWidth={1} />
      </div>
    {:else}
      {#if isPaymentSuccess}
        <div transition:fade class="flex flex-col items-center justify-center mb-2">
          <BadgeCheck class="h-44 w-44 text-green-600" />
          <DialogHeader class="text-2xl font-bold mb-1">Payment Successful</DialogHeader>
          <div class="text-center text-base">
            Your payment for <b>Invoice {invoiceId ?? ""}</b> has been successfully accepted.
          </div>
        </div>
      {:else if !isPaymentSuccess}
        <div transition:fade class="flex flex-col items-center justify-center">
          <DialogHeader class="font-semibold text-xl mb-4">Payment Failed</DialogHeader>
          <XCircle class="h-28 w-28 text-destructive" />
        </div>
      {/if}

      {#if isUpdatingPaymentStatus}
        <div class="flex gap-2 items-center justify-center">
          <div class="text-muted-foreground">Updating invoice payment status</div>
          <Loader2 class="h-4 w-4 text-primary-300 animate-spin" />
        </div>
      {:else if !isUpdatingPaymentStatus && isInvoicePaymentStatusUpdated}
        <div class="flex gap-2 items-center justify-center">
          <Check class="h-4 w-4 text-green-600" strokeWidth={3} />
          <div class="text-sm">Invoice payment status has been updated</div>
        </div>
      {:else if !isUpdatingPaymentStatus && !isInvoicePaymentStatusUpdated}
        <div>
          <div class="flex gap-2 items-center justify-center">
            <X class="h-4 w-4 text-destructive" />
            <div class="text-sm">Failed to update Invoice payment status.</div>
          </div>
          {#if isPaymentSuccess}
            <div class="text-xs text-center">
              Please contact your merchant to manually update the invoice payment satus
            </div>
          {/if}
        </div>
      {/if}

      {#if !isUpdatingPaymentStatus}
        <Button on:click={goToInvoicePage} class="mt-2">Return to Invoice</Button>
      {/if}
    {/if}
  </DialogContent>
</Dialog>
