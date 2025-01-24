<script>
  let chart;
  import("svelte-apexcharts").then((module) => (chart = module.chart));
  import { Plus } from "radix-icons-svelte";
  import { RefreshCw } from "lucide-svelte";
  import { Separator } from "$lib/components/ui/separator";
  import { Button } from "$lib/components/ui/button";
  import { onMount } from "svelte";
  import { Calendar } from "lucide-svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";
  import { isDebug } from "$lib/stores/settings-store";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import moment from "moment";
  import ExpiredSoon from "$lib/components/dashboard/expired-soon.svelte";
  import NumberTransactionChart from "$lib/components/dashboard/number-transaction-chart.svelte";
  import TotalTransactionChart from "$lib/components/dashboard/total-transaction-chart.svelte";
  import Summary from "$lib/components/dashboard/summary.svelte";

  let chartCountOptions = {
    chart: {
      type: "area",
    },
    series: [
      {
        name: "2023",
        data: [30, 40, 35, 50, 49, 60, 30, 40, 35, 50, 49, 60],
      },
      {
        name: "2024",
        data: [45, 50, 30, 30, 80, 66, 45, 50, 30, 30, 80, 66],
      },
    ],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    },
  };

  let chartTotalOptions = {
    chart: {
      type: "area",
    },
    series: [
      {
        name: "2023",
        data: [30, 40, 35, 50, 49, 60, 30, 40, 35, 50, 49, 60],
      },
      {
        name: "2024",
        data: [45, 50, 30, 30, 80, 66, 45, 50, 30, 30, 80, 66],
      },
    ],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    },
  };

  const thisMonth = moment().format("MMM"); // eg: Jan, Feb, Mar
  const lastMonth = moment().subtract(1, "months").format("MMM"); // eg: Jan, Feb, Mar
  const thisYear = moment().format("YYYY"); // eg: 2024, 2025

  let data = {
    expired_soon: [],
    summary: {},
    default_currency: "",
  };
  let recurringData = {
    summary: {},
    upcoming_invoice: [],
  };

  let isLoadingRecurringSummary = true;
  let isLoadingUpcomingInvoice = true;
  let isLoadingExpiredSoon = true;
  let isLoadingSummary = true;
  let isSyncingSummary = false;
  let hasDefaultCurrency = false;
  let hasRecurringData = false;
  let now = new Date();
  let selectedCurrencyName = "USD";
  let selectedRecurringCurrencyName = "USD";
  let handleInvoiceCurrencyState;
  let handleRecurringCurrencyState;

  let totalTransactionThisMonth = 0;
  let numberTransactionThisMonth = 0;
  let totalPaidThisMonth;
  let totalPaidLastMonth;
  let totalUnpaidThisMonth;
  let totalUnpaidLastMonth;
  let percentPaidThisMonth;
  let percentUnpaidThisMonth;
  let totalRecurring;
  let numberOfRecurring;

  $: hours = now.getHours();
  $: minutes = now.getMinutes();
  $: seconds = now.getSeconds();
  $: hasDefaultCurrency && hasRecurringData && setRecurringDefaultCurrency();

  $: $isDebug && console.log({ data, recurringData });

  const getExpiredSoon = async () => {
    try {
      isLoadingExpiredSoon = true;
      const response = await createGetRequest("invoice/list-expired-soon");
      data.expired_soon = response.data.data;
      isLoadingExpiredSoon = false;
    } catch (err) {
      isLoadingExpiredSoon = false;
      handleError(err, "Failed to get list of expired soon invoices");
    }
  };

  const getSummary = async () => {
    try {
      isLoadingSummary = true;
      const response = await createGetRequest("invoice/monthly-summary");
      data.summary = response.data.data;
      data.default_currency = response.data.default_currency;
      setDefaultCurrency();
      setChartOption();
      isLoadingSummary = false;
      hasDefaultCurrency = true;
    } catch (err) {
      isLoadingSummary = false;
      handleError(err, "Failed to get invoice summary");
    }
  };

  const getRecurringSummary = async () => {
    try {
      isLoadingRecurringSummary = true;
      const response = await createGetRequest("recurring/summary");
      recurringData.summary = response.data.data;
      setRecurringSummary();
      isLoadingRecurringSummary = false;
      hasRecurringData = true;
    } catch (err) {
      isLoadingRecurringSummary = false;
      handleError(err, "Failed to get recurring summary");
    }
  };

  const getUpcomingInvoice = async () => {
    try { 
      isLoadingUpcomingInvoice = true;
      const response = await createGetRequest("recurring/upcoming-invoice");
      recurringData.upcoming_invoice = response.data.data;
      isLoadingUpcomingInvoice = false;
    } catch (err) {
      isLoadingUpcomingInvoice = false;
      handleError(err, "Failed to get upcoming invoice");
    }
  };

  // set selected currency with chosen default currency or first value of currency in the summary
  const setDefaultCurrency = () => {
    selectedCurrencyName =
      data?.default_currency && data.default_currency !== "none"
        ? data.default_currency
        : Object.keys(data?.summary)[0] ?? "USD";
    handleInvoiceCurrencyState(data?.summary, selectedCurrencyName);
    setTransactionThisMonth();
  };

  // set selected currency with chosen default currency or first value of currency in the summary
  const setRecurringDefaultCurrency = () => {
    let currencyExist = false;
    for (const currencyName of Object.keys(recurringData?.summary)) {
      if (currencyName === data.default_currency) {
        currencyExist = true;
      }
    }
    if (currencyExist) {
      selectedRecurringCurrencyName = data.default_currency;
    } else {
      selectedRecurringCurrencyName = Object.keys(recurringData?.summary)[0] ?? "USD";
    }
    handleRecurringCurrencyState(recurringData?.summary, selectedRecurringCurrencyName);
    setRecurringSummary();
  };

  const setTransactionThisMonth = () => {
    const currency = selectedCurrencyName;
    const thisYearMonthsList = data.summary[currency]?.count[thisYear];
    const transactionThisMonth = thisYearMonthsList ? thisYearMonthsList[thisMonth] : 0;

    totalPaidThisMonth = data.summary[currency]?.total[thisYear];
    totalPaidThisMonth = totalPaidThisMonth ? totalPaidThisMonth[thisMonth]?.paid ?? 0 : 0;

    totalPaidLastMonth = data.summary[currency]?.total[thisYear];
    totalPaidLastMonth = totalPaidLastMonth ? totalPaidLastMonth[lastMonth]?.paid ?? 0 : 0;

    totalUnpaidThisMonth = data.summary[currency]?.total[thisYear];
    totalUnpaidThisMonth = totalUnpaidThisMonth ? totalUnpaidThisMonth[thisMonth]?.unpaid ?? 0 : 0;

    totalUnpaidLastMonth = data.summary[currency]?.total[thisYear];
    totalUnpaidLastMonth = totalUnpaidLastMonth ? totalUnpaidLastMonth[lastMonth]?.unpaid ?? 0 : 0;

    totalTransactionThisMonth = totalPaidThisMonth + totalUnpaidThisMonth;
    numberTransactionThisMonth = (transactionThisMonth?.paid ?? 0) + (transactionThisMonth?.unpaid ?? 0);

    percentPaidThisMonth =
      totalPaidLastMonth === 0
        ? 0
        : Math.abs(((totalPaidThisMonth - totalPaidLastMonth) / totalPaidLastMonth) * 100).toFixed(1);

    percentUnpaidThisMonth =
      totalUnpaidLastMonth === 0
        ? 0
        : Math.abs(((totalUnpaidThisMonth - totalUnpaidLastMonth) / totalUnpaidLastMonth) * 100).toFixed(1);
  };

  const setRecurringSummary = () => {
    totalRecurring = recurringData.summary[selectedRecurringCurrencyName]?.total;
    numberOfRecurring = recurringData.summary[selectedRecurringCurrencyName]?.count;
  };

  const setChartOption = () => {
    const monthList = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const counts = { ...data.summary[selectedCurrencyName]?.count };
    const totals = { ...data.summary[selectedCurrencyName]?.total };
    let chartCountSeries = [];
    let chartTotalSeries = [];

    Object.entries(counts).forEach(([year, months]) => {
      let yearValues = { name: year.toString(), data: [] };
      const arrData = [null, null, null, null, null, null, null, null, null, null, null, null];
      Object.entries(months).forEach(([month, count]) => {
        // chartMonths.unshift(month);
        const index = monthList.indexOf(month);
        arrData[index] = ((count?.paid ?? 0) + (count?.unpaid ?? 0)).toFixed(2);
      });
      yearValues.data = arrData;
      chartCountSeries.push(yearValues);
    });

    Object.entries(totals).forEach(([year, months]) => {
      let yearValues = { name: year.toString(), data: [] };
      const arrData = [null, null, null, null, null, null, null, null, null, null, null, null];
      Object.entries(months).forEach(([month, total]) => {
        const index = monthList.indexOf(month);
        arrData[index] = ((total?.paid ?? 0) + (total?.unpaid ?? 0)).toFixed(2);
      });
      yearValues.data = arrData;
      chartTotalSeries.push(yearValues);
    });

    chartCountOptions = {
      chart: {
        type: "area",
      },
      series: chartCountSeries,
      xaxis: {
        categories: monthList,
      },
    };

    chartTotalOptions = {
      chart: {
        type: "area",
      },
      series: chartTotalSeries,
      xaxis: {
        categories: monthList,
      },
    };
  };

  const updateInvoiceDashboard = () => {
    setTransactionThisMonth();
    setChartOption();
  };

  const updateRecurringDashboard = () => {
    setRecurringSummary();
  };

  const syncSummary = () => {
    isSyncingSummary = true;
    createGetRequest("invoice/recalculate-summary")
      .then(() => {
        isSyncingSummary = false;
        getSummary();
        getRecurringSummary();
      })
      .catch((err) => {
        isSyncingSummary = false;
        handleError(err, "Failed to sync summary");
      });
  };

  onMount(() => {
    getSummary();
    getExpiredSoon();
    if(invoize.can_use_premium_code) {
      getUpcomingInvoice();
      getRecurringSummary();
    }

    const interval = setInterval(() => {
      now = new Date();
    }, 1000);

    return () => {
      clearInterval(interval);
    };
  });
</script>

<Breadcrumb />

<div class="w-full flex sm:flex-row flex-col sm:justify-between justify-center my-4 gap-y-4">
  <div class="flex flex-nowrap font-medium items-center sm:text-base text-xs text-nowrap gap-x-1 ml-1">
    <Calendar class="sm:w-5 w-4 sm:h-5 h-4 text-primary" />
    {moment().format(invoize.date_format)}
    <span class="text-primary mx-1"> | </span>
    {hours < 10 ? "0" + hours : hours}:{minutes < 10 ? "0" + minutes : minutes}:{seconds < 10 ? "0" + seconds : seconds}
  </div>
  <div class="flex flex-nowrap items-center gap-4">
    <Tooltip openDelay={0}>
      <TooltipTrigger>
        <Button size="icon" variant="link" on:click={syncSummary}>
          <RefreshCw class="w-5 cursor-pointer {isSyncingSummary ? 'animate-spin text-primary-200' : 'text-primary'}" />
        </Button>
      </TooltipTrigger>
      <TooltipContent class="bg-background text-primary border border-primary shadow-lg">
        Refresh Dashboard
      </TooltipContent>
    </Tooltip>
    <Button on:click={() => (location.href = `#/invoice/create`)}>
      <Plus />
      <div class="ml-2">Create Invoice</div>
    </Button>
    {#if invoize.can_use_premium_code}
    <Button on:click={() => (location.href = `#/recurring/create`)}>
      <Plus />
      <div class="ml-2">Create Recurring</div>
    </Button>
    {/if}
  </div>
</div>

<div class="flex xl:flex-row xl:flex-nowrap flex-col justify-between xl:items-start items-center gap-4">
  <!-- Left side -->
  <div class="xl:w-1/2 w-full space-y-4">
    <!-- Invoice -->
    <Summary
      bind:handleCurrencyState={handleInvoiceCurrencyState}
      on:updateCurrency={updateInvoiceDashboard}
      {isLoadingSummary}
      bind:selectedCurrencyName
      summaryData={data.summary}
      {numberTransactionThisMonth}
      {totalTransactionThisMonth}
      {totalPaidLastMonth}
      {totalPaidThisMonth}
      {percentPaidThisMonth}
      {percentUnpaidThisMonth}
      {totalUnpaidLastMonth}
      {totalUnpaidThisMonth} />

      {#if invoize.can_use_premium_code}
        <ExpiredSoon isLoading={isLoadingExpiredSoon} date={data.expired_soon} />
        <NumberTransactionChart {data} {isLoadingSummary} {chart} {chartCountOptions} {selectedCurrencyName} />
      {/if}
  </div>

  {#if !invoize.can_use_premium_code}
    <div class="xl:w-1/2 w-full flex 2xl:flex-row items-center xl:items-start flex-col gap-4">
      <div class="xl:min-w-[400px] w-full space-y-4">

        <ExpiredSoon isLoading={isLoadingExpiredSoon} date={data.expired_soon} />
        <NumberTransactionChart {data} {isLoadingSummary} {chart} {chartCountOptions} {selectedCurrencyName} />
        </div>
    </div>
  {/if}


  <!-- Right side -->
   {#if invoize.can_use_premium_code}
    <div class="xl:w-1/2 w-full flex 2xl:flex-row items-center xl:items-start flex-col gap-4">
      <div class="xl:min-w-[400px] w-full space-y-4">
        <!-- Recurring -->
        <Summary
          bind:handleCurrencyState={handleRecurringCurrencyState}
          on:updateCurrency={updateRecurringDashboard}
          {isLoadingSummary}
          bind:selectedCurrencyName={selectedRecurringCurrencyName}
          summaryData={recurringData.summary}
          numberTransactionThisMonth={numberOfRecurring}
          totalTransactionThisMonth={totalRecurring}
          {totalPaidLastMonth}
          {totalPaidThisMonth}
          {percentPaidThisMonth}
          {percentUnpaidThisMonth}
          {totalUnpaidLastMonth}
          {totalUnpaidThisMonth}
          isRecurring={true} />

        <!-- Upcoming invoice -->
        <ExpiredSoon isLoading={isLoadingUpcomingInvoice} date={recurringData.upcoming_invoice} isRecurring={true} />
        <TotalTransactionChart {data} {isLoadingSummary} {chart} {chartTotalOptions} {selectedCurrencyName} />

      </div>
    </div>
    {/if}

</div>

<!-- Footer -->
<Separator class="mt-4 mb-2" />
<div class="flex flex-row flex-nowrap justify-between">
  <div class="flex flex-col items-start">
    <Button variant="link" href="https://wpsora.com/docs/invoize/" target="_blank">Read Documentation</Button>
  </div>
  <div class="text-muted-foreground text-sm">Version {invoize.version}</div>
</div>
