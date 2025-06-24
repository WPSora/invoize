<script>
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";
  import { isWcPaymentChecked, wcPayment } from "$lib/stores/invoice-store";

  export let isEdit;
  export let isWoocommerce;

  $: isEdit && isWoocommerce && $wcPayment && checkPayment();

  const checkPayment = () => {
    $isWcPaymentChecked = true;
  };
</script>

{#if isEdit && isWoocommerce && $wcPayment}
  <div class="flex flex-row gap-x-2">
    <Checkbox id="wc-payment" bind:checked={$isWcPaymentChecked} />
    <Label for="wc-payment">Woocommerce payment</Label>
  </div>
{/if}
