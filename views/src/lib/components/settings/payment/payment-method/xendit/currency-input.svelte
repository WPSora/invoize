<script>
  import { Input } from "$lib/components/ui/input";
  import { Button } from "$lib/components/ui/button";
  import { Loader2, Check } from "lucide-svelte";
  import { removeArrowOnInputStyle } from "$lib/common/styles";
  import { createPostRequest } from "$lib/helpers/request";
  import { slide } from "svelte/transition";
  import { xenditPrimaryCurrency, hasPaymentTabSettings, xenditCurrencyConverter } from "$lib/stores/settings-store";
  import { handleError } from "$lib/helpers/errorHelper";
  import { numberFormatter } from "$lib/helpers/decimalFormatter";
  import toast from "svelte-french-toast";

  let currencyName;
  let isLoading = false;
  /** @type string uppercase*/
  export let name;
  // /** @type number */
  export let value;
  export let isEditing = false;
  export let isFocused;

  $: $hasPaymentTabSettings && updateCurrencyName($xenditPrimaryCurrency);

  const updateCurrencyName = (currency) => {
    currencyName = currency;
  };

  const updateState = (e) => {
    isEditing = true;
    value = e.target.value.replace(",", ".");
  };

  const validateInput = () => {
    const parsed = parseFloat(value);
    if (isNaN(parsed)) {
      toast.error("Invalid input");
      return false;
    }
    value = parsed;
    return true;
  };

  const saveInputToStore = () => {
    $xenditCurrencyConverter[name] = value;
  };

  const submit = () => {
    if (isLoading) return;
    if (!validateInput()) return;
    isLoading = true;
    const settingsName = `xenditCurrencyConverter.${name}`;
    toast.promise(createPostRequest("settings/update?tab=payment", { [settingsName]: value }), {
      loading: "Saving...",
      success: () => {
        saveInputToStore();
        isLoading = false;
        isEditing = false;
        return "Currency saved successfully";
      },
      error: (err) => {
        isEditing = false;
        isLoading = false;
        return handleError(err, "Failed to save currency");
      },
    });
  };
</script>

<div class="flex flex-row flex-nowrap justify-between items-center py-1 px-2 rounded-lg bg-muted">
  <div class="text-sm">1 {name}</div>
  <form on:submit|preventDefault={submit} class="flex flex-nowrap gap-2 items-center justify-center">
    <div class="flex flex-nowrap items-center justify-center gap-2">
      <div class="text-sm">{currencyName}</div>
      {#if isFocused}
        <Input
          {name}
          type="text"
          id="{name}-converter"
          class={removeArrowOnInputStyle}
          bind:value
          on:blur={() => (isFocused = false)}
          on:input={updateState} />
      {:else}
        <Input
          type="text"
          value={numberFormatter(currencyName, value)}
          on:focus={() => {
            isFocused = true;
            setTimeout(() => {
              document.getElementById(`${name}-converter`)?.focus();
            }, 100);
          }} />
      {/if}
    </div>
    {#if isEditing}
      <div transition:slide={{ axis: "x" }}>
        <Button disabled={isLoading} size="icon" type="submit">
          {#if isLoading}
            <Loader2 class="h-4 w-4 text-white animate-spin" />
          {:else}
            <Check class="h-4 w-4 text-white" />
          {/if}
        </Button>
      </div>
    {/if}
  </form>
</div>
