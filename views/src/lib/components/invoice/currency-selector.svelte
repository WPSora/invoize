<script>
  import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem } from "$lib/components/ui/command";
  import { Popover, PopoverContent, PopoverTrigger } from "$lib/components/ui/popover";
  import { Button } from "$lib/components/ui/button";
  import { Check, ChevronsUpDown } from "lucide-svelte";
  import { tick } from "svelte";
  import { cn } from "$lib/utils";
  import { createEventDispatcher } from "svelte";
  import { isWcInstalled, defaultCurrenciesOption } from "$lib/stores/settings-store";
  import { onMount } from "svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { toast } from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";

  const dispatch = createEventDispatcher();

  export let selectedCurrencyName;
  export let selectedCurrencySymbol;
  export let checkable = true;
  export let disabled = false;
  export let defaultLabel = null;

  let isCurrencyPopoverOpen;

  const updateSelectedCurrency = (value) => {
    selectedCurrencyName = value;
    selectedCurrencySymbol = $defaultCurrenciesOption[value];
  };

  const closeAndFocusTrigger = (triggerId) => {
    isCurrencyPopoverOpen = false;
    tick().then(() => {
      document.getElementById(triggerId)?.focus();
    });
  };

  onMount(() => {
    // overwrites default currencies if Wc is installed
    // if not installed, then use default currency on store
    if ($isWcInstalled) {
      createGetRequest("settings/currencies")
        .then((res) => {
          $defaultCurrenciesOption = res.data;
        })
        .catch((err) => {
          handleError(err, "Failed to retrieve currencies data.");
        });
    }
  });
</script>

<Popover bind:open={isCurrencyPopoverOpen}>
  <PopoverTrigger asChild let:builder class="w-full">
    <Button
      {disabled}
      builders={[builder]}
      variant="outline"
      role="combobox"
      aria-expanded={isCurrencyPopoverOpen}
      class="w-full justify-between text-wrap text-left h-fit"
      id="select-currency">
      {#if defaultLabel}
        {defaultLabel}
      {:else}
        {selectedCurrencyName} ({@html selectedCurrencySymbol})
      {/if}
      <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
    </Button>
  </PopoverTrigger>
  <PopoverContent class="w-60 p-0 max-h-60 overflow-y-scroll shadow-2xl">
    <Command loop>
      <CommandInput
        placeholder="Search currency..."
        id="currency-search-input"
        style="border: 0px solid black; margin: 8px;" />
      <CommandEmpty>No currency found</CommandEmpty>
      <CommandGroup>
        {#each Object.entries($defaultCurrenciesOption) as [key, value]}
          <CommandItem
            value={key}
            onSelect={(currentValue) => {
              updateSelectedCurrency(currentValue);
              dispatch("selectCurrency", { name: key, symbol: value });
              closeAndFocusTrigger("select-currency");
            }}>
            {#if checkable}
              <Check class={cn("h-4 w-10", selectedCurrencyName !== key && "text-transparent")} />
            {/if}
            <div class="w-full flex justify-between">
              <div>{key}</div>
              <div>{@html value}</div>
            </div>
          </CommandItem>
        {/each}
      </CommandGroup>
    </Command>
  </PopoverContent>
</Popover>
