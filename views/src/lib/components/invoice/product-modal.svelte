<script>
  import * as Dialog from "$lib/components/ui/dialog";
  import { currencies } from "$lib/stores/settings-store";
  import { toast } from "svelte-french-toast";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { createPostRequest } from "$lib/helpers/request";
  import { Button } from "$lib/components/ui/button";
  import { Textarea } from "$lib/components/ui/textarea";
  import { Loader2 } from "lucide-svelte";
  import { createEventDispatcher } from "svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import CustomCurrencyInput from "$lib/components/custom-ui/CustomCurrencyInput.svelte";

  export let isOpen = false;
  export let product = {};
  // update this to update selected input (for edit only)
  export let selectedCurrency = null;
  export let loadedFromFormInvoice = false;

  $: loadedFromFormInvoice ? product.currency = selectedCurrency?.value : null;

  const dispatch = createEventDispatcher();
  let isLoading = false;

  const checkValidInput = () => {
    let isValid = true;
    if (!product.price) {
      toast.error("Please fill unit price");
      isValid = false;
    }
    if (!product.currency) {
      toast.error("Please choose currency");
      isValid = false;
    }
    return isValid;
  };

  const resetInput = () => {
    product = {};
    selectedCurrency = null;
  };

  const saveProduct = () => {
    if (!checkValidInput()) {
      return;
    }
    if (isLoading) {
      return;
    }
    isLoading = true;
    const route = product.id ? "product/update" : "product/add";
    toast.promise(
      createPostRequest(route, product, (res) => {
        dispatch("update", res.data?.data);
      }),
      {
        loading: "Saving product...",
        success: () => {
          resetInput();
          isLoading = false;
          isOpen = false;
          return "Product saved successfully!";
        },
        error: (err) => {
          isLoading = false;
          return handleError(err, "Failed to save product");
        },
      },
    );
  };

</script>

<Dialog.Root bind:open={isOpen}>
  <Dialog.Content class="max-w-lg">
    <Dialog.Header>
      <Dialog.Title>Product</Dialog.Title>
      <Dialog.Description>Add your product information</Dialog.Description>
    </Dialog.Header>
    <form on:submit|preventDefault={saveProduct} class="space-y-4">
      <!-- Name -->
      <div class="grid w-full items-center gap-1.5">
        <Label for="product-name">Name <MiniStar /></Label>
        <Input required type="text" id="product-name" bind:value={product.name} placeholder="Product name" />
      </div>

      <!-- Currency -->
      {#if !loadedFromFormInvoice}
        <div>
          <Label for="currency-selector" class="mb-2">Currency <MiniStar /></Label>
          <Select
            bind:selected={selectedCurrency}
            onSelectedChange={(e) => {
              product.currency = e.value;
            }}
            required>
            <SelectTrigger id="currency-selector" class="shadow-none">
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
      <!-- Unit Price -->
      <div class="grid w-full items-center gap-1.5">
        <Label for="unit-price">Unit Price <MiniStar /></Label>
        <CustomCurrencyInput
          inputClass="text-start"
          currencyName={selectedCurrency?.value?.name}
          placeholder="Product price"
          bind:value={product.price} />
      </div>

      <!-- Description -->
      <div class="grid w-full items-center gap-1.5">
        <Label for="product-description">Description</Label>
        <Textarea
          id="product-description"
          bind:value={product.description}
          placeholder="Product description"
          class="h-20 shadow-none" />
      </div>

      <Dialog.Footer>
        <Button disabled={isLoading}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </Dialog.Footer>
    </form>
  </Dialog.Content>
</Dialog.Root>
