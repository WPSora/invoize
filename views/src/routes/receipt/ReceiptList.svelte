<script>
  import {
    searchQuery,
    paidReceiptPagination,
    archivedReceiptPagination,
    cancelledReceiptPagination,
    allReceiptPagination,
    searchReceiptPagination,
    hasPaidData,
    paidTabData,
    hasArchivedData,
    archivedTabData,
    hasCancelledData,
    cancelledTabData,
    hasAllData,
    allTabData,
    hasSearchData,
    searchTabData,
  } from "$lib/stores/receipt-list-store";
  import {
    receiptListBaseTabStyle,
    allTabClass,
    paidTabClass,
    archiveTabClass,
    cancelTabClass,
    searchTabClass,
    baseSelectTabClass,
  } from "$lib/common/styles";
  import { createGetRequest } from "$lib/helpers/request";
  import { Tabs, TabsContent, TabsTrigger, TabsList } from "$lib/components/ui/tabs";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Loader2 } from "lucide-svelte";
  import { setTableColumns } from "$lib/components/receipt-list/receipt-table-column";
  import { InvoiceListTab, ReceiptListTab } from "$lib/common/enum";
  import { formatLongNumber } from "$lib/helpers/longNumberFormatter";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import Tab from "$lib/components/custom-ui/Tab.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import { receiptTab } from "$lib/stores/active-tab-store";
  import { receiptListPage } from "$lib/stores/navigation";

  const nav = [{ name: "Receipt", link: "receipts" }];

  // for Select tab in small screen
  let activeStatusTabSelect = { label: "Unpaid", value: "unpaid" };

  let isSearching = false;
  let isAllReceiptLoading = false;
  let isArchivedReceiptLoading = false;
  let isCancelledReceiptLoading = false;
  let isPaidReceiptLoading = false;
  let alwaysReload = true;

  let paidTab;
  let archivedTab;
  let cancelledTab;
  let allTab;
  let searchTab;

  // if tabClickCount is more than 1, it means user clicking on the same tab and need to refresh the tab
  let tabClickCount = 1;

  const updateActiveTab = (e) => {
    tabClickCount = 0;
    $receiptTab = e;
  };

  const refreshTab = (tab) => {
    tabClickCount++;
    if (tabClickCount > 1) {
      updateTabList(tab);
    }
  };

  const updateTabList = (tab) => {
    if (tab === InvoiceListTab.PAID.LOWER_CASE) {
      paidTab.getListItems();
    } else if (tab === InvoiceListTab.ARCHIVED.LOWER_CASE) {
      archivedTab.getListItems();
    } else if (tab === InvoiceListTab.CANCELLED.LOWER_CASE) {
      cancelledTab.getListItems();
    } else if (tab === InvoiceListTab.SEARCH.LOWER_CASE) {
      searchTab.getListItems();
    }
    allTab.getListItems();
  };

  const handleSearchReceipt = () => {
    $hasSearchData = false;
    if (!$searchQuery) {
      $searchTabData = [];
      $receiptTab = $receiptTab === "search" ? "paid" : $receiptTab;
      return;
    }
    isSearching = true;
    createGetRequest(`receipt/list?search=${$searchQuery}`).then((response) => {
      const { page, per_page, total_items, total_pages, items } = response.data;
      $searchTabData = items;
      $searchReceiptPagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      $hasSearchData = true;
      $receiptTab = "search";
      isSearching = false;
    });
  };
</script>

<Breadcrumb to={nav} />
<PageTitle title="Receipt" description="View Your Receipt" hasDivider={false} />

<div class="bg-background flex sm:flex-row flex-col h-fit gap-y-4 justify-between px-3 sm:py-2 py-5 w-full">
  <SearchBar
    {isSearching}
    handleSearch={handleSearchReceipt}
    bind:searchQuery={$searchQuery}
    placeholder="Customer name or receipt number" />
</div>

<Tabs bind:value={$receiptTab} onValueChange={updateActiveTab}>
  <TabsList class="bg-background w-full flex justify-between py-6 px-2 gap-x-2 rounded-md">
    <!-- Tab for large screen -->
    <div class="lg:flex hidden justify-around w-3/5 flex-nowrap gap-2">
      <TabsTrigger
        value={InvoiceListTab.PAID.LOWER_CASE}
        class="{receiptListBaseTabStyle} {paidTabClass}"
        on:click={() => refreshTab(InvoiceListTab.PAID.LOWER_CASE)}>
        Paid
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isPaidReceiptLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($paidReceiptPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value={InvoiceListTab.CANCELLED.LOWER_CASE}
        class="{receiptListBaseTabStyle} {cancelTabClass}"
        on:click={() => refreshTab(InvoiceListTab.CANCELLED.LOWER_CASE)}>
        Cancelled
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isCancelledReceiptLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($cancelledReceiptPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value={InvoiceListTab.ARCHIVED.LOWER_CASE}
        class="{receiptListBaseTabStyle} {archiveTabClass}"
        on:click={() => refreshTab(InvoiceListTab.ARCHIVED.LOWER_CASE)}>
        Archived
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isArchivedReceiptLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($archivedReceiptPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value={InvoiceListTab.ALL.LOWER_CASE}
        class="{receiptListBaseTabStyle} {allTabClass}"
        on:click={() => refreshTab(InvoiceListTab.ALL.LOWER_CASE)}>
        All
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isAllReceiptLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($allReceiptPagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      {#if $hasSearchData}
        <TabsTrigger
          value={InvoiceListTab.SEARCH.LOWER_CASE}
          class="{receiptListBaseTabStyle} {searchTabClass}"
          on:click={() => refreshTab(InvoiceListTab.SEARCH.LOWER_CASE)}>
          Search result
          <!-- number badge -->
          <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
            {#if isSearching}
              <Loader2 class="h-4 w-4 mx-auto animate-spin" />
            {:else}
              {formatLongNumber($searchReceiptPagination?.totalItems)}
            {/if}
          </div>
        </TabsTrigger>
      {/if}
    </div>

    <!-- Select for small screen -->
    <div class="lg:hidden flex w-full">
      <Select bind:selected={activeStatusTabSelect}>
        <SelectTrigger class="sm:w-60 w-full text-black font-medium" id="select-tab">
          <SelectValue placeholder="Select Tab" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={InvoiceListTab.PAID.LOWER_CASE} label={InvoiceListTab.PAID.PASCAL_CASE}>
            <TabsTrigger value={InvoiceListTab.PAID.LOWER_CASE} class={baseSelectTabClass}>
              {InvoiceListTab.PAID.PASCAL_CASE}
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isPaidReceiptLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($paidReceiptPagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value={InvoiceListTab.CANCELLED.LOWER_CASE} label={InvoiceListTab.CANCELLED.PASCAL_CASE}>
            <TabsTrigger value={InvoiceListTab.CANCELLED.LOWER_CASE} class={baseSelectTabClass}>
              {InvoiceListTab.CANCELLED.PASCAL_CASE}
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isCancelledReceiptLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($cancelledReceiptPagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value={InvoiceListTab.ARCHIVED.LOWER_CASE} label={InvoiceListTab.ARCHIVED.PASCAL_CASE}>
            <TabsTrigger value={InvoiceListTab.ARCHIVED.LOWER_CASE} class={baseSelectTabClass}>
              {InvoiceListTab.ARCHIVED.PASCAL_CASE}
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isArchivedReceiptLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($archivedReceiptPagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value={InvoiceListTab.ALL.LOWER_CASE} label={InvoiceListTab.ALL.PASCAL_CASE}>
            <TabsTrigger value={InvoiceListTab.ALL.LOWER_CASE} class={baseSelectTabClass}>
              {InvoiceListTab.ALL.PASCAL_CASE}
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isAllReceiptLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($allReceiptPagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>
        </SelectContent>
      </Select>
    </div>
  </TabsList>

  <!-- All one-time tabs -->
  <TabsContent value={ReceiptListTab.PAID.LOWER_CASE}>
    <Tab
      name="receipt"
      tab={ReceiptListTab.PAID.LOWER_CASE}
      pageToLoad={$receiptListPage.paid}
      listApi="receipt/list?tab=paid"
      bind:this={paidTab}
      bind:dataList={$paidTabData}
      bind:hasData={$hasPaidData}
      {alwaysReload}
      {setTableColumns}
      bind:pagination={$paidReceiptPagination}
      bind:isLoading={isPaidReceiptLoading} />
  </TabsContent>

  <TabsContent value={ReceiptListTab.ARCHIVED.LOWER_CASE}>
    <Tab
      name="receipt"
      tab={ReceiptListTab.ARCHIVED.LOWER_CASE}
      pageToLoad={$receiptListPage.archived}
      listApi="receipt/list?tab=archived"
      bind:this={archivedTab}
      bind:dataList={$archivedTabData}
      bind:hasData={$hasArchivedData}
      {alwaysReload}
      {setTableColumns}
      bind:pagination={$archivedReceiptPagination}
      bind:isLoading={isArchivedReceiptLoading} />
  </TabsContent>

  <TabsContent value={ReceiptListTab.CANCELLED.LOWER_CASE}>
    <Tab
      name="receipt"
      tab={ReceiptListTab.CANCELLED.LOWER_CASE}
      pageToLoad={$receiptListPage.cancelled}
      listApi="receipt/list?tab=cancelled"
      bind:this={cancelledTab}
      bind:dataList={$cancelledTabData}
      bind:hasData={$hasCancelledData}
      {alwaysReload}
      {setTableColumns}
      bind:pagination={$cancelledReceiptPagination}
      bind:isLoading={isCancelledReceiptLoading} />
  </TabsContent>

  <TabsContent value={ReceiptListTab.ALL.LOWER_CASE}>
    <Tab
      name="receipt"
      tab={ReceiptListTab.ALL.LOWER_CASE}
      pageToLoad={$receiptListPage.all}
      listApi="receipt/list?tab=all"
      bind:this={allTab}
      bind:dataList={$allTabData}
      bind:hasData={$hasAllData}
      {alwaysReload}
      {setTableColumns}
      bind:pagination={$allReceiptPagination}
      bind:isLoading={isAllReceiptLoading} />
  </TabsContent>

  <TabsContent value={ReceiptListTab.SEARCH.LOWER_CASE}>
    <Tab
      name="receipt"
      tab={ReceiptListTab.SEARCH.LOWER_CASE}
      pageToLoad={$receiptListPage.search}
      isSearch={true}
      listApi="receipt/list?search={$searchQuery}"
      bind:this={searchTab}
      bind:dataList={$searchTabData}
      bind:hasData={$hasSearchData}
      {setTableColumns}
      bind:pagination={$searchReceiptPagination}
      bind:isLoading={isSearching} />
  </TabsContent>
</Tabs>
