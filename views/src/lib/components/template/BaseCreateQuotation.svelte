<script>
  import {
    selectedBusiness,
    selectedClientBind,
    selectedDueDateInterval,
    createdProductList,
    internalNote,
    selectedDiscount,
    selectedTax,
    selectedReminderBefore,
    selectedReminderAfter,
    selectedClient,
    checkedTax,
    checkedDiscount,
    invoiceNumber,
    isWcPaymentChecked,
    wcPayment,
  } from "$lib/stores/invoice-store";
  import { Alert, AlertTitle, AlertDescription } from "$lib/components/ui/alert";
  import { CardTitle } from "$lib/components/ui/card";
  import { slide } from "svelte/transition";
  import { Button } from "$lib/components/ui/button";
  import { invoiceFormStyle } from "$lib/common/styles";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { activeTab1, activeTab2, activeTab3 } from "$lib/stores/settings-store";
  import { SettingsTab1 } from "$lib/common/enum";
  import {
    prefix,
    startFromNumber,
    banks,
    automaticPaypals,
    directPaypals,
    xenditKey,
    xenditToken,
  } from "$lib/stores/settings-store";
  import { Send, Loader2 } from "lucide-svelte";
  import { onMount } from "svelte";
  import moment from "moment";
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import TemplateInvoice from "$lib/components/template/BaseCreate.svelte";
  import TemplateOptions from "$lib/components/template/Options.svelte";
  import { createGetRequest } from "$lib/helpers/request";
  let sendInvoiceModalComponent;

  export let nav;
  export let selectedQuotationDate = moment().format("YYYY-MM-DD");
  export let selectedDueDate;
  export let selectedDueDateBind = null;
  export let selectedDueDateCustom;
  export let isSubmitting = false;
  export let isEdit = false;

  let isFinishModalOpen = false;
  let isSendInvoiceConfirmationOpen = false;
  let isLoadingSettings = false;
  let isLoadingPayment = false;
  let isDiscountModalOpen = false;
  let discountData;
  let discountTemporaryEdit;

  // update due date if user change invoice date
  $: {
    const days = $selectedDueDateInterval?.value;
    if (days) {
      selectedDueDate = moment(selectedQuotationDate).add(days, "days").format("YYYY-MM-DD");
    }
  }

  const updateDueDate = (e) => {
    selectedDueDate = null;
    selectedDueDateBind = null;
  };

  const goToSetting = () => {
    $activeTab1 = SettingsTab1.PAYMENT.VALUE;
    $activeTab2 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.VALUE;
    $activeTab3 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.TAB3.BANK;
    window.location.href = "#/setting";
    window.scrollTo(0, 0);
  };

  const handleSettingFetch = () => {
    createGetRequest("settings/retrieve?tab=quotation", (res) => {
      const settings = res.data.data;
      settings.map((item) => {
        if (item.name == "prefix") {
          $prefix = item.value;
        }
        if (item.name == "startFromNumber") {
          $startFromNumber = item.value;
        }
      });
    });
  };

  onMount(() => {
    return () => {
      $selectedBusiness = null;
      $selectedClient = null;
      $selectedClientBind = null;
      $createdProductList = [];
      $selectedTax = [];
      $selectedDiscount = [];
      $internalNote = null;
      $selectedReminderAfter = [];
      $selectedReminderBefore = [];
      $selectedDueDateInterval = null;
      $checkedDiscount = [];
      $checkedTax = [];
      $isWcPaymentChecked = false;
      $wcPayment = null;
    };
  });
</script>

{#if !isEdit && sendInvoiceModalComponent}
  <svelte:component
    this="{sendInvoiceModalComponent}"
    bind:open="{isSendInvoiceConfirmationOpen}"
    on:createAndSend
    isLoading="{isSubmitting}" />
{/if}

<Breadcrumb to="{nav}" />

{#if !isLoadingPayment && isEmptyCheck($banks) && isEmptyCheck($automaticPaypals) && isEmptyCheck($directPaypals) && (!$xenditKey || !$xenditToken)}
  <Alert variant="warning" class="mt-4">
    <AlertTitle>Missing Payment Method</AlertTitle>
    <AlertDescription>Please go to settings and manage your payment method</AlertDescription>
    <Button variant="link" on:click="{goToSetting}" class="text-xs p-0">Go to Settings</Button>
  </Alert>
{/if}

<div class="flex xl:flex-row xl:flex-nowrap flex-wrap justify-center mt-5">
  <TemplateInvoice
    on:updateDueDate
    on:settingsFetched="{handleSettingFetch}"
    bind:isLoadingSettings
    bind:isDiscountModalOpen
    bind:discountData
    bind:discountTemporaryEdit
    {isEdit}
    {selectedDueDateBind}
    bind:isLoadingPayment>
    <!-- Title -->
    <div slot="header-title" class="flex flex-col items-end">
      <CardTitle class="sm:text-[42px] text-[24px] font-bold mb-2">QUOTATION</CardTitle>
      {#if isLoadingSettings}
        <Loader2 class="h-5 w-5 animate-spin text-primary mt-1" />
      {:else}
        <p class="text-2xl font-bold">
          {#if isEdit}
            {$invoiceNumber}
          {:else}
            {$prefix}{$startFromNumber}
          {/if}
        </p>
      {/if}
    </div>

    <!-- Order date -->
    <svelte:fragment slot="order-date">
      <div class="{invoiceFormStyle}">
        <Label for="quotation-date">Quotation date</Label>
        <Input
          id="quotation-date"
          type="date"
          placeholder="Quotation date"
          bind:value="{selectedQuotationDate}"
          class="relative" />
      </div>
    </svelte:fragment>

    <!-- Custom Due date -->
    <div slot="due-date" class="{invoiceFormStyle}" transition:slide>
      <Input id="due-date" type="date" class="relative" bind:value="{selectedDueDateCustom}" />
    </div>
  </TemplateInvoice>

  <TemplateOptions
    on:submit
    bind:isFinishModalOpen
    {isEdit}
    {isLoadingSettings}
    bind:isDiscountModalOpen
    bind:discountData
    bind:discountTemporaryEdit>
    <!-- arrow button -->
    <button
      slot="save-options"
      on:click="{() => {
        import('$lib/components/invoice/send-invoice-modal.svelte').then((module) => {
          sendInvoiceModalComponent = module.default;
          isFinishModalOpen = !isFinishModalOpen;
          isSendInvoiceConfirmationOpen = true;
        });
      }}"
      class="w-full px-5 py-3 rounded-sm text-sm font-medium flex items-center justify-center outline-none hover:bg-gradient-to-tr hover:from-primary hover:to-primary-500 hover:text-background">
      <Send class="h-4 w-4 mr-3" />
      Save and Send E-mail
    </button>
  </TemplateOptions>
</div>
