<script>
  import AutomaticPaypal from "$lib/components/settings/payment/payment-method/paypal/automatic-paypal.svelte";
  import DirectPaypal from "$lib/components/settings/payment/payment-method/paypal/direct-paypal.svelte";
</script>

<div class="w-full flex flex-col px-6 gap-6">
  <DirectPaypal />
  <AutomaticPaypal />
</div>
