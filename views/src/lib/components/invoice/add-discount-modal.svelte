<script>
  import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Button } from "$lib/components/ui/button";
  import { Label } from "$lib/components/ui/label";
  import { Input } from "$lib/components/ui/input";
  import { Loader2 } from "lucide-svelte";
  import { currencies, defaultCurrency } from "$lib/stores/settings-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { discounts } from "$lib/stores/settings-store";
  import { toast } from "svelte-french-toast";
  import { createPostRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import CustomCurrencyInput from "$lib/components/custom-ui/CustomCurrencyInput.svelte";
  import { selectedDiscount, currencyPayload } from "$lib/stores/invoice-store";
  import { Alert, AlertDescription } from "$lib/components/ui/alert"
  import DialogDescription from "../ui/dialog/dialog-description.svelte";
  import { writable } from "svelte/store";

  export let isEditState = false;
  export let isDeleteModalOpen = false;
  export let temporaryEdit = false;
  export let isOpen;
  export let isLoading;
  export let fromSetting = false; // modal opened from settings page

  /** @type {object}
   * @property {number | null} id
   * @property {string} name
   * @property {string | null} description
   * @property {{ value: string, label: string }} type
   * @property {{ value: { name: string, symbol: string }, label: string }} currency
   * @property {string} value
   */
  export let payload = {
    id: null,
    name: "",
    description: "",
    type: { value: "percent", label: "Percent (%)" },
    currency: { value: null, label: null },
    value: "",
  };

  let discountCurrencies = writable([]);
  
  $: $discountCurrencies = $currencies.filter((currency) => {
    if(fromSetting) {
      return true;
    }
    return $currencyPayload.name == currency.name;
  });

  const resetPayload = () => {
    payload = {
      id: null,
      name: "",
      description: "",
      type: { value: "percent", label: "Percent (%)" },
      currency: { value: null, label: null },
      value: "",
    };
  };

  const onOpenChange = () => {
    isOpen = false;
    resetPayload();
  };

  const handleSubmit = () => {
    if (!checkIsValidInput()) {
      return;
    }
    // Handle temporary edit
    if(temporaryEdit) {
      $selectedDiscount[payload.index] = {
        name: payload.name,
        description: payload.description ?? null,
        type: payload.type.value,
        currency: payload.type.value == 'percent' ? null : payload.currency.value,  
        value: payload['value'],
      };
      temporaryEdit = false;
      isOpen = false;
      return;
    }
    isEditState ? handleEdit() : handleAdd();
  };

  const setSelectedCurrency = ({ value }) => {
    if($currencyPayload.name != 'none') {
      payload.currency = { value: $currencyPayload.name, label: $currencyPayload.name };
    } else {
      payload.currency = value === "fixed" 
        ? { label: $defaultCurrency.name, value: { ...$defaultCurrency } }
        : { value: null, label: null };
    }
    payload.value = 0
  };

  const checkIsValidInput = () => {
    let isValid = true;
    if (payload.type.value === "fixed" && (!payload.currency.value || !payload.currency.label)) {
      toast.error("Please choose currency");
      isValid = false;
    }
    if (!payload.value) {
      toast.error("Please fill discount amount");
      isValid = false;
    }
    return isValid;
  };

  const handleEdit = () => {
    $discounts[payload.id] = {
      id: payload.id,
      name: payload.name,
      description: payload.description ?? null,
      type: payload.type.value,
      currency: payload.currency.value,
      value: parseFloat(payload.value),
    };
    submit();
  };

  const handleAdd = () => {
    const discount = {
      name: payload.name,
      description: payload.description ?? null,
      type: payload.type.value,
      currency: payload.currency.value,
      value: parseFloat(payload.value),
    };
    // if the first discount is "none" (which is the value for no discount created), remove it
    // it acts as a default null value
    if (isEmptyCheck($discounts)) {
      $discounts = [];
    }
    $discounts = [...$discounts, discount];
    submit();
  };

  export const submit = () => {
    if (isLoading) return;
    isLoading = true;

    toast.promise(createPostRequest("settings/update?tab=invoice", { discounts: $discounts }), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        isOpen = false;
        isDeleteModalOpen = false;
        return "Discount Saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save discount.");
      },
    });

  };
</script>

<Dialog bind:open={isOpen} {onOpenChange}>
  <DialogTrigger id="tax-modal"></DialogTrigger>
  <DialogContent class="sm:max-w-[425px] ">
    <form on:submit|preventDefault={handleSubmit}>
      <DialogHeader>
        <DialogTitle>{isEditState || temporaryEdit ? "Edit Discount" : "Add New Discount"}</DialogTitle>
        <DialogDescription>
          {isEditState || temporaryEdit ? "Edit the discount details below" : "Fill in the details below to add a new discount"}
        </DialogDescription>
      </DialogHeader>

      <!-- Temporary edit alert -->
      {#if temporaryEdit}
        <div class="mt-6">
          <Alert variant="warning" class="mb-4">
            <AlertDescription>
              You are in temporary edit mode. Saving will only apply changes to the discount in the current invoice.
            </AlertDescription>
          </Alert>
        </div>
      {/if}

      <div class="grid gap-4 py-4">
        <div class="grid grid-cols-1 items-center gap-4">
          <!-- Name -->
          <div class="space-y-2">
            <Label for="discount-name">Name <MiniStar /></Label>
            <Input id="discount-name" type="text" placeholder="Discount name" bind:value={payload.name} required />
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="discount-description">Description</Label>
            <Input
              id="discount-description"
              type="text"
              placeholder="Discount description"
              bind:value={payload.description} />
          </div>

          <!-- Type -->
          <div>
            <Label for="discount-type-select" class="mb-2">Type <MiniStar /></Label>
            <Select bind:selected={payload.type} required onSelectedChange={setSelectedCurrency}>
              <SelectTrigger id="discount-type-select" class="shadow-none">
                <SelectValue placeholder="Select type" />
              </SelectTrigger>
              <SelectContent class="max-h-60 overflow-y-auto">
                <SelectItem value="percent">Percent (%)</SelectItem>
                <SelectItem value="fixed">Fixed</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Currency -->
          {#if payload.type.value === "fixed"}
            <div>
              <Label for="discount-currency-select" class="mb-2">Currency <MiniStar /></Label>
              <Select bind:selected={payload.currency} required disabled={$currencyPayload.name != 'none' && !fromSetting}>
                <SelectTrigger id="discount-currency-select" class="shadow-none">
                  <SelectValue placeholder="Select currency" />
                </SelectTrigger>
                <SelectContent class="max-h-60 overflow-y-auto">
                  {#each $discountCurrencies as currency}
                    <SelectItem value={currency}>{currency.name}</SelectItem>
                  {:else}
                    <div class="text-sm p-2 text-muted-foreground italic">You have no currencies saved</div>
                  {/each}
                </SelectContent>
              </Select>
            </div>
          {/if}

          <!-- Amount -->
          <div class="space-y-2">
            <Label for="discount-value">Amount <MiniStar /></Label>
            {#if payload.type.value === "fixed"}
              <CustomCurrencyInput
                inputClass="text-start"
                currencyName={payload?.currency?.label?.trim()}
                bind:value={payload.value}
                placeholder="Discount amount" />
            {:else}
              <div class="flex flex-row items-center space-x-2">
                <Input
                  type="number"
                  max="100"
                  id="discount-value"
                  placeholder="Discount amount"
                  bind:value={payload.value}
                  required />
                {#if payload.type.value === "percent"}
                  <div>%</div>
                {/if}
              </div>
            {/if}
          </div>
        </div>
      </div>

      <!-- Save -->
      <DialogFooter>
        <Button type="submit" disabled={isLoading}>
          {#if isLoading}
            <Loader2 class="h-4 w-4 mr-1 animate-spin text-white" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </DialogFooter>
    </form>
  </DialogContent>
</Dialog>
