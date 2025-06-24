<script>
  import {
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
  } from "$lib/stores/invoice-store";
  import { banks, automaticPaypals, directPaypals, xenditKey, xenditToken } from "$lib/stores/settings-store";
  import { Alert, AlertTitle, AlertDescription } from "$lib/components/ui/alert";
  import { Button } from "$lib/components/ui/button";
  import { activeTab1, activeTab2, activeTab3 } from "$lib/stores/settings-store";
  import { SettingsTab1 } from "$lib/common/enum";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { recurring } from "$lib/stores/recurring-store";
  import { Card, CardContent, CardHeader } from "$lib/components/ui/card";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Separator } from "$lib/components/ui/separator";
  import { Label } from "$lib/components/ui/label";
  import { dueDateRecurringCustom } from "$lib/stores/invoice-store";
  import { removeArrowOnInputStyle } from "$lib/common/styles";
  import { slide } from "svelte/transition";
  import { endTime } from "$lib/common/options";
  import { Plus } from "lucide-svelte";
  import { Input } from "$lib/components/ui/input";
  import { onMount } from "svelte";
  import RecurringHeaderTitle from "$lib/components/recurring-preview/header-title.svelte";
  import TemplateInvoice from "$lib/components/template/BaseCreate.svelte";
  import TemplateOptions from "$lib/components/template/Options.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  let createInvoiceModalComponent;

  export let nav;
  export let selectedDueDateBind;
  export let isSubmitting = false;
  export let isEdit = false;

  let isFinishModalOpen;
  let isCreateInvoiceModalOpen;
  let isLoadingSettings;
  let isLoadingPayment = false;
  let isDiscountModalOpen = false;
  let discountData;
  let discountTemporaryEdit;

  const goToSetting = () => {
    $activeTab1 = SettingsTab1.PAYMENT.VALUE;
    $activeTab2 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.VALUE;
    $activeTab3 = SettingsTab1.PAYMENT.TAB2.PAYMENT_METHOD.TAB3.BANK;
    window.location.href = "#/setting";
    window.scrollTo(0, 0);
  };

  onMount(() => {
    return () => {
      selectedDueDateBind = null;
      $selectedClient = null;
      $selectedClientBind = null;
      $selectedDueDateInterval = null;
      $createdProductList = [];
      $selectedTax = [];
      $selectedDiscount = [];
      $internalNote = null;
      $selectedReminderAfter = [];
      $selectedReminderBefore = [];
      $checkedDiscount = [];
      $checkedTax = [];
      $recurring = {
        name: null,
        interval: { value: null, label: null },
        start_date: null,
        end: null,
      };
    };
  });
</script>

{#if !isEdit && createInvoiceModalComponent}
  <svelte:component
    this="{createInvoiceModalComponent}"
    bind:open="{isCreateInvoiceModalOpen}"
    on:createInvoice
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
    on:settingsFetched
    on:updateDueDate
    bind:isLoadingSettings
    bind:isDiscountModalOpen
    bind:discountData
    bind:discountTemporaryEdit
    {selectedDueDateBind}
    {isEdit}
    bind:isLoadingPayment>
    <!-- Title -->
    <svelte:fragment slot="header-title">
      <RecurringHeaderTitle />
    </svelte:fragment>

    <!-- Due date -->
    <div slot="due-date" class="flex flex-row justify-start items-center md:max-w-xs w-full gap-2" transition:slide>
      <Input
        type="number"
        id="custom-due-date"
        min="1"
        bind:value="{$dueDateRecurringCustom}"
        class="{removeArrowOnInputStyle} w-full" />
      <div class="text-sm pr-2">day(s)</div>
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
    <Card slot="reminder" class="xl:w-[250px] w-full shadow-md rounded-md">
      <CardHeader class="pb-2 font-bold text-xl xl:text-base">Recurring Settings</CardHeader>
      <Separator class="xl:w-10/12 w-11/12 mx-auto mb-4" />
      <CardContent class="space-y-5">
        <!-- recurring name -->
        <div class="space-y-1">
          <Label for="interval-name">Recurring Name</Label>
          <Input id="interval-name" type="text" bind:value="{$recurring.name}" placeholder="Recurring name" />
        </div>

        <!-- recurring interval -->
        <div class="space-y-1">
          <Label for="interval">Repeat this invoice</Label>
          <Select bind:selected="{$recurring.interval}">
            <SelectTrigger id="interval">
              <SelectValue placeholder="Choose interval" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="daily" label="Daily">Daily</SelectItem>
              <SelectItem value="weekly" label="Weekly">Weekly</SelectItem>
              <SelectItem value="monthly" label="Monthly">Monthly</SelectItem>
              <SelectItem value="yearly" label="Yearly">Yearly</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- recurring start date -->
        <div class="space-y-1">
          <Label for="start-date">Start invoice on</Label>
          <Input type="date" class="relative" id="start-date" bind:value="{$recurring.start_date}" />
        </div>

        <!-- recurring end date -->
        <div class="space-y-1">
          <Label for="end">End invoice after</Label>
          <Select bind:selected="{$recurring.end}">
            <SelectTrigger id="at">
              <SelectValue placeholder="Choose end period" />
            </SelectTrigger>
            <SelectContent class="max-h-60 overflow-y-auto">
              {#each endTime as end}
                <SelectItem value="{end.value}" label="{end.label}">{end.label}</SelectItem>
              {/each}
            </SelectContent>
          </Select>
        </div>
      </CardContent>
    </Card>

    <button
      slot="save-options"
      on:click="{() => {
        import('$lib/components/recurring/create-invoice-modal.svelte').then((module) => {
          createInvoiceModalComponent = module.default;
          isFinishModalOpen = !isFinishModalOpen;
          isCreateInvoiceModalOpen = true;
        });
      }}"
      class="w-full px-5 py-3 rounded-sm text-sm font-medium flex items-center justify-center outline-none hover:bg-gradient-to-tr hover:from-primary hover:to-primary-500 hover:text-background">
      <Plus class="h-4 w-4 mr-1" />
      Save and Create Invoice
    </button>
  </TemplateOptions>
</div>
