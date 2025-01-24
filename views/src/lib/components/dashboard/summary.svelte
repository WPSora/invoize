<script>
  import { Card, CardContent, CardHeader, CardTitle } from "$lib/components/ui/card";
  import { Separator } from "$lib/components/ui/separator";
  import { BadgeDollarSign, Hash, Loader2, History } from "lucide-svelte";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";
  import { ArrowUp, ArrowDown } from "radix-icons-svelte";
  import { createEventDispatcher } from "svelte";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";
  import moment from "moment";
  import CurrencyChip from "$lib/components/dashboard/currency-chip.svelte";

  const dispatch = createEventDispatcher();

  export let isLoadingSummary;
  export let selectedCurrencyName;
  export let summaryData;
  export let numberTransactionThisMonth;
  export let totalTransactionThisMonth;
  export let totalPaidLastMonth;
  export let totalPaidThisMonth;
  export let percentPaidThisMonth;
  export let percentUnpaidThisMonth;
  export let totalUnpaidLastMonth;
  export let totalUnpaidThisMonth;
  export let isRecurring = false;

  /** @type {boolean[]} */
  let currencyState = [];

  export const handleCurrencyState = (summary, selectedCurrency) => {
    if (currencyState.length > 0) {
      currencyState = [];
    }
    for (const currency of Object.keys(summary)) {
      if (selectedCurrency == currency) {
        currencyState.push(true);
      } else {
        currencyState.push(false);
      }
    }
  };

  const handleToggleCurrency = (i, currencyName) => {
    currencyState = currencyState.map((item, idx) => {
      if (i === idx) return true;
      return false;
    });
    selectedCurrencyName = currencyName;
    dispatch("updateCurrency");
  };

  const generatSuffixForCurrencyChip = (currency) => {
    try {
      let data = summaryData[currency];

      if(! data) {
        return null;
      }

      if(isRecurring) {
        return `(${data.count})`
      }

      
      let currentData = data['count'][moment().year()][moment().format('MMM')]

      if(! currentData) {
        return null;
      }
  
      return `(${currentData.paid + currentData.unpaid})`;
    } catch (error) {
      console.log(`Error in generatSuffixForCurrencyChip: ${error}`);
      return null;
    }

  }
  
</script>

<Card>
  <CardHeader>
    <div class="flex justify-between items-center">
      <CardTitle class="text-primary sm:text-xl text-lg">
        <div>
          {#if isRecurring}
            Recurring Summary
          {:else}
            Invoice Summary
          {/if}
        </div>
        <!-- Month -->
        {#if !isRecurring}
          <div class="m-0 p-0 text-xs text-muted-foreground text-nowrap italic font-thin">
            {moment().format("MMMM YYYY")}
          </div>
        {/if}
      </CardTitle>
      <div class="flex flex-wrap justify-end gap-2 w-3/5">
        {#each Object.entries(summaryData) as [currency, value], i}
          <CurrencyChip
            currencyName={currency}
            suffix={generatSuffixForCurrencyChip(currency)}
            bind:isActive={currencyState[i]}
            on:click={() => handleToggleCurrency(i, currency)} />
        {/each}
      </div>
    </div>
    <Separator />
  </CardHeader>

  <CardContent class="space-y-3">
    <!-- Total Transaction -->
    <div class="flex flex-row justify-between text-sm bg-secondary p-4 rounded-lg">
      <div class="flex items-center">
        {#if isRecurring}
          <History class="sm:h-10 h-8 sm:w-10 w-8 text-amber-500" />
        {:else}
          <BadgeDollarSign class="sm:h-10 h-8 sm:w-10 w-8 text-green-600" />
        {/if}
      </div>
      <div>
        <div class="font-semibold text-end">
          {#if isRecurring}
            Total Active Recurring Value
          {:else}
            Total Transactions
          {/if}
        </div>
        <div
          class="gap-x-2 break-words flex justify-end sm:text-3xl text-lg font-bold {isRecurring
            ? 'text-amber-500'
            : 'text-green-600'}">
          {#if isLoadingSummary}
            <Loader2 class="h-8 w-8 animate-spin" />
          {:else}
            {currencyFormatter(selectedCurrencyName, totalTransactionThisMonth ?? 0)}
          {/if}
        </div>
      </div>
    </div>

    <!-- Number of transaction -->
    <div class="flex flex-row justify-between text-sm bg-secondary p-4 rounded-lg">
      <div class="flex items-center">
        <Hash class="sm:h-10 h-8 sm:w-10 w-8 text-primary" />
      </div>
      <div>
        <div class="font-semibold text-end">
          {#if isRecurring}
            Number of Active Recurring
          {:else}
            Number of Transactions
          {/if}
        </div>
        <div class="gap-x-2 break-words flex justify-end sm:text-3xl text-lg font-bold text-primary">
          {#if isLoadingSummary}
            <Loader2 class="h-8 w-8 animate-spin" />
          {:else}
            {numberTransactionThisMonth ?? 0}
          {/if}
        </div>
      </div>
    </div>

    <!-- Total Paid -->
    {#if !isRecurring}
      <div class="flex flex-row flex-wrap justify-between text-sm bg-secondary p-4 sm:space-y-0 space-y-2 rounded-lg">
        <div>
          <div class="font-semibold">Total Paid</div>
          <div class="text-muted-foreground text-[10px]">with percentage from last month</div>
        </div>
        <div class="flex flex-row flex-nowrap sm:w-fit w-full justify-end items-center text-end gap-x-2">
          {#if isLoadingSummary}
            <Loader2 class="h-6 w-6 animate-spin" />
          {:else}
            <div class="break-words sm:text-xl text-sm font-medium">
              {currencyFormatter(selectedCurrencyName, totalPaidThisMonth)}
            </div>
            <Separator orientation="vertical" class="bg-slate-300 mx-1" />
            <!-- it will be green and arrow up if paid this month is higher than paid last month -->
            <Tooltip openDelay={0}>
              <TooltipTrigger class="cursor-auto">
                <div
                  class="flex flex-nowrap items-center sm:text-xl text-sm font-bold {totalPaidThisMonth >
                    totalPaidLastMonth && totalPaidLastMonth !== 0
                    ? 'text-green-600'
                    : totalPaidThisMonth < totalPaidLastMonth
                      ? 'text-destructive'
                      : 'text-primary'}">
                  {percentPaidThisMonth}%
                  {#if totalPaidThisMonth > totalPaidLastMonth && totalPaidLastMonth !== 0}
                    <ArrowUp size={20} />
                  {:else if totalPaidThisMonth < totalPaidLastMonth}
                    <ArrowDown size={20} />
                  {:else}
                    <div></div>
                  {/if}
                </div>
              </TooltipTrigger>
              <TooltipContent class="bg-background text-primary border border-primary shadow-lg">
                Last month: {currencyFormatter(selectedCurrencyName, totalPaidLastMonth)}
              </TooltipContent>
            </Tooltip>
          {/if}
        </div>
      </div>

      <!-- Total Unpaid -->
      <div class="flex flex-row flex-wrap justify-between text-sm bg-secondary p-4 sm:space-y-0 space-y-2 rounded-lg">
        <div>
          <div class="font-semibold">Total Unpaid</div>
          <div class="text-muted-foreground text-[10px]">with percentage from last month</div>
        </div>
        <div class="flex flex-row flex-nowrap sm:w-fit w-full justify-end text-end items-center gap-x-2 relative">
          {#if isLoadingSummary}
            <Loader2 class="h-6 w-6 animate-spin" />
          {:else}
            <div class="break-words sm:text-xl text-sm font-medium">
              {currencyFormatter(selectedCurrencyName, totalUnpaidThisMonth)}
            </div>
            <Separator orientation="vertical" class="bg-slate-300 mx-1" />
            <!-- it will be red and arrow up if unpaid this month is higher than unpaid last month -->
            <Tooltip openDelay={0}>
              <TooltipTrigger class="cursor-auto">
                <div
                  class="flex flex-nowrap items-center sm:text-xl text-sm font-bold {totalUnpaidThisMonth >
                    totalUnpaidLastMonth && totalUnpaidLastMonth !== 0
                    ? 'text-destructive'
                    : totalUnpaidThisMonth < totalUnpaidLastMonth
                      ? 'text-green-600'
                      : 'text-primary'}">
                  {percentUnpaidThisMonth}%

                  {#if totalUnpaidThisMonth > totalUnpaidLastMonth && totalUnpaidLastMonth !== 0}
                    <ArrowUp size={20} />
                  {:else if totalUnpaidThisMonth < totalUnpaidLastMonth}
                    <ArrowDown size={20} />
                  {:else}
                    <div></div>
                  {/if}
                </div>
              </TooltipTrigger>
              <TooltipContent class="bg-background text-primary border border-primary shadow-lg">
                Last month: {currencyFormatter(selectedCurrencyName, totalUnpaidLastMonth)}
              </TooltipContent>
            </Tooltip>
          {/if}
        </div>
      </div>
    {/if}
  </CardContent>
</Card>
