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
  import { currencies, defaultCurrency } from "$lib/stores/settings-store";
  import { Loader2 } from "lucide-svelte";
  import { taxes } from "$lib/stores/settings-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { toast } from "svelte-french-toast";
  import { createPostRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import CustomCurrencyInput from "$lib/components/custom-ui/CustomCurrencyInput.svelte";

  export let isEditState = false;
  export let isOpen;
  export let isLoading;
  export let isDeleteModalOpen = false;

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
    isEditState ? handleEdit() : handleAdd();
  };

  const setSelectedCurrency = ({ value }) => {
    if (value === "fixed") {
      payload.currency = { label: $defaultCurrency.name, value: { ...$defaultCurrency } };
    } else {
      payload.currency = { label: null, value: null };
    }
  };

  const checkIsValidInput = () => {
    let isValid = true;
    if (payload.type.value === "fixed" && (!payload.currency.value || !payload.currency.label)) {
      toast.error("Please choose currency");
      isValid = false;
    }
    if (!payload.value) {
      toast.error("Please fill tax amount");
      isValid = false;
    }
    return isValid;
  };

  const handleEdit = () => {
    $taxes[payload.id] = {
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
    const tax = {
      name: payload.name,
      description: payload.description ?? null,
      type: payload.type.value,
      currency: payload.currency?.value,
      value: parseFloat(payload.value),
    };
    // if the first tax is "none" (which is the value for no tax created), remove it
    // it acts as a default null value
    if (isEmptyCheck($taxes)) {
      $taxes = [];
    }
    $taxes = [...$taxes, tax];
    submit();
  };

  export const submit = () => {
    if (isLoading) return;
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=invoice", { taxes: $taxes }), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        isOpen = false;
        isDeleteModalOpen = false;
        return "Tax Saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save tax");
      },
    });
  };
</script>

<Dialog bind:open={isOpen} {onOpenChange}>
  <DialogTrigger id="tax-modal"></DialogTrigger>
  <DialogContent class="sm:max-w-[425px] ">
    <form on:submit|preventDefault={handleSubmit}>
      <DialogHeader>
        <DialogTitle>{isEditState ? "Edit Tax" : "Add New Tax"}</DialogTitle>
      </DialogHeader>

      <div class="grid gap-4 py-4">
        <div class="grid grid-cols items-center gap-4">
          <!-- Name -->
          <div class="space-y-2">
            <Label for="tax-name">Name <MiniStar /></Label>
            <Input id="tax-name" type="text" placeholder="Tax name" bind:value={payload.name} required />
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="tax-description">Description</Label>
            <Input id="tax-description" type="text" placeholder="Tax description" bind:value={payload.description} />
          </div>

          <!-- Type -->
          <div>
            <Label for="tax-type-select" class="mb-2">Type <MiniStar /></Label>
            <Select bind:selected={payload.type} required onSelectedChange={setSelectedCurrency}>
              <SelectTrigger id="tax-type-select" class="shadow-none">
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
              <Label for="tax-currency-select" class="mb-2">Currency <MiniStar /></Label>
              <Select bind:selected={payload.currency} required>
                <SelectTrigger id="tax-currency-select" class="shadow-none">
                  <SelectValue placeholder="Select currency" />
                </SelectTrigger>
                <SelectContent class="max-h-60 overflow-y-auto">
                  {#each $currencies as currency}
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
            <Label for="tax-value">Amount <MiniStar /></Label>
            {#if payload.type.value === "fixed"}
              <CustomCurrencyInput
                inputClass="text-start"
                currencyName={payload?.currency?.value?.name}
                bind:value={payload.value}
                placeholder="Tax amount" />
            {:else}
              <div class="flex flex-row items-center space-x-2">
                <Input type="number" id="tax-value" placeholder="Tax amount" bind:value={payload.value} required />
                <div>%</div>
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
