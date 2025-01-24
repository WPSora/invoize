<script>
  import { createPostRequest } from "$lib/helpers/request";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Check, Loader2 } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { Trash } from "radix-icons-svelte";
  import { currencies, defaultCurrency } from "$lib/stores/settings-store";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { onMount } from "svelte";
  import { isPro, isProPopupOpen } from "$lib/stores/settings-store";
  import CurrencySelector from "$lib/components/invoice/currency-selector.svelte";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import toast from "svelte-french-toast";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";
  import { handleError } from "$lib/helpers/errorHelper";

  let selectedCurrencyName;
  let selectedCurrencySymbol;
  let isLoading = false;
  let isDeleteMode = false;
  let isDefaultButtonLoading = [];

  const isAlreadySelected = (currency) => {
    let isCurrencyActive = false;
    $currencies.map((item) => {
      if (item.name === currency.detail.name) {
        toast.error("Currency already added.");
        isCurrencyActive = true;
      }
    });
    return isCurrencyActive;
  };

  const selectCurrency = (currency) => {
    if (isAlreadySelected(currency)) {
      return;
    }
    // if the first currency is "none" (which is the value for no currency created), remove it
    // it acts as a default null value
    if (isEmptyCheck($currencies)) {
      $currencies = [];
    }

    $currencies = [...$currencies, currency.detail];

    // if there is only one currency, set it as default.
    if ($currencies.length === 1 && $currencies[0].name !== "none") {
      saveDefaultCurrency(currency.detail);
    }

    submit();
  };

  const deleteSavedCurrencies = (i) => {
    const deletedCurrency = $currencies.find((_, index) => index === i);
    $currencies = $currencies.filter((_, index) => index !== i);
    // add default value if no currency is saved, because it can't save null value
    if ($currencies.length === 0) {
      $currencies = [...emptyData];
    }
  };

  const saveDefaultCurrency = async (currency, i = -1) => {
    if (isDefaultButtonLoading[i]) {
      return;
    }
    isDefaultButtonLoading[i] = true;
    try {
      await createPostRequest("settings/update?tab=invoice", { defaultCurrency: currency });
      $defaultCurrency = currency;
      isDefaultButtonLoading[i] = false;
      toast.success("Default currency updated.");
    } catch (err) {
      isDefaultButtonLoading[i] = false;
      handleError(err, "Failed to save default currency.");
    }
  };

  const submit = () => {
    if (isLoading) {
      return;
    }
    isLoading = true;
    // if delete all currency
    if (isEmptyCheck($currencies)) {
      $defaultCurrency = { name: null, symbol: null };
      saveDefaultCurrency({ name: "none" });
    }

    // if  delete default currency
    const hasDefault = $currencies.find((currency) => currency.name === $defaultCurrency.name);
    if (!hasDefault && $currencies.length > 0 && $currencies[0].name !== "none") {
      saveDefaultCurrency($currencies[0]);
    }

    toast.promise(createPostRequest("settings/update?tab=invoice", { currencies: $currencies }), {
      loading: "Saving...",
      success: () => {
        isDeleteMode = false;
        isLoading = false;
        return "Currency Saved";
      },
      error: (err) => {
        $currencies.pop();
        $currencies = $currencies;
        isDeleteMode = false;
        isLoading = false;
        return handleError(err, "Failed to save currency");
      },
    });
  };

  onMount(() => {
    $currencies.map((_) => isDefaultButtonLoading.push(false));
  });
</script>

<Card class="w-full flex md:flex-row flex-col p-4">
  <!-- Add currency -->
  <div class="flex-1">
    <CardHeader class="space-y-0.5 md:p-6 px-0">
      <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Add Currency</CardTitle>
      <CardDescription class="ml-3 md:text-sm text-xs">
        Choose currency you want to use in your invoice.
      </CardDescription>
    </CardHeader>
    <CardContent class="sm:w-80 w-full md:px-6 md:pb-6 p-0">
      {#if !$isPro && $currencies.length >= 2 && !isLoading}
        <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} customText="Upgrade to Pro to Use More Currencies" />
      {:else}
        <CurrencySelector
          bind:selectedCurrencyName
          bind:selectedCurrencySymbol
          on:selectCurrency={selectCurrency}
          checkable={false}
          defaultLabel="Select Currency"
          disabled={isLoading || (!$isPro && $currencies.length >= 3)} />
      {/if}
    </CardContent>
  </div>

  <!-- Active currency -->
  <div class="flex-1">
    <CardHeader class="space-y-0.5 md:p-6 px-0">
      <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Active Currency</CardTitle>
      <CardDescription class="ml-3 md:text-sm text-xs">List of active currencies.</CardDescription>
    </CardHeader>
    <CardContent
      class="md:px-6 md:pb-6 px-0 pt-0 grid xl:grid-cols-2 md:grid-cols-1 sm:grid-cols-2 grid-cols-1 gap-4 justify-start">
      {#if !isEmptyCheck($currencies)}
        {#each $currencies as currency, i}
          <div
            class="col-span-1 flex flex-nowrap justify-between items-center border border-border rounded-lg py-1 pl-3 pr-1 text-sm shadow-sm">
            <div class="text-nowrap">{currency.name} (<span class="text-primary">{@html currency.symbol}</span>)</div>
            {#if !isDeleteMode}
              {#if currency.name === $defaultCurrency.name}
                <Button variant="link" class="text-green-600 flex flex-nowrap items-center gap-x-2 text-xs" disabled>
                  Active
                  <Check class="text-green h-4 w-4" />
                </Button>
              {:else}
                <Button
                  variant="secondary"
                  disabled={isDefaultButtonLoading[i - 1] || isLoading}
                  on:click={() => saveDefaultCurrency(currency, i - 1)}
                  class="flex flex-row flex-nowrap items-center gap-x-2 rounded-lg text-xs">
                  {#if isDefaultButtonLoading[i - 1]}
                    <Loader2 class="w-4 h-4 animate-spin" />
                    <div>Setting as Default</div>
                  {:else}
                    <div>Set as Default</div>
                  {/if}
                  <!-- <Check class="text-green-600 w-5 h-5" /> -->
                </Button>
              {/if}
            {:else}
              <!-- delete button on each currency -->
              <div>
                <DeleteButton
                  class="shadow-none border-none"
                  disabled={isLoading}
                  on:click={() => deleteSavedCurrencies(i)} />
              </div>
            {/if}
          </div>
        {/each}
      {:else}
        <div class="italic text-sm text-muted-foreground my-3">No currency saved.</div>
      {/if}
    </CardContent>

    {#if !isDeleteMode}
      <div class="w-full flex flex-row items-center justify-end md:pr-6">
        <DeleteButton class="w-28" disabled={isLoading} on:click={() => (isDeleteMode = !isDeleteMode)}>
          <div class="flex flex-row flex-nowrap items-center justify-around text-xs gap-x-1">
            {#if !isLoading}
              Delete
              <Trash />
            {:else}
              Delete
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            {/if}
          </div>
        </DeleteButton>
      </div>
    {:else}
      <div class="w-full flex flex-row items-center justify-end">
        <Button
          class="w-28"
          disabled={isLoading}
          on:click={() => {
            submit();
          }}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </div>
    {/if}
  </div>
</Card>
