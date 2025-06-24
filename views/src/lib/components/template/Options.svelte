<script>
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger, Value } from "$lib/components/ui/select";
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "$lib/components/ui/accordion";
  import { HoverCard, HoverCardTrigger, HoverCardContent } from "$lib/components/ui/hover-card";
  import { Card, CardContent, CardHeader } from "$lib/components/ui/card";
  import { ChevronDown, Loader2, Send } from "lucide-svelte";
  import { Plus } from "radix-icons-svelte";
  import { Button } from "$lib/components/ui/button";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Separator } from "$lib/components/ui/separator";
  import { Textarea } from "$lib/components/ui/textarea";
  import { createEventDispatcher } from "svelte";
  import { currencies, taxes, discounts, reminders, isPro, isProPopupOpen } from "$lib/stores/settings-store";
  import {
    internalNote,
    currencyPayload,
    selectedDiscount,
    selectedTax,
    selectedReminderAfter,
    selectedReminderBefore,
    checkedTax,
    checkedDiscount,
    reminderForAdmin,
    reminderForClient,
  } from "$lib/stores/invoice-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import { Label } from "$lib/components/ui/label";
  import DiscountModal from "$lib/components/invoice/add-discount-modal.svelte";
  import TaxModal from "$lib/components/invoice/add-tax-modal.svelte";
  import ReminderModal from "$lib/components/invoice/add-reminder-modal.svelte";
  import CardDescription from "$lib/components/ui/card/card-description.svelte";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";

  const dispatch = createEventDispatcher();
  const accordionOpen = ["currency", "reminder"];

  export let isFinishModalOpen = false;
  export let isEdit = false;
  export let isLoadingSettings = false;
  export let isDiscountModalOpen = false;
  export let discountData;
  export let discountTemporaryEdit;

  let isTaxModalOpen = false;
  let isReminderModalOpen = false;
  let isLoadingDiscount = false;
  let isLoadingTax = false;
  let selectedCurrency;

  $: selectedCurrency = { label: $currencyPayload.name, value: $currencyPayload.name };

  // set selectedCurrency payload value
  const updateSelectedCurrency = (e) => {
    const selected = $currencies.find((item) => item.name === e.value);
    $currencyPayload = { name: selected.name, symbol: selected.symbol };
  };

  const updateSelectedDiscounts = (discount, isChecked) => {
    $selectedDiscount = isChecked
      ? [...$selectedDiscount, discount]
      : $selectedDiscount.filter((item) => item.name !== discount.name);
  };

  const updateSelectedTaxes = (tax, isChecked) => {
    $selectedTax = isChecked ? [...$selectedTax, tax] : $selectedTax.filter((item) => item.name !== tax.name);
  };

  const updateSelectedReminderBefore = (reminder, isChecked) => {
    $selectedReminderBefore = isChecked
      ? [...$selectedReminderBefore, reminder]
      : $selectedReminderBefore.filter((item) => item !== reminder);
  };

  const updateSelectedReminderAfter = (reminder, isChecked) => {
    $selectedReminderAfter = isChecked
      ? [...$selectedReminderAfter, reminder]
      : $selectedReminderAfter.filter((item) => item !== reminder);
  };

  const updateReminderForClient = (isChecked) => {
    $reminderForClient = isChecked;
    checkToDisableReminder();
  };

  const updateReminderForAdmin = (isChecked) => {
    $reminderForAdmin = isChecked;
    checkToDisableReminder();
  };

  const checkToDisableReminder = () => {
    if (!$reminderForAdmin && !$reminderForClient) {
      $selectedReminderAfter = [];
      $selectedReminderBefore = [];
    }
  };

  const openTaxModal = () => (isTaxModalOpen = true);
  const openDiscountModal = () => (isDiscountModalOpen = true);
  const openReminderModal = () => (isReminderModalOpen = true);
</script>

<TaxModal bind:isOpen="{isTaxModalOpen}" isLoading="{isLoadingTax}" />
<DiscountModal
  bind:isOpen="{isDiscountModalOpen}"
  bind:isLoading="{isLoadingDiscount}"
  bind:payload="{discountData}"
  bind:temporaryEdit="{discountTemporaryEdit}" />
<ReminderModal bind:open="{isReminderModalOpen}" />

<div class="sticky xl:w-[250px] w-full top-16 space-y-5 h-fit xl:ml-6 xl:mt-0 mt-4 mx-2 md:mx-0">
  <!-- Internal Note -->
  <Card class="xl:w-[250px] w-full shadow-md rounded-md">
    <CardHeader>
      <Label for="internal-note">Internal note</Label>
      <CardDescription>
        <Textarea id="internal-note" bind:value="{$internalNote}" placeholder="internal note here..." />
      </CardDescription>
    </CardHeader>
  </Card>

  <slot name="reminder" />

  <!-- Options -->
  <Card class="xl:w-[250px] shadow-md w-f-ull h-fit rounded-md">
    <CardHeader class="pb-2 font-bold text-xl xl:text-base">Additional Options</CardHeader>
    <Separator class="xl:w-10/12 w-11/12 mx-auto" />
    <CardContent>
      <div class="flex flex-col w-full gap-2">
        <Accordion multiple value="{accordionOpen}">
          <!-- Currency -->
          <AccordionItem value="currency">
            <AccordionTrigger class="text-base">Currency</AccordionTrigger>
            {#if isLoadingSettings}
              <AccordionContent>
                <div class="flex flex-row flex-nowrap items-center justify-center gap-1">
                  <Loader2 class="h-4 w-4 text-primary animate-spin" />
                  <div class="text-xs text-muted-foreground">Fetching settings</div>
                </div>
              </AccordionContent>
            {:else}
              <AccordionContent class="p-1">
                <Select required onSelectedChange="{updateSelectedCurrency}" bind:selected="{selectedCurrency}">
                  <SelectTrigger id="currency">
                    <SelectValue placeholder="Select currency" />
                  </SelectTrigger>
                  <SelectContent class="max-h-60 overflow-y-auto">
                    {#if !isEmptyCheck($currencies)}
                      {#each $currencies as currency}
                        <SelectItem value="{currency.name}" label="{currency.name}" class="flex justify-between">
                          <div>
                            {currency.name}
                          </div>
                          <div>
                            {@html currency.symbol}
                          </div>
                        </SelectItem>
                      {/each}
                    {:else}
                      <div class="p-2 text-sm text-muted-foreground">
                        <div>No currency saved</div>
                        <a href="#/setting" class="text-xs text-blue-500">Go to Setting</a>
                      </div>
                    {/if}
                  </SelectContent>
                </Select>
              </AccordionContent>
            {/if}
          </AccordionItem>

          <!-- Discount -->
          <AccordionItem value="discount">
            <AccordionTrigger class="text-base">Discount</AccordionTrigger>
            <AccordionContent>
              {#if isEmptyCheck($discounts)}
                <div class="italic text-muted-foreground mb-2">No saved discount option</div>
              {:else}
                {#each $discounts as discount, i}
                  <!-- If fixed value, only show discount based on selected currency -->
                  {#if discount?.currency?.name === $currencyPayload.name || discount.type === "percent"}
                    <div class="flex flex-row items-center gap-x-2 mb-3">
                      <!-- bind:checked updated the checked is check or not -->
                      <!-- onCheckedChange updated the actualy value that get send as payload -->
                      <Checkbox
                        id="{discount.name}-{discount.value}-{i}"
                        bind:checked="{$checkedDiscount[i]}"
                        onCheckedChange="{(isChecked) => updateSelectedDiscounts(discount, isChecked)}" />
                      <Label for="{discount.name}-{discount.value}-{i}">
                        {discount.name}
                      </Label>
                    </div>
                  {/if}
                {/each}
              {/if}
              {#if !$isPro && $discounts.length >= 3}
                <div class="flex justify-center w-full">
                  <UpgradeToProButton bind:isProPopupOpen="{$isProPopupOpen}" customText="Add more with Pro" />
                </div>
              {:else}
                <Button variant="link" class="w-full" on:click="{openDiscountModal}">
                  <Plus />
                  <div class="ml-2">New discount</div>
                </Button>
              {/if}
            </AccordionContent>
          </AccordionItem>

          <!-- Tax -->
          <AccordionItem value="tax">
            <AccordionTrigger class="text-base">Tax</AccordionTrigger>
            <AccordionContent>
              {#if isEmptyCheck($taxes)}
                <div class="italic text-muted-foreground mb-2">No saved tax option</div>
              {:else}
                {#each $taxes as tax, i}
                  <!-- If fixed value, only show tax based on selected currency -->
                  {#if tax?.currency?.name === $currencyPayload.name || tax.type === "percent"}
                    <div class="flex flex-row items-center gap-x-2 mb-3">
                      <!-- bind:checked updated the checked is check or not -->
                      <!-- onCheckedChange updated the actualy value that get send as payload -->
                      <Checkbox
                        id="{tax.name}-{tax.value}-{i}"
                        bind:checked="{$checkedTax[i]}"
                        onCheckedChange="{(isChecked) => updateSelectedTaxes(tax, isChecked)}" />
                      <Label for="{tax.name}-{tax.value}-{i}">{tax.name}</Label>
                    </div>
                  {/if}
                {/each}
              {/if}
              {#if !$isPro && $taxes.length >= 3}
                <div class="flex justify-center w-full">
                  <UpgradeToProButton bind:isProPopupOpen="{$isProPopupOpen}" customText="Add more with Pro" />
                </div>
              {:else}
                <Button variant="link" class="w-full" on:click="{openTaxModal}">
                  <Plus />
                  <div class="ml-2">New tax</div>
                </Button>
              {/if}
            </AccordionContent>
          </AccordionItem>

          <!-- Reminder -->
          <AccordionItem value="reminder">
            <AccordionTrigger class="text-base">Reminder</AccordionTrigger>
            {#if isLoadingSettings}
              <AccordionContent>
                <div class="flex flex-row flex-nowrap items-center justify-center gap-1">
                  <Loader2 class="h-4 w-4 text-primary animate-spin" />
                  <div class="text-xs text-muted-foreground">Fetching settings</div>
                </div>
              </AccordionContent>
            {:else}
              <AccordionContent>
                {#if isEmptyCheck($reminders)}
                  <div class="italic text-muted-foreground mb-2">No saved reminder option</div>
                {:else}
                  {@const isDisableReminder = !$reminderForAdmin && !$reminderForClient}
                  <div class="space-y-4">
                    <div class="flex gap-2">
                      <Checkbox
                        id="reminder-for-client"
                        checked="{$reminderForClient}"
                        onCheckedChange="{(isChecked) => {
                          updateReminderForClient(isChecked);
                        }}" />
                      <Label for="reminder-for-client">For customer</Label>
                    </div>
                    <div class="flex gap-2">
                      <Checkbox
                        id="reminder-for-admin"
                        checked="{$reminderForAdmin}"
                        onCheckedChange="{(isChecked) => {
                          updateReminderForAdmin(isChecked);
                        }}" />
                      <Label for="reminder-for-admin">For WP admin</Label>
                    </div>
                    <Separator />
                    <div class="flex flex-row justify-between mb-3 gap-3">
                      <!-- Reminder Before -->
                      <div class="space-y-3">
                        <div class="font-medium w-full">Before Due Date</div>
                        {#each $reminders as reminder}
                          <div class="flex flex-row items-center gap-x-2 mb-3">
                            <Checkbox
                              id="{reminder + ' before'}"
                              disabled="{isDisableReminder}"
                              class="{isDisableReminder ? 'border-slate-400' : ''}"
                              checked="{$selectedReminderBefore?.includes(reminder) ?? false}"
                              onCheckedChange="{(isChecked) => {
                                updateSelectedReminderBefore(reminder, isChecked);
                              }}" />
                            <Label for="{reminder + ' before'}" class="{isDisableReminder ? 'text-slate-400' : ''}"
                              >{pluralHelper(reminder)}</Label>
                          </div>
                        {/each}
                      </div>
                      <!-- Reminder After -->
                      <div class="space-y-3">
                        <div class="font-medium w-full">After Due Date</div>
                        {#each $reminders as reminder}
                          <div class="flex flex-row items-center gap-x-2 mb-3">
                            <Checkbox
                              id="{reminder + ' after'}"
                              disabled="{isDisableReminder}"
                              class="{isDisableReminder ? 'border-slate-400' : ''}"
                              checked="{$selectedReminderAfter?.includes(reminder) ?? false}"
                              onCheckedChange="{(isChecked) => {
                                updateSelectedReminderAfter(reminder, isChecked);
                              }}" />
                            <Label for="{reminder + ' after'}" class="{isDisableReminder ? 'text-slate-400' : ''}"
                              >{pluralHelper(reminder)}</Label>
                          </div>
                        {/each}
                      </div>
                    </div>
                  </div>
                  <Button variant="link" class="w-full" on:click="{openReminderModal}">
                    <Plus />
                    <div class="ml-2">New reminder</div>
                  </Button>
                {/if}
              </AccordionContent>
            {/if}
          </AccordionItem>
        </Accordion>

        <!-- Save button -->
        <div class="flex flex-nowrap justify-center z-10">
          <div
            class="flex justify-center items-center xl:w-full md:w-1/4 w-full transition-all"
            id="finish-button-wrap">
            <button
              type="submit"
              on:click="{() => dispatch('submit')}"
              class="finish-button h-12 font-bold text-lg text-white {!isEdit
                ? 'rounded-l-md w-full'
                : 'rounded-md w-full'}">
              Save
            </button>
            {#if !isEdit}
              <HoverCard bind:open="{isFinishModalOpen}">
                <HoverCardTrigger>
                  <button
                    on:click="{() => {
                      isFinishModalOpen = !isFinishModalOpen;
                    }}"
                    class="finish-button rounded-l-none h-12 w-10 p-0 rounded-r-md flex flex-row justify-center items-center">
                    <ChevronDown color="white" />
                  </button>
                </HoverCardTrigger>
                <HoverCardContent
                  class="w-fit sm:ml-[-60px] p-1 cursor-pointer bg-gradient-to-t from-slate-50 to-white">
                  <slot name="save-options" />
                </HoverCardContent>
              </HoverCard>
            {/if}
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</div>

<style>
  #finish-button-wrap {
    padding: 0px;
    margin: 16px;
    border-radius: 5px;
    box-shadow:
      inset 0 0 0px 0px white,
      inset 0 0 0px 0px white;
  }

  .finish-button {
    background: linear-gradient(to bottom left, #904dff, #4100ae);
    background-size: 600%;
    background-position: 50% 50%;
    box-shadow: 0 0 5px 0 linear;
    /* background-color: #c026d3; */
  }

  #finish-button-wrap:hover {
    margin: 0px;
    padding: 16px;
    box-shadow:
      inset 0 0 12px 12px white,
      inset 0 0 3px 2px white;
    background: linear-gradient(to top right, #904dff, #4100ae, #904dff, #4100ae);
    background-size: 600%;
    animation-name: finish-wrap;
    animation-duration: 2s;
    animation-iteration-count: infinite;
  }
  .finish-button:hover {
    animation-name: finish;
    animation-duration: 2s;
    animation-iteration-count: infinite;
  }

  @keyframes finish-wrap {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  @keyframes finish {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }
</style>
