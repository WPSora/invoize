<script>
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Button } from "$lib/components/ui/button";
  import { slide } from "svelte/transition";
  import { Label } from "$lib/components/ui/label";
  import { xenditKey, xenditToken } from "$lib/stores/settings-store";
  import { isXenditChecked, totalXendit, xenditCurrencies, currencyPayload } from "$lib/stores/invoice-store";
  import { activeTab1, activeTab2, activeTab3, xenditPrimaryCurrency } from "$lib/stores/settings-store";
  import { SettingsTab1 } from "$lib/common/enum";
  import { onMount } from "svelte";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  const goToSetting = () => {
    $activeTab1 = SettingsTab1.PAYMENT.VALUE;
    $activeTab2 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.VALUE;
    $activeTab3 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.TAB3.XENDIT;
    window.location.href = "#/setting";
    window.scrollTo(0, 0);
  };

  $: isDisabled = !$xenditCurrencies.includes($currencyPayload.name);
  $: isDisabled && ($isXenditChecked = false);

  onMount(() => {
    return () => {
      $isXenditChecked = false;
    };
  });
</script>

<div class=" flex flex-col sm:max-w-xs gap-y-1.5">
  <div class="flex flex-row gap-x-2">
    {#if !isDisabled}
      <Checkbox
        id="xendit-payment"
        bind:checked="{$isXenditChecked}"
        disabled="{isDisabled || !$xenditKey || !$xenditToken}"
        class="{(isDisabled || !$xenditKey || !$xenditToken) && 'border-muted-foreground cursor-not-allowed'}" />
    {/if}
    <Label
      for="xendit-payment"
      class="flex flex-row flex-nowrap items-center gap-1 {(isDisabled || !$xenditKey || !$xenditToken) &&
        'text-muted-foreground cursor-not-allowed'}">
      <div>Xendit</div>
      <div class="font-normal text-xs text-muted-foreground">
        {#if isDisabled}
          (Currency chosen not supported with your Xendit primary currency)
        {:else}
          (Payment will be converted to {$xenditPrimaryCurrency})
        {/if}
      </div></Label>
  </div>

  {#if (!$xenditKey || !$xenditToken) && $isXenditChecked}
    <div transition:slide class=" text-sm text-muted-foreground">
      <div>Xendit payment not configured yet.</div>
      <Button variant="link" class="p-0 h-fit text-xs text-blue-500" on:click="{goToSetting}">Go to Setting</Button>
    </div>
  {:else if $isXenditChecked && !isDisabled}
    <div transition:slide class="flex flex-nowrap items-center gap-2 text-sm text-muted-foreground">
      <div>Converted value:</div>
      <div class="text-primary">{currencyFormatter($xenditPrimaryCurrency, $totalXendit)}</div>
    </div>
  {/if}
</div>
