<script>
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Button } from "$lib/components/ui/button";
  import { slide } from "svelte/transition";
  import { Label } from "$lib/components/ui/label";
  import { banks } from "$lib/stores/settings-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { selectedBank, isBankPaymentChecked } from "$lib/stores/invoice-store";
  import { activeTab1, activeTab2, activeTab3, defaultBank } from "$lib/stores/settings-store";
  import { currencyPayload } from "$lib/stores/invoice-store";
  import { SettingsTab1 } from "$lib/common/enum";
  import { onMount } from "svelte";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  let isSelectOpen = false;

  $: isDisabled = isEmptyCheck($banks);

  const handleCheck = (isChecked) => {
    if (isChecked) {
      const selected = $banks.find((bank) => bank.id === $defaultBank);
      if (selected && $currencyPayload.name === selected.currency.name) {
        $selectedBank = { label: selected.name, value: selected };
      }
    } else {
      resetSelected();
    }
  };

  const resetSelected = () => {
    $isBankPaymentChecked = false;
    $selectedBank = null;
  };

  const goToSetting = () => {
    $activeTab1 = SettingsTab1.PAYMENT.VALUE;
    $activeTab2 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.VALUE;
    $activeTab3 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.TAB3.BANK;
    window.location.href = "#/setting";
    window.scrollTo(0, 0);
  };

  onMount(() => {
    return () => {
      resetSelected();
    };
  });
</script>

<div class=" flex flex-col sm:max-w-xs gap-y-1.5">
  <div class="flex flex-col gap-y-2">
    <div class="flex flex-row gap-x-2">
      <Checkbox
        id="bank-payment"
        disabled={isDisabled}
        bind:checked={$isBankPaymentChecked}
        onCheckedChange={handleCheck}
        class={isDisabled && "border-muted-foreground cursor-not-allowed"} />
      <Label for="bank-payment" class={isDisabled && "text-muted-foreground cursor-not-allowed"}>Bank</Label>
    </div>
    {#if $isBankPaymentChecked}
      <div transition:slide>
        <Select bind:selected={$selectedBank} bind:open={isSelectOpen}>
          <SelectTrigger class="h-fit">
            <SelectValue placeholder="Choose payment method" />
          </SelectTrigger>
          <SelectContent class="max-h-60 overflow-y-auto">
            {#if !isEmptyCheck($banks)}
              <!-- {@const banksChosenCurrency = $banks.filter((b) => b.currency.name == $currencyPayload.name)} -->
              {#each $banks as selection}
                <SelectItem
                  value={selection}
                  label={selection.name}
                  class="flex flex-col items-start justify-start"
                  on:click={() => ($selectedBank = $selectedBank)}>
                  <div class="flex flex-row flex-nowrap items-center justify-between w-full text-primary">
                    <div>{selection.name}</div>
                    <div class="text-xs text-muted-foreground">
                      {selection.currency?.name}
                    </div>
                  </div>
                  <div class="text-muted-foreground text-xs">{selection.type}</div>
                </SelectItem>
              {:else}
                <div class="p-2 text-xs text-muted-foreground">No bank payment available for the chosen currency</div>
              {/each}
            {:else}
              <div class="p-2 text-sm text-muted-foreground">
                <div>No bank payment set</div>
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
  {#if $selectedBank}
    <div transition:slide>
      {#key $selectedBank}
        <div transition:slide class="flex flex-row justify-between text-gray-500 sm:w-[300px]">
          <div class="flex flex-nowrap gap-3 border-dashed border-2 border-border p-2 w-full rounded-md">
            <div>
              <p>Name</p>
              <p>Detail</p>
            </div>
            <div>
              <p>:</p>
              <p>:</p>
            </div>
            <div>
              <p>{$selectedBank.value?.name}</p>
              <p><MultilineText text={$selectedBank.value?.detail} /></p>
            </div>
          </div>
        </div>
      {/key}
    </div>
  {/if}
</div>
