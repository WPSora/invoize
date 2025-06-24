<script>
  import PaymentMethod from "$lib/components/settings/payment/payment-method/xendit/payment-method.svelte";
  import SecretKey from "$lib/components/settings/payment/payment-method/xendit/secret-key-xendit.svelte";
  import PrimaryCurrency from "$lib/components/settings/payment/payment-method/xendit/primary-currency-xendit.svelte";
  import CurrencyConverter from "$lib/components/settings/payment/payment-method/xendit/currency-converter-xendit.svelte";
  import RedirectUrl from "$lib/components/settings/payment/payment-method/xendit/redirect-url.svelte";
</script>

<div class="w-full flex flex-col px-6 gap-6">
  <SecretKey />
  <PaymentMethod />
  <PrimaryCurrency />
  <CurrencyConverter />
  <RedirectUrl />
</div>
