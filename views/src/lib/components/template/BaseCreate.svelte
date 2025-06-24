<script>
  import { onMount, createEventDispatcher } from "svelte";
  import { slide } from "svelte/transition";
  import { Table, TableBody, TableCell, TableRow, TableHead, TableHeader } from "$lib/components/ui/table";
  import { Card, CardContent, CardFooter, CardHeader } from "$lib/components/ui/card";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";
  import { Button } from "$lib/components/ui/button";
  import { Separator } from "$lib/components/ui/separator";
  import { Textarea } from "$lib/components/ui/textarea";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { Plus, Trash, Pencil2 } from "radix-icons-svelte";
  import { Loader2 } from "lucide-svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { setDueDate } from "$lib/helpers/dueDateHelper";
  import { removeArrowOnInputStyle, invoiceFormStyle } from "$lib/common/styles";
  import { dueDateList } from "$lib/common/options";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";
  import {
    hasInvoiceTabSettings,
    dueDateInterval,
    banks,
    xenditCurrencyConverter,
    xenditPrimaryCurrency,
    defaultPayment,
    defaultBank,
    isDebug,
    automaticPaypals,
    isPro,
    directPaypals,
  } from "$lib/stores/settings-store";
  import {
    selectedBusiness,
    client,
    clients,
    wcClients,
    selectedClient,
    selectedClientBind,
    searchClientsResult,
    searchWcClientsResult,
    createdProductList,
    createdProduct,
    note,
    termsAndConditionsInvoice,
    currencyPayload,
    product,
    selectedProductSelectBind,
    products,
    wcProducts,
    searchProductsResult,
    searchWcProductsResult,
    selectedDueDateInterval,
    subtotal,
    total,
    totalDiscount,
    selectedDiscount,
    totalTax,
    selectedTax,
    totalXendit,
    xenditCurrencies,
    isBankPaymentChecked,
    isPaypalChecked,
    isXenditChecked,
    selectedBank,
    selectedPaypal,
    billedTo,
    billedToSameAsClient,
    selectedPaypalBind,
  } from "$lib/stores/invoice-store";
  import { savePaymentTabToSettingsStore, saveInvoiceTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import { handleError } from "$lib/helpers/errorHelper";
  import { PaymentMethod, PaypalType, PaypalTypeLabel } from "$lib/common/enum";
  import toast from "svelte-french-toast";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  import BusinessPreview from "$lib/components/custom-ui/BusinessPreview.svelte";
  import CustomSelect from "$lib/components/custom-ui/Select.svelte";
  import CustomAddressModal from "$lib/components/invoice/custom-address-modal.svelte";
  import CustomerModal from "$lib/components/invoice/customer-modal.svelte";
  import ProductModal from "$lib/components/invoice/product-modal.svelte";
  import BusinessSelect from "$lib/components/custom-ui/BusinessSelect.svelte";
  import Combobox from "$lib/components/custom-ui/Combobox.svelte";
  import ClientPreview from "$lib/components/custom-ui/ClientPreview.svelte";
  import BankSelect from "$lib/components/invoice/bank-select.svelte";
  import PaypalSelect from "$lib/components/invoice/paypal-select.svelte";
  import XenditSelect from "$lib/components/invoice/xendit-select.svelte";
  import CustomCurrencyInput from "$lib/components/custom-ui/CustomCurrencyInput.svelte";
  import Checkbox from "../ui/checkbox/checkbox.svelte";

  const dispatch = createEventDispatcher();
  /** invoice / recurring */
  export let updateDueDate = (e) => {
    dispatch("updateDueDate", e);
  };
  // whether this component use for creating or editing
  export let isEdit = false;
  export let isLoadingSettings = false;
  export let selectedDueDateBind = null;
  export let isLoadingPayment = false;
  export let isDiscountModalOpen = false;
  export let discountData = {};
  export let discountTemporaryEdit = false;

  let isCustomAddressModalOpen = false;
  let isAddCustomerModalOpen = false;
  let isAddProductModalOpen = false;
  let isLoadingBusiness = false;
  let isSearchingProductState = false;
  let isSearchingWcProductState = false;
  let isSearchingCustomerState = false;
  let isSearchingWcCustomerState = false;
  let selectedProduct;
  let selectedProductBind;
  let selectedDescriptionIndex;
  let isWcProduct = false;
  let isWcClient = false;
  let customAddress;

  $: $subtotal = Math.ceil($createdProductList?.reduce((total, item) => total + item.amount, 0) * 100) / 100;
  $: $totalDiscount =
    Math.ceil(
      $selectedDiscount?.reduce((total, item) => {
        const price = calculateAdditionalFee($subtotal, item.value, item.type);
        return total + price;
      }, 0) * 100,
    ) / 100;
  $: calculateSubtotalAfterDiscount = $subtotal - $totalDiscount;
  $: subtotalAfterDiscount = calculateSubtotalAfterDiscount < 0 ? 0 : calculateSubtotalAfterDiscount;
  $: $totalTax =
    Math.ceil(
      $selectedTax?.reduce((total, item) => {
        const price = calculateAdditionalFee(subtotalAfterDiscount, item.value, item.type);
        return total + price;
      }, 0) * 100,
    ) / 100;
  $: calculateTotal = $subtotal + ($totalTax ?? 0) - ($totalDiscount ?? 0);
  $: $total = calculateTotal < 0 ? 0 : calculateTotal;
  $: $totalXendit = Math.ceil(calculateTotalXendit($currencyPayload, $total));

  const calculateTotalXendit = (currency, total) => {
    if ($xenditPrimaryCurrency === currency.name) return total;
    if (Object.keys($xenditCurrencyConverter).includes(currency.name)) {
      return total * $xenditCurrencyConverter[currency.name];
    }
    return null;
  };

  const removeCustomAddress = () => {
    $selectedClient.customAddress = "";
  };

  const openCustomAddressModal = () => {
    isCustomAddressModalOpen = true;
    customAddress = $selectedClient.customAddress;
  };

  const updateCustomerCustomAddress = (e) => {
    $selectedClientBind = {
      label: $selectedClient.name,
      value: { ...$selectedClient, customAddress: e.detail.address },
    };
  };

  const calculateAmount = (i) => {
    // Don't calculate if invalid input
    if (Number.isInteger(i) && ($createdProductList[i].unitPrice < 0 || $createdProductList[i].quantity < 1)) {
      toast.error("Invalid input");
      return;
    }
    // Calculate for already created product
    if (Number.isInteger(i)) {
      $createdProductList[i].amount = $createdProductList[i].quantity * $createdProductList[i].unitPrice;
      return;
    }
    // Calculate for GOING TO BE CREATED product
    if ($createdProduct.quantity > 0 && $createdProduct.unitPrice > 0) {
      $createdProduct.amount = $createdProduct.quantity * $createdProduct.unitPrice;
    }
  };

  const updateSelectedProduct = (selected) => {
    const { id, name, description, price } = selected.value;
    $createdProduct = {
      id,
      name,
      description,
      unitPrice: parseFloat(price),
      quantity: 1,
      amount: 1,
      note: null,
    };
    calculateAmount();
  };

  const addProductToList = () => {
    if ($createdProduct.name && $createdProduct.quantity > 0 && $createdProduct.unitPrice > 0) {
      $createdProductList = [...$createdProductList, $createdProduct];
      $createdProduct = {
        id: 0,
        name: "",
        description: "",
        unitPrice: null,
        quantity: null,
        amount: null,
        note: null,
      };
      selectedProduct = null;
      $selectedProductSelectBind = null;
    } else {
      toast.error("Invalid input");
    }
  };

  const deleteProductFromList = (selectedIndex) => {
    $createdProductList = $createdProductList.filter((_, i) => i != selectedIndex);
    selectedDescriptionIndex = null;
  };

  const setSelectedDescription = (i) => {
    selectedDescriptionIndex = i;
  };

  const deleteSelectedDescription = () => {
    selectedDescriptionIndex = null;
  };

  const createNewProduct = (e) => {
    $isDebug && console.log({ createNewProduct: e });
    const { id, name, description, price, currency } = e.detail;
    const newProduct = {
      id,
      name,
      description,
      unitPrice: price,
      currency,
    };
    selectedProductBind = { ...newProduct };
    selectedProduct = { ...newProduct };
    $createdProduct = {
      ...newProduct,
      quantity: 1,
      amount: 1,
      note: null,
    };
    $selectedProductSelectBind = { label: name, value: { ...newProduct } };
    delete newProduct["unitPrice"];
    $products = [{ ...newProduct, price }, ...$products];
    calculateAmount();
  };

  /** calculate fee percentage value */
  const calculateAdditionalFee = (price, fee, feeType = "#") => {
    if (feeType === "%" || feeType === "percent") {
      return (price * fee) / 100;
    }
    return fee;
  };

  const setXenditCurrencies = () => {
    $xenditCurrencies = [$xenditPrimaryCurrency, ...Object.keys($xenditCurrencyConverter)];
  };

  // this will set the initial due date input
  const setInitialDueDateInput = () => {
    // this is updating the input from store/settings data.
    // if it's editing, the due date input get updated from saveDueDateToInput in EditInvoice
    if ($dueDateInterval && !isEdit) {
      // update value that need to be sent to the API
      // the split use to get from 7 days -> 7
      dispatch("updateDueDate", { value: $dueDateInterval.split(" ")[0] });
      // set default selected value
      selectedDueDateBind = setDueDate($dueDateInterval);
    }
  };

  const setDefaultPaymentToInput = () => {
    if (isEdit) return;
    switch ($defaultPayment.value) {
      case PaymentMethod.BANK:
        const selected = $banks.find((bank) => bank.id === $defaultBank);
        if (selected && $currencyPayload.name === selected.currency.name) {
          $isBankPaymentChecked = true;
          $selectedBank = { label: selected.name, value: selected };
        }
        break;
      case PaymentMethod.PAYPAL_AUTO_CONFIRMATION:
        $isPaypalChecked = true;
        $selectedPaypalBind = { label: PaypalTypeLabel.AUTO, value: $automaticPaypals[0] };
        $selectedPaypal = { name: $automaticPaypals[0]?.name, type: PaypalType.AUTO };
        break;
      case PaymentMethod.PAYPAL_DIRECT:
        $isPaypalChecked = true;
        $selectedPaypalBind = { label: PaypalTypeLabel.DIRECT, value: $directPaypals[0] };
        $selectedPaypal = { name: $directPaypals[0], type: PaypalType.DIRECT };
        break;
      case PaymentMethod.XENDIT:
        $isXenditChecked = true;
        break;
      default:
        break;
    }
  };

  const getSettings = async () => {
    try {
      isLoadingSettings = true;
      const response = await createGetRequest("settings/retrieve?tab=invoice");

      /**
       * Filter out defaultReminderGroup if user is not pro
       */
      let filteredData = response.data.data.filter((item) => {
        if($isPro) {
          return true
        }
        return item.name !== 'defaultReminderGroup';
      });

      saveInvoiceTabToSettingsStore(filteredData, true, isEdit);
      setInitialDueDateInput();
      dispatch("settingsFetched");
      $hasInvoiceTabSettings = true;
      isLoadingSettings = false;
    } catch (err) {
      isLoadingSettings = false;
      handleError(err, "Failed to retrieve settings data");
    }
  };

  const getPayments = async () => {
    try {
      isLoadingPayment = true;
      const response = await createGetRequest("settings/retrieve?tab=payment");
      $isDebug && console.log(response.data);
      savePaymentTabToSettingsStore(response.data.data);
      setXenditCurrencies();
      setDefaultPaymentToInput();
      response.data.data.map((item) => {
        if (item.name === "banks") {
          $banks = item.value;
        }
      });
      isLoadingPayment = false;
    } catch (err) {
      handleError(err, "Failed to retrieve payments data");
    }
  };

  const handleTemporaryEditDiscount = (index) => {
    isDiscountModalOpen = true;
    discountTemporaryEdit = true;
    let discount = $selectedDiscount[index];
    discountData = {
      index,
      name: discount.name,
      description: discount.description,
      type: discount.type === "percent" ? { label: "Percent (%)", value: "percent" } : { label: "Fixed", value: "fixed" },
      value: discount.value,
    }

    if(discount.type != 'percent') {
      discountData['currency'] = { label: $currencyPayload.name, value: {...$currencyPayload} };
    }

  }

  onMount(() => {
    // we fetch settings and save it to invoice-store. Then we bind input to invoice-store.
    getSettings();
    getPayments();
  });

  $: selectedCurrencyForProduct = {
    disabled: false,
    value: $currencyPayload,
    label: $currencyPayload.name,
  };


</script>

<CustomerModal bind:isOpen={isAddCustomerModalOpen} isCreateFromInvoicePage={true}/>
<CustomAddressModal bind:isOpen={isCustomAddressModalOpen} on:customAddress={updateCustomerCustomAddress} bind:address={customAddress}/>
<ProductModal bind:isOpen={isAddProductModalOpen} on:update={createNewProduct} bind:selectedCurrency={selectedCurrencyForProduct} loadedFromFormInvoice={true}/>

<!-- Invoice -->
<Card class="xl:w-[1000px] w-full rounded-md shadow-md mx-2 md:mx-0">
  <CardHeader class="flex flex-row justify-between items-center sm:mx-10 sm:mt-10">
    <!-- Left side Header -->
    <!-- Logo -->
    <div class="text-muted-foreground rounded-lg">
      {#if isLoadingBusiness}
        <Loader2 class="h-10 w-10 animate-spin text-primary ml-5" />
      {:else if $selectedBusiness?.logo}
        <img class="ml-2 h-20 object-cover" src={$selectedBusiness?.logo} alt="Company logo" />
      {:else}
        <!-- if business has no logo -->
        <div></div>
      {/if}
    </div>
    <!-- Right side Header -->
    <slot name="header-title" />
  </CardHeader>
  <Separator class="w-9/12 mx-auto h-[1px] bg-slate-200 my-4" />
  <CardContent class="md:mx-10 px-0 sm:p-6">
    <!-- Content Top Side -->
    <div class="w-full flex flex-row md:flex-nowrap flex-wrap justify-between px-4 mb-8">
      <!-- Content Top-Left Side -->
      <div class="w-full md:mr-2">
        <!-- Issuer -->
        <div class="flex flex-col md:max-w-xs w-full gap-1.5 sm:mb-4 mb-10">
          <BusinessSelect bind:isLoading={isLoadingBusiness} {isEdit} />
          <div class={`${!$selectedBusiness ? "md:h-28" : ""}`}></div>
          <!-- Issuer/Business preview -->
          {#if !isLoadingBusiness && $selectedBusiness}
            <div
              class={`${
                !$selectedBusiness ? "invisible md:h-28" : "flex"
              } flex-row justify-between border-dashed border-gray-300
                border-2 p-2 rounded-md`}>
              <BusinessPreview data={$selectedBusiness} />
            </div>
          {:else}
            <div class="md:h-28"></div>
          {/if}
        </div>

        <!-- Customer -->
        <div class="flex flex-col md:max-w-[345px] w-full gap-1.5 sm:mb-4 mb-10">
          <Combobox
            bind:isModalOpen={isAddCustomerModalOpen}
            bind:selectedDataStore={$selectedClient}
            bind:dataStore={$client}
            bind:listStore={$clients}
            bind:wcListStore={$wcClients}
            bind:searchResult={$searchClientsResult}
            bind:searchWcResult={$searchWcClientsResult}
            bind:isWcData={isWcClient}
            bind:isSearchingState={isSearchingCustomerState}
            bind:isSearchingWcState={isSearchingWcCustomerState}
            selectedBind={$selectedClientBind}
            name="customer"
            api="client/list"
            detailApi="client/detail"
            perPage={5}
            wcApi="client/wc-clients" />
          <!-- Customer preview -->
          {#if $selectedClient && $selectedClient.id != 0}
            {#key $selectedClient}
              <div transition:slide class="flex flex-col border-dashed border-gray-300 border-2 p-2 rounded-md">
                <ClientPreview />
                <Separator class="my-2" />
                {#if $selectedClient.customAddress}
                  <div class="flex flex-col md:flex-row justify-center items-center ">
                    <div>
                      <Button variant="ghost" on:click={openCustomAddressModal} class="text-xs text-muted-foreground w-full">
                        Edit Custom Address
                      </Button>
                    </div>
                    <div>
                      <Button variant="ghost" on:click={removeCustomAddress} class="text-xs text-muted-foreground w-full">
                        Remove Custom Address
                      </Button>
                    </div>
                  </div>
                {:else}
                  <Button variant="ghost" on:click={openCustomAddressModal} class="text-xs text-muted-foreground">
                    Add Custom Address
                  </Button>
                {/if}
              </div>
            {/key}
          {/if}
        </div>

        <div class="flex flex-col md:max-w-[345px] w-full gap-1.5 sm:mb-4 mb-10">
          <Label>Bill to</Label>
          {#if !$billedToSameAsClient}
            <div class="space-y-3">
              <Input type="text" placeholder="Name" class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70" bind:value={$billedTo.name}/>
              <div>
              </div>
              <div>
                <Textarea placeholder="Additional Information" class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70" bind:value={$billedTo.detail}/>
              </div>
            </div>
          {/if}
          <div class="flex items-center space-x-2 mt-2">
            <Checkbox bind:checked={$billedToSameAsClient} id="terms" aria-labelledby="terms-label" />
            <Label id="terms-label" for="terms" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
              Same as customer
            </Label>
          </div>
        </div>
      </div>

      <!-- Content Top-Right Side -->
      <div class="w-full">
        <!-- Status -->
        <slot name="status" />
        <!-- Invoice, Due, & Order date -->
        <div class="flex flex-col gap-x-12 justify-end items-end">
          <!-- Order date & Invoice date -->
          <slot name="order-date" />
          <!-- Due date -->
          <div class={invoiceFormStyle}>
            <CustomSelect
              id="due-date"
              label="Due date"
              placeholder="Choose due date"
              selectionList={dueDateList}
              selected={selectedDueDateBind}
              onSelectedChange={updateDueDate} />
          </div>
          {#if $selectedDueDateInterval?.label === "Custom"}
            <slot name="due-date" />
          {/if}
        </div>
      </div>
    </div>

    <!-- Table Content -->
    <div class="px-2">
      <Table>
        <TableHeader>
          <TableRow class="flex flex-col items-center justify-center md:table-row bg-slate-100">
            <!-- Number -->
            <TableHead class="hidden md:table-cell w-[50px] rounded-tl-lg">#</TableHead>
            <!-- Product -->
            <TableHead
              class="min-w-[300px] text-lg md:text-sm font-bold md:font-medium flex items-center justify-center">
              Products
            </TableHead>
            <!-- Price -->
            <TableHead class="min-w-[150px] text-center hidden md:table-cell">Price</TableHead>
            <!-- Quantity -->
            <TableHead class="min-w-[55px] text-center hidden md:table-cell">Qty</TableHead>
            <!-- Amount -->
            <TableHead class="min-w-[130px] text-center hidden md:table-cell">Amount</TableHead>
            <!-- Action button -->
            <TableHead class="text-center w-[50px] hidden md:table-cell rounded-tr-lg"></TableHead>
          </TableRow>
        </TableHeader>

        <TableBody class="border-b-2">
          {#each $createdProductList as product, i}
            <!-- Created product -->
            <TableRow class="flex flex-col md:table-row md:p-0 py-6 border-b-0">
              <!-- on large screen, number & product are in different div -->
              <div transition:slide class="md:table-cell md:align-middle flex">
                <!-- Number -->
                <TableCell class="md:font-medium md:text-xs font-bold text-base">
                  {i + 1}.
                </TableCell>

                <!-- Product -->
                <!-- For Small screen -->
                <TableCell class="px-1 w-full md:hidden">
                  <div class="text-base font-serif">
                    {product.name}
                  </div>
                  <div class="flex justify-between items-center w-full">
                    <div class="text-xs mr-1 w-full">
                      {#if selectedDescriptionIndex === i}
                        <!-- To edit description -->
                        <div transition:slide>
                          <Textarea
                            class="w-full text-xs"
                            id="edit-description"
                            bind:value={product.description}
                            placeholder="product description..." />
                        </div>
                      {:else}
                        <!-- To show description -->
                        <MultilineText text={product.description} class="text-gray-500" isShowEmpty={true} />
                      {/if}
                    </div>
                    {#if selectedDescriptionIndex !== i}
                      <Tooltip>
                        <TooltipTrigger>
                          <Button
                            variant="link"
                            class="p-1 h-fit hover:bg-primary hover:text-white rounded-sm"
                            on:click={() => setSelectedDescription(i)}>
                            <Pencil2 size={16} />
                          </Button>
                        </TooltipTrigger>
                        <TooltipContent class="bg-background text-primary border border-primary">
                          Edit Description
                        </TooltipContent>
                      </Tooltip>
                    {/if}
                  </div>
                  {#if selectedDescriptionIndex === i}
                    <div class="flex justify-end w-full mt-2">
                      <Button class="mr-1" on:click={deleteSelectedDescription}>Save</Button>
                    </div>
                  {/if}
                </TableCell>
              </div>

              <!-- Product -->
              <!-- for Large screen -->
              <TableCell class="text-sm px-1 hidden md:table-cell justify-between w-full">
                <div transition:slide>
                  <div class="flex flex-nowrap items-center w-full">
                    <div class="font-serif w-full">
                      {product.name}
                    </div>
                    <!-- Edit description button -->
                    <div>
                      {#if selectedDescriptionIndex !== i}
                        <Tooltip>
                          <TooltipTrigger>
                            <Button
                              variant="link"
                              class="p-1 h-fit hover:bg-primary hover:text-white rounded-sm"
                              on:click={() => setSelectedDescription(i)}>
                              <Pencil2 size={16} />
                            </Button>
                          </TooltipTrigger>
                          <TooltipContent class="bg-background text-primary border border-primary">
                            Edit Descriptionƒ
                          </TooltipContent>
                        </Tooltip>
                      {/if}
                    </div>
                  </div>
                  <div class="text-[12px]">
                    <div class="mr-2">
                      <!-- To edit description -->
                      {#if selectedDescriptionIndex === i}
                        <div transition:slide>
                          <Textarea
                            class="w-full"
                            id="edit-description"
                            bind:value={product.description}
                            placeholder="product description..." />
                        </div>
                      {:else}
                        <MultilineText text={product.description} class="text-gray-500" isShowEmpty={true} />
                      {/if}
                    </div>
                  </div>
                  {#if selectedDescriptionIndex === i}
                    <div class="flex justify-end w-full mt-2">
                      <Button class=" mr-2 " on:click={deleteSelectedDescription}>Save</Button>
                    </div>
                  {/if}
                </div>
              </TableCell>

              <!-- Edit -->
              <!-- Unit price -->
              <TableCell class="text-center px-1 md:table-cell flex flex-row items-center">
                <div transition:slide class="w-full">
                  <div class="md:hidden mr-2 text-nowrap min-w-[70px]">Unit price:</div>
                  <CustomCurrencyInput
                    currencyName={$currencyPayload.name}
                    onValueChange={() => calculateAmount(i)}
                    placeholder="unit price"
                    bind:value={product.unitPrice} />
                </div>
              </TableCell>

              <!-- Edit -->
              <!-- Quantity -->
              <TableCell class="text-center px-1 md:table-cell flex flex-row items-center">
                <div transition:slide class="w-full">
                  <div class="md:hidden mr-2 min-w-[70px]">Quantity:</div>
                  <div>
                    <Input
                      type="number"
                      min="1"
                      class={`text-center ${removeArrowOnInputStyle}`}
                      placeholder={product.quantity.toString()}
                      bind:value={product.quantity}
                      on:change={() => calculateAmount(i)} />
                  </div>
                </div>
              </TableCell>

              <!-- Edit -->
              <!-- Amount -->
              <TableCell class=" text-center px-1 md:table-cell flex flex-row items-center cursor-not-allowed">
                <div transition:slide class="w-full">
                  <div class="md:hidden mr-2 text-nowrap min-w-[70px]">Amount:</div>
                  <div
                    class="flex justify-center w-full text-slate-400 border border-slate-200 border-solid
                    rounded h-[40px] items-center px-2">
                    {currencyFormatter($currencyPayload?.name, product.amount, 2)}
                  </div>
                </div>
              </TableCell>

              <!-- Action button -->
              <TableCell class="text-center pr-1 md:table-cell flex flex-row justify-end">
                <div transition:slide>
                  <Tooltip>
                    <TooltipTrigger>
                      <Button
                        type="reset"
                        variant="outline"
                        on:click={() => {
                          deleteProductFromList(i);
                        }}>
                        <Trash color="red" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent class="bg-background text-destructive border border-destructive">
                      Delete Product
                    </TooltipContent>
                  </Tooltip>
                </div>
              </TableCell>
            </TableRow>
          {/each}

          <!-- To create product -->
          <TableRow class="flex flex-col md:table-row md:p-0 py-6">
            <!-- Number -->
            <!-- for Large screen -->
            <TableCell class="md:font-medium md:text-xs md:table-cell font-bold text-base hidden">
              {$createdProductList.length + 1}.
            </TableCell>

            <!-- Product -->
            <TableCell class="px-1 md:table-cell md:w-full flex flex-row justify-center items-center">
              <!-- Number -->
              <!-- For Small screen -->
              <div class="md:hidden mr-2 text-nowrap font-bold text-base">
                {$createdProductList.length + 1}.
              </div>

              <Combobox
                bind:isModalOpen={isAddProductModalOpen}
                bind:selectedDataStore={selectedProductBind}
                bind:selectedBind={$selectedProductSelectBind}
                bind:dataStore={$product}
                bind:listStore={$products}
                bind:wcListStore={$wcProducts}
                bind:searchResult={$searchProductsResult}
                bind:searchWcResult={$searchWcProductsResult}
                bind:isWcData={isWcProduct}
                bind:isSearchingState={isSearchingProductState}
                bind:isSearchingWcState={isSearchingWcProductState}
                callback={updateSelectedProduct}
                name="product"
                api="product/list"
                perPage={5}
                wcApi="product/wc-products"
                hasLabel={false}>
                <div slot="item-right-side" class="text-muted-foreground text-xs" let:data>
                  {currencyFormatter(data?.currency?.name, data.price) ?? ""}
                </div>
              </Combobox>
            </TableCell>

            <!-- Create -->
            <!-- Unit price -->
            <TableCell class="px-1 md:table-cell flex flex-row items-center">
              <div class="md:hidden mr-2 text-nowrap min-w-[70px]">Unit price:</div>
              <CustomCurrencyInput
                currencyName={$currencyPayload.name}
                onValueChange={(e) => calculateAmount()}
                placeholder="unit price"
                bind:value={$createdProduct.unitPrice} />
            </TableCell>

            <!-- Create -->
            <!-- Quantity -->
            <TableCell class="px-1 md:table-cell flex flex-row items-center">
              <div class="md:hidden mr-2 text-nowrap min-w-[70px]">Quantity:</div>
              <Input
                type="number"
                min="1"
                placeholder="qty"
                class="text-center {removeArrowOnInputStyle}"
                on:change={calculateAmount}
                bind:value={$createdProduct.quantity} />
            </TableCell>

            <!-- Create -->
            <!-- Amount -->
            <TableCell class="px-1 md:table-cell flex flex-row items-center">
              <div class="md:hidden mr-2 text-nowrap min-w-[70px]">Amount:</div>
              <Input
                type="text"
                placeholder="amount"
                class="text-center {removeArrowOnInputStyle}"
                value={currencyFormatter($currencyPayload.name, $createdProduct.amount, 2)}
                disabled />
            </TableCell>

            <!-- Action button -->
            <TableCell class="text-center pr-1 flex flex-row justify-end md:table-cell">
              <Tooltip>
                <TooltipTrigger>
                  <Button
                    variant={$createdProduct.id === 0 ? "outline" : "default"}
                    class={$createdProduct.id === 0 ? "text-primary" : "text-background"}
                    on:click={addProductToList}>
                    <Plus />
                    <div class="md:hidden ml-1 md:ml-0">Add Product</div>
                  </Button>
                </TooltipTrigger>
                <TooltipContent class="bg-background text-primary border border-primary">Add Product</TooltipContent>
              </Tooltip>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- Content Bottom Side -->
    <div class="flex md:flex-row flex-col-reverse justify-between md:mt-20 mb-16 md:items-start items-center gap-x-2">
      <!-- Content Bottom-Left Side -->
      <div class="sm:w-[300px] w-10/12 md:mt-0 mt-20">
        <!-- Payment -->
        <div class="flex flex-col gap-y-4">
          <Label class="cursor-default text-base font-semibold">Payment Method</Label>
          <BankSelect />
          <PaypalSelect />
          <XenditSelect />
          <slot name="wc-payment" />
        </div>
      </div>

      <!-- Content Bottom-Right Side -->
      <div class="sm:w-[300px] w-10/12 space-y-4">
        <!-- Subtotal -->
        {#if $currencyPayload.name}
          <div class="flex flex-row justify-between items-center mt-10 md:mt-0">
            <p class="text-base font-semibold">Subtotal</p>
            <p class="text-base">{currencyFormatter($currencyPayload?.name, $subtotal)}</p>
          </div>
        {/if}
        <Separator class="my-4" />

        <!-- Discount -->
        {#if $selectedDiscount?.length > 0}
          <div transition:slide>
            {#each $selectedDiscount as discount, index}
              <div transition:slide class="text-sm text-red-600 cursor-pointer hover:text-[unset] flex flex-row justify-between w-full hover:text-red-800" on:click={() => handleTemporaryEditDiscount(index)}> 
                  <div>{discount.name} {discount.type === "percent" ? discount.value + "%" : ""}</div>
                  - {currencyFormatter(
                    $currencyPayload?.name,
                    calculateAdditionalFee($subtotal, discount.value, discount.type),
                  )}
              </div>
              {/each}
              <span class="text-xs text-[#777]">
                Click to adjust discount
              </span>
          </div>
          <Separator class="my-4" />
        {/if}

        <!-- Tax -->
        {#if $selectedTax?.length > 0}
          <div transition:slide>
            {#each $selectedTax as tax}
              <div transition:slide class="flex flex-row justify-between w-full text-sm">
                <div>{tax.name} {tax.type === "percent" ? tax.value + "%" : ""}</div>
                {currencyFormatter(
                  $currencyPayload?.name,
                  calculateAdditionalFee(subtotalAfterDiscount, tax.value, tax.type),
                )}
              </div>
            {/each}
          </div>
          <Separator class="my-4" />
        {/if}

        <!-- Total discount -->
        {#if $selectedDiscount?.length > 0}
          <div transition:slide class="flex flex-row justify-between w-full text-sm">
            <div>Total discount</div>
            <div>{currencyFormatter($currencyPayload?.name, $totalDiscount)}</div>
          </div>
        {/if}

        <!-- Total -->
        {#if $currencyPayload.name}
          <div class="flex flex-row justify-between items-center">
            <p class="text-xl font-bold">Total</p>
            <p class="text-base font-bold">{currencyFormatter($currencyPayload?.name, $total)}</p>
          </div>
        {/if}
      </div>
    </div>
  </CardContent>

  <CardFooter class="flex flex-col items-start sm:px-6 sm:mx-10">
    <!-- Note -->
    <div class="flex flex-col w-full gap-1.5 mb-8">
      <Label for="note">Note</Label>
      <Textarea id="note" placeholder="Fill note here" bind:value={$note} class="h-44" />
    </div>

    <!-- Terms & Conditions -->
    <div class="flex flex-row flex-nowrap justify-between items-center w-full gap-x-2">
      <div class="flex flex-col w-10/12 gap-1.5 mb-4">
        <Label>Terms & Conditions</Label>
        <Textarea
          class="h-[100px]"
          id="note"
          placeholder="Fill terms & conditions here"
          bind:value={$termsAndConditionsInvoice} />
      </div>
      <div class="flex flex-col justify-center items-center text-center">
        <img width="80" height="80" alt="example-qr-code" src="{invoize.plugin_url}public/example-qrcode.png" />
        <p>QR Code Preview</p>
      </div>
    </div>
  </CardFooter>
</Card>
