<script>
  import { CardTitle, CardDescription } from "$lib/components/ui/card";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { xenditCurrencies } from "$lib/common/options";
  import { Separator } from "$lib/components/ui/separator";
  import { createPostRequest } from "$lib/helpers/request";
  import { xenditPrimaryCurrency, hasPaymentTabSettings } from "$lib/stores/settings-store";
  import toast from "svelte-french-toast";
  import { Loader2 } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";

  let selectedValue = { label: null, value: null };
  let isLoading = false;

  $: $hasPaymentTabSettings && updateInputFromStore();

  const updateInputFromStore = () => {
    selectedValue = { label: $xenditPrimaryCurrency, value: $xenditPrimaryCurrency };
  };

  const saveCurrency = (e) => {
    if (isLoading) return;
    isLoading = true;
    const payload = e.value;
    toast.promise(createPostRequest("settings/update?tab=payment", { xenditPrimaryCurrency: payload }), {
      loading: "Saving...",
      success: () => {
        $xenditPrimaryCurrency = payload;
        selectedValue = { label: payload, value: payload };
        isLoading = false;
        return "Default currency saved successfully";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save default currency");
      },
    });
  };
</script>

<div>
  <div class="flex flex-nowrap justify-between items-center">
    <div>
      <CardTitle class="md:text-base">Primary Currency</CardTitle>
      <CardDescription class="text-xs">
        Choose your Xendit account supported currency. Payment will be paid in this currency
      </CardDescription>
    </div>
    <div class="w-48">
      <Select selected={selectedValue} onSelectedChange={saveCurrency} disabled={isLoading}>
        <SelectTrigger id="currency">
          <div class="flex flex-nowrap items-center">
            <SelectValue placeholder="Select currency" />
            {#if isLoading}
              <Loader2 class="h-4 w-4 text-primary animate-spin ml-2" />
            {/if}
          </div>
        </SelectTrigger>
        <SelectContent class="max-h-60 overflow-y-auto">
          {#each xenditCurrencies as currency}
            <SelectItem value={currency} label={currency}>{currency}</SelectItem>
          {/each}
        </SelectContent>
      </Select>
    </div>
  </div>
  <Separator class="mt-4" />
</div>
