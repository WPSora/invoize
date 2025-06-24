<script>
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Button } from "$lib/components/ui/button";
  import { slide } from "svelte/transition";
  import { Label } from "$lib/components/ui/label";
  import { directPaypals, automaticPaypals } from "$lib/stores/settings-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { selectedPaypal, isPaypalChecked, selectedPaypalBind } from "$lib/stores/invoice-store";
  import { activeTab1, activeTab2, activeTab3 } from "$lib/stores/settings-store";
  import { SettingsTab1, PaypalType, PaypalTypeLabel } from "$lib/common/enum";
  import { onMount } from "svelte";

  let isSelectOpen = false;

  $: isDisabled = isEmptyCheck($automaticPaypals) && isEmptyCheck($directPaypals);

  const updateSelectedPaypal = (e) => {
    if (e.label === PaypalTypeLabel.DIRECT) {
      $selectedPaypal = { name: e.value, type: PaypalType.DIRECT };
    }
    if (e.label === PaypalTypeLabel.AUTO) {
      $selectedPaypal = { name: e.value.name, type: PaypalType.AUTO };
    }
  };

  const resetSelected = () => {
    $selectedPaypal = null;
    $selectedPaypalBind = null;
  };

  const goToSetting = () => {
    $activeTab1 = SettingsTab1.PAYMENT.VALUE;
    $activeTab2 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.VALUE;
    $activeTab3 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.TAB3.PAYPAL;
    window.location.href = "#/setting";
    window.scrollTo(0, 0);
  };

  onMount(() => {
    return () => {
      $isPaypalChecked = false;
      resetSelected();
    };
  });
</script>

<div class=" flex flex-col sm:max-w-xs gap-y-1.5">
  <div class="flex flex-col gap-y-2">
    <div class="flex flex-row gap-x-2">
      <Checkbox
        id="paypal-payment"
        disabled={isDisabled}
        bind:checked={$isPaypalChecked}
        on:click={resetSelected}
        class={isDisabled && "border-muted-foreground cursor-not-allowed"} />
      <Label for="paypal-payment" class={isDisabled && "text-muted-foreground cursor-not-allowed"}>Paypal</Label>
    </div>
    {#if $isPaypalChecked}
      <div transition:slide>
        <Select bind:open={isSelectOpen} onSelectedChange={updateSelectedPaypal} bind:selected={$selectedPaypalBind}>
          <SelectTrigger class="h-fit">
            <SelectValue placeholder="Choose payment method" />
          </SelectTrigger>
          <SelectContent class="max-h-60 overflow-y-auto">
            {#if !isEmptyCheck($automaticPaypals) || !isEmptyCheck($directPaypals)}
              {#if !isEmptyCheck($automaticPaypals)}
                <!-- Automatic paypal -->
                {#each $automaticPaypals as paypal}
                  <SelectItem
                    value={paypal}
                    label={PaypalTypeLabel.AUTO}
                    class="flex flex-col items-start justify-start"
                    on:click={() => ($selectedPaypal = $selectedPaypal)}>
                    <div>Auto Confirmation</div>
                  </SelectItem>
                {/each}
              {/if}
              {#if !isEmptyCheck($directPaypals)}
                {#each $directPaypals as paypal}
                  <SelectItem
                    value={paypal}
                    label={PaypalTypeLabel.DIRECT}
                    class="flex flex-col items-start justify-start"
                    on:click={() => ($selectedPaypal = $selectedPaypal)}>
                    <div>Direct Payment</div>
                  </SelectItem>
                {/each}
              {/if}
              <!-- Direct paypal -->
            {:else}
              <div class="p-2 text-sm text-muted-foreground">
                <div>No paypal payment set</div>
                <Button variant="link" class="p-0 h-fit text-xs text-blue-500" on:click={goToSetting}>
                  Go to Setting
                </Button>
              </div>
            {/if}
          </SelectContent>
        </Select>
      </div>
    {/if}
  </div>

  <!-- Preview -->
  {#if $selectedPaypal}
    <div transition:slide class={`sm:w-[300px] text-sm text-muted-foreground italic ml-2`}>
      <div class="font-medium">{$selectedPaypal.name}</div>
      {#if $selectedPaypal.type === PaypalType.AUTO}
        <div>Link will be generated after invoice is created.</div>
      {/if}
    </div>
  {/if}
</div>
