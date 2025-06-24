<script>
  import { createGetRequest } from "$lib/helpers/request";
  import { Loader2 } from "lucide-svelte";
  import { Tabs, TabsContent, TabsTrigger, TabsList } from "$lib/components/ui/tabs";
  import {
    activeTabPagination,
    inactiveTabPagination,
    invoiceTabPagination,
    searchTabPagination,
    activeTabData,
    inactiveTabData,
    invoiceTabData,
    searchTabData,
    hasActiveTabData,
    hasInvoiceTabData,
    hasInactiveTabData,
    hasSearchTabData,
    searchQuery,
  } from "$lib/stores/recurring-store";
  import { isCreatingNewInvoice } from "$lib/stores/invoice-store";
  import {
    receiptListBaseTabStyle,
    paidTabClass,
    unpaidTabClass,
    expiredTabClass,
    searchTabClass,
  } from "$lib/common/styles";
  import { setTableColumns } from "$lib/components/recurring-list/recurring-table-column";
  import { setSearchTableColumns } from "$lib/components/recurring-list/recurring-search-table-column";
  import { setAllTabTableColumns } from "$lib/components/invoice-list/invoice-all-tab-table-column";
  import { RecurringListTab } from "$lib/common/enum";
  import { Button } from "$lib/components/ui/button";
  import { onMount } from "svelte";
  import { Plus } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { formatLongNumber } from "$lib/helpers/longNumberFormatter";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import Tab from "$lib/components/custom-ui/Tab.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import { recurringListPage } from "$lib/stores/navigation";
  import { recurringTab } from "$lib/stores/active-tab-store";


  export let params = {};

  const nav = [
    { name: "Recurring", link: "recurrings" },
    { name: "List", link: "" },
  ];

  let customerDetail = {};
  let isSearching = false;
  let isActiveTabLoading = false;
  let isInactiveTabLoading = false;
  let isInvoiceTabLoading = false;
  let isSearchTabLoading = false;
  let alwaysReload = true;

  let activeTab;
  let inactiveTab;
  let invoiceTab;
  let searchTab;
  // if tabClickCount is more than 1, it means user clicking on the same tab and need to refresh the tab
  let tabClickCount = 1;

  const updateTabList = (isUpdateInvoiceTab) => {
    activeTab.getListItems();
    inactiveTab.getListItems();
    $hasSearchTabData && searchTab.getListItems();
    isUpdateInvoiceTab && invoiceTab.getListItems();
  };

  const updateSpecificTabList = (tab) => {
    if (tab === RecurringListTab.ACTIVE.LOWER_CASE) {
      activeTab.getListItems();
    } else if (tab === RecurringListTab.INACTIVE.LOWER_CASE) {
      inactiveTab.getListItems();
    } else if (tab === RecurringListTab.INVOICE.LOWER_CASE) {
      invoiceTab.getListItems();
    } else if (tab === RecurringListTab.SEARCH.LOWER_CASE) {
      searchTab.getListItems();
    }
  };

  const updateActiveTab = (e) => {
    tabClickCount = 0;
    $recurringTab = e;
  };

  const refreshTab = (tab) => {
    tabClickCount++;
    if (tabClickCount > 1) {
      updateSpecificTabList(tab);
    }
  };

  const handleSearchRecurring = () => {
    $hasSearchTabData = false;
    if (!$searchQuery) {
      $searchTabData = [];
      $recurringTab = $recurringTab === "search" ? "active" : $recurringTab;
      return;
    }
    isSearching = true;
    createGetRequest(`recurring/recurring-list?id=${params.clientId}&search=${$searchQuery}`).then((response) => {
      const { page, per_page, total_items, total_pages, items } = response.data;
      $searchTabData = items;
      $searchTabPagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      $hasSearchTabData = true;
      $recurringTab = "search";
      isSearching = false;
    });
  };

  const getClientRecurringDetail = async (clientId) => {
    try {
      const response = await createGetRequest(`recurring/client-recurring-detail?id=${clientId}`);
      customerDetail = response.data;
    } catch (err) {
      handleError(err, "Failed to get client recurring detail.");
    }
  };

  onMount(() => {
    getClientRecurringDetail(params.clientId);
  });
</script>

<Breadcrumb to={nav} />
<PageTitle title="Recurring Invoice" description={`Customer: ${customerDetail?.name ?? '-'}`} hasDivider={false} />

<Tabs bind:value={$recurringTab} onValueChange={updateActiveTab}>
  <TabsList
    class="bg-background w-full h-fit flex sm:flex-row flex-col-reverse justify-between sm:items-center items-end px-2 gap-x-2 gap-y-4 rounded-md">
    <div class="lg:flex hidden justify-around {$hasSearchTabData ? 'w-[600px]' : 'w-[500px]'} flex-nowrap gap-2">
      <TabsTrigger
        value={RecurringListTab.ACTIVE.LOWER_CASE}
        class="{receiptListBaseTabStyle} {paidTabClass}"
        on:click={() => refreshTab(RecurringListTab.ACTIVE.LOWER_CASE)}>
        Active
        <!-- number badge -->
        <div class="rounded-full bg-green-600 text-background text-center min-w-8 w-fit px-0.5 ml-3">
          {#if isActiveTabLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($activeTabPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value={RecurringListTab.INACTIVE.LOWER_CASE}
        class="{receiptListBaseTabStyle} {unpaidTabClass}"
        on:click={() => refreshTab(RecurringListTab.INACTIVE.LOWER_CASE)}>
        Inactive
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isInactiveTabLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($inactiveTabPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value={RecurringListTab.INVOICE.LOWER_CASE}
        class="{receiptListBaseTabStyle} {expiredTabClass}"
        on:click={() => refreshTab(RecurringListTab.INVOICE.LOWER_CASE)}>
        Created Invoice
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isInvoiceTabLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($invoiceTabPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      {#if $hasSearchTabData}
        <TabsTrigger
          value={RecurringListTab.SEARCH.LOWER_CASE}
          class="{receiptListBaseTabStyle} {searchTabClass}"
          on:click={() => refreshTab(RecurringListTab.SEARCH.LOWER_CASE)}>
          Search
          <!-- number badge -->
          <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
            {#if isInvoiceTabLoading}
              <Loader2 class="h-4 w-4 mx-auto animate-spin" />
            {:else}
              {formatLongNumber($searchTabPagination?.totalItems)}
            {/if}
          </div>
        </TabsTrigger>
      {/if}
    </div>

    <div class="flex gap-4">
      <SearchBar
        {isSearching}
        handleSearch={handleSearchRecurring}
        bind:searchQuery={$searchQuery}
        placeholder="Search recurring invoice by name" />

      <div class="flex flex-row flex-nowrap justify-between gap-x-6 sm:w-fit w-full">
        <Button
          variant="default"
          class="sm:w-fit w-full"
          on:click={() => (location.href = `#/recurring/${params.clientId}/create`)}>
          <Plus class="mr-1 h-4 w-4" />
          <span>Create Recurring</span>
        </Button>
      </div>
    </div>
  </TabsList>

  <TabsContent value={RecurringListTab.ACTIVE.LOWER_CASE}>
    <Tab
      name="recurring"
      tab="{RecurringListTab.ACTIVE.LOWER_CASE}"
      pageToLoad="{$recurringListPage.active}"
      listApi={`recurring/recurring-list?id=${params.clientId}&recurringStatus=active`}
      bind:this={activeTab}
      bind:dataList={$activeTabData}
      bind:hasData={$hasActiveTabData}
      bind:pagination={$activeTabPagination}
      bind:isLoading={isActiveTabLoading}
      {alwaysReload}
      {setTableColumns}
      {params}
      {updateTabList} />
  </TabsContent>

  <TabsContent value={RecurringListTab.INACTIVE.LOWER_CASE}>
    <Tab
      name="recurring"
      tab="{RecurringListTab.INACTIVE.LOWER_CASE}"
      pageToLoad="{$recurringListPage.inactive}"
      listApi={`recurring/recurring-list?id=${params.clientId}&recurringStatus=inactive`}
      bind:this={inactiveTab}
      bind:dataList={$inactiveTabData}
      bind:hasData={$hasInactiveTabData}
      bind:pagination={$inactiveTabPagination}
      bind:isLoading={isInactiveTabLoading}
      {alwaysReload}
      {setTableColumns}
      {params}
      {updateTabList} />
  </TabsContent>

  <TabsContent value={RecurringListTab.INVOICE.LOWER_CASE}>
    <Tab
      name="recurring"
      tab="{RecurringListTab.INVOICE.LOWER_CASE}"
      pageToLoad="{$recurringListPage.invoice}"
      listApi={`invoice/list?tab=all&recurring=true&clientId=${params.clientId}`}
      bind:this={invoiceTab}
      bind:dataList={$invoiceTabData}
      bind:hasData={$hasInvoiceTabData}
      bind:pagination={$invoiceTabPagination}
      bind:isLoading={isInvoiceTabLoading}
      bind:isCreatingNew={$isCreatingNewInvoice}
      setTableColumns={setAllTabTableColumns}
      {alwaysReload}
      {params}
      {updateTabList} />
  </TabsContent>

  <TabsContent value={RecurringListTab.SEARCH.LOWER_CASE}>
    <Tab
      name="recurring"
      tab="{RecurringListTab.SEARCH.LOWER_CASE}"
      pageToLoad="{$recurringListPage.search}"
      isSearch={true}
      listApi="recurring/recurring-list?id={params.clientId}&search={$searchQuery}"
      bind:this={searchTab}
      bind:dataList={$searchTabData}
      bind:hasData={$hasSearchTabData}
      bind:pagination={$searchTabPagination}
      bind:isLoading={isSearchTabLoading}
      setTableColumns={setSearchTableColumns}
      {params}
      {updateTabList} />
  </TabsContent>
</Tabs>
