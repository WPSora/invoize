<script>
  import { CardTitle, CardDescription } from "$lib/components/ui/card";
  import { xenditCurrencyConverter, hasPaymentTabSettings } from "$lib/stores/settings-store";
  import CurrencyInput from "$lib/components/settings/payment/payment-method/xendit/currency-input.svelte";

  let USD = 0;
  let EUR = 0;
  let GBP = 0;
  let isInputFocused = [false, false, false];

  // update input value when API data already saved to store.
  $: $hasPaymentTabSettings && saveStoreToInput();

  const saveStoreToInput = () => {
    USD = $xenditCurrencyConverter.USD;
    EUR = $xenditCurrencyConverter.EUR;
    GBP = $xenditCurrencyConverter.GBP;
  };
</script>

<div class="space-y-2">
  <div>
    <CardTitle class="md:text-base">Currency Converter</CardTitle>
    <CardDescription class="text-xs">
      Set the value of currency exchange. If your invoice currency is in the list of currencies below, then it will
      automatically get converted to chosen Primary Currency.
    </CardDescription>
  </div>
  <div class="space-y-2">
    <CurrencyInput name="USD" bind:value={USD} isFocused={isInputFocused[0]} />
    <CurrencyInput name="EUR" bind:value={EUR} isFocused={isInputFocused[1]} />
    <CurrencyInput name="GBP" bind:value={GBP} isFocused={isInputFocused[2]} />
  </div>
</div>
