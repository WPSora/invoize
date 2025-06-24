<script>
  import {
    searchQuery,
    unpaidInvoicePagination,
    paidInvoicePagination,
    expiredInvoicePagination,
    archivedInvoicePagination,
    cancelledInvoicePagination,
    trashedInvoicePagination,
    allInvoicePagination,
    searchInvoicePagination,
    unpaidTabData,
    hasUnpaidData,
    paidTabData,
    hasPaidData,
    expiredTabData,
    hasExpiredData,
    archivedTabData,
    hasArchivedData,
    cancelledTabData,
    hasCancelledData,
    trashedTabData,
    hasTrashedData,
    allTabData,
    hasAllData,
    searchTabData,
    hasSearchData,
  } from "$lib/stores/invoice-list-store";
  import {
    invoiceListBaseTabStyle,
    allTabClass,
    paidTabClass,
    unpaidTabClass,
    expiredTabClass,
    archiveTabClass,
    cancelTabClass,
    trashTabClass,
    searchTabClass,
    baseSelectTabClass,
  } from "$lib/common/styles";
  import { invoiceListPage } from "$lib/stores/navigation";
  import { createGetRequest } from "$lib/helpers/request";
  import { Tabs, TabsContent, TabsTrigger, TabsList } from "$lib/components/ui/tabs";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Loader2 } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { Plus } from "radix-icons-svelte";
  import { InvoiceListTab } from "$lib/common/enum";
  import { setTableColumns } from "$lib/components/invoice-list/invoice-table-column";
  import { setAllTabTableColumns } from "$lib/components/invoice-list/invoice-all-tab-table-column";
  import { formatLongNumber } from "$lib/helpers/longNumberFormatter";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import Tab from "$lib/components/custom-ui/Tab.svelte";
  import { invoiceTab } from "$lib/stores/active-tab-store";

  const nav = [{ name: "Invoice", link: "invoices" }];

  // for Select tab in small screen
  let activeStatusTabSelect = { label: "Unpaid", value: "unpaid" };

  let isSearching = false;
  let isAllInvoiceLoading = false;
  let isArchivedInvoiceLoading = false;
  let isCancelledInvoiceLoading = false;
  let isExpiredInvoiceLoading = false;
  let isTrashedInvoiceLoading = false;
  let isPaidInvoiceLoading = false;
  let isUnpaidInvoiceLoading = false;
  let alwaysReload = true;

  let unpaidTab;
  let paidTab;
  let expiredTab;
  let archivedTab;
  let cancelledTab;
  let trashedTab;
  let allTab;
  let searchTab;

  // if tabClickCount is more than 1, it means user clicking on the same tab and need to refresh the tab.
  // this start from 1 because to be able to refresh the initial tab (unpaid) by clicking it only once.
  let tabClickCount = 1;

  /**
   * This function will be passed to -> tab -> invoice-table-column -> action-cell
   * So when we try to update the status (which is from the action cell)
   * It will tell to InvoiceList which tab to get updated
   */
  const updateTabList = (tab1, tab2) => {
    if (tab1 === InvoiceListTab.UNPAID.LOWER_CASE || tab2 === InvoiceListTab.UNPAID.LOWER_CASE) {
      unpaidTab.getListItems();
    }
    if (tab1 === InvoiceListTab.PAID.LOWER_CASE || tab2 === InvoiceListTab.PAID.LOWER_CASE) {
      paidTab.getListItems();
    }
    if (tab1 === InvoiceListTab.EXPIRED.LOWER_CASE || tab2 === InvoiceListTab.EXPIRED.LOWER_CASE) {
      expiredTab.getListItems();
    }
    if (tab1 === InvoiceListTab.ARCHIVED.LOWER_CASE || tab2 === InvoiceListTab.ARCHIVED.LOWER_CASE) {
      archivedTab.getListItems();
    }
    if (tab1 === InvoiceListTab.CANCELLED.LOWER_CASE || tab2 === InvoiceListTab.CANCELLED.LOWER_CASE) {
      cancelledTab.getListItems();
    }
    if (tab1 === InvoiceListTab.TRASHED.LOWER_CASE || tab2 === InvoiceListTab.TRASHED.LOWER_CASE) {
      trashedTab.getListItems();
    }
    if ($hasSearchData) {
      searchTab.getListItems();
    }
    allTab.getListItems();
  };

  const handleSearchInvoice = () => {
    $hasSearchData = false;
    if (!$searchQuery) {
      $searchTabData = [];
      $invoiceTab = $invoiceTab === "search" ? "unpaid" : $invoiceTab;
      return;
    }
    isSearching = true;
    createGetRequest(`invoice/list?search=${$searchQuery}`).then((response) => {
      const { page, per_page, total_items, total_pages, items } = response.data;
      $searchTabData = items;
      $searchInvoicePagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      $hasSearchData = true;
      $invoiceTab = "search";
      isSearching = false;
    });
  };

  const updateActiveTab = (e) => {
    tabClickCount = 0;
    $invoiceTab = e;
  };

  const refreshTab = (tab) => {
    tabClickCount++;
    if (tabClickCount > 1) {
      updateTabList(tab);
    }
  };
</script>

<Breadcrumb to="{nav}" />
<PageTitle title="Invoice" description="Manage Your Invoice" hasDivider="{false}" />

<SearchBar
  class="mb-2"
  {isSearching}
  handleSearch="{handleSearchInvoice}"
  bind:searchQuery="{$searchQuery}"
  placeholder="Customer name or invoice number" />

<Tabs bind:value="{$invoiceTab}" onValueChange="{updateActiveTab}">
  <TabsList
    class="bg-background w-full h-fit flex sm:flex-row flex-col-reverse justify-between sm:items-center items-end px-2 gap-x-2 gap-y-4 rounded-md">
    <!-- Tab for large screen -->
    <div class="lg:flex hidden justify-around w-4/5 flex-nowrap gap-2">
      <TabsTrigger
        value="{InvoiceListTab.UNPAID.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {unpaidTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.UNPAID.LOWER_CASE)}">
        Unpaid
        <!-- number badge -->
        <div class="rounded-full bg-red-500 text-background text-center min-w-8 w-fit px-0.5 ml-3">
          {#if isUnpaidInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($unpaidInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.PAID.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {paidTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.PAID.LOWER_CASE)}">
        Paid
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isPaidInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($paidInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.EXPIRED.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {expiredTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.EXPIRED.LOWER_CASE)}">
        Expired
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isExpiredInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($expiredInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.ARCHIVED.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {archiveTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.ARCHIVED.LOWER_CASE)}">
        Archived
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isArchivedInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($archivedInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.CANCELLED.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {cancelTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.CANCELLED.LOWER_CASE)}">
        Cancelled
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isCancelledInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($cancelledInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.TRASHED.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {trashTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.TRASHED.LOWER_CASE)}">
        Trashed
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isTrashedInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($trashedInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      <TabsTrigger
        value="{InvoiceListTab.ALL.LOWER_CASE}"
        class="{invoiceListBaseTabStyle} {allTabClass}"
        on:click="{() => refreshTab(InvoiceListTab.ALL.LOWER_CASE)}">
        All
        <!-- number badge -->
        <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
          {#if isAllInvoiceLoading}
            <Loader2 class="h-4 w-4 mx-auto animate-spin" />
          {:else}
            {formatLongNumber($allInvoicePagination?.totalItems)}
          {/if}
        </div>
      </TabsTrigger>

      {#if $hasSearchData}
        <TabsTrigger
          value="{InvoiceListTab.SEARCH.LOWER_CASE}"
          class="{invoiceListBaseTabStyle} {searchTabClass}"
          on:click="{() => refreshTab(InvoiceListTab.SEARCH.LOWER_CASE)}">
          Search result
          <!-- number badge -->
          <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
            {#if isSearching}
              <Loader2 class="h-4 w-4 mx-auto animate-spin" />
            {:else}
              {formatLongNumber($searchInvoicePagination?.totalItems)}
            {/if}
          </div>
        </TabsTrigger>
      {/if}
    </div>

    <!-- Use Select for small screen instead of Tabs -->
    <div class="lg:hidden flex w-full">
      <Select bind:selected="{activeStatusTabSelect}">
        <SelectTrigger class="sm:w-60 w-full text-black font-medium" id="select-tab">
          <SelectValue placeholder="Select Tab" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="{InvoiceListTab.UNPAID.LOWER_CASE}" label="{InvoiceListTab.UNPAID.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.UNPAID.LOWER_CASE}" class="{baseSelectTabClass}">
              Unpaid
              <div class="rounded-full bg-red-500 text-background min-w-8 w-fit px-0.5 ml-3">
                {#if isUnpaidInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($unpaidInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.PAID.LOWER_CASE}" label="{InvoiceListTab.PAID.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.PAID.LOWER_CASE}" class="{baseSelectTabClass}">
              Paid
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isPaidInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($paidInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.EXPIRED.LOWER_CASE}" label="{InvoiceListTab.EXPIRED.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.EXPIRED.LOWER_CASE}" class="{baseSelectTabClass}">
              Expired
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isExpiredInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($expiredInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.ARCHIVED.LOWER_CASE}" label="{InvoiceListTab.ARCHIVED.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.ARCHIVED.LOWER_CASE}" class="{baseSelectTabClass}">
              Archived
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isArchivedInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($archivedInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.CANCELLED.PASCAL_CASE}" label="{InvoiceListTab.CANCELLED.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.CANCELLED.LOWER_CASE}" class="{baseSelectTabClass}">
              Cancelled
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isCancelledInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($cancelledInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.TRASHED.LOWER_CASE}" label="{InvoiceListTab.TRASHED.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.TRASHED.LOWER_CASE}" class="{baseSelectTabClass}">
              Trashed
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isTrashedInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($trashedInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>

          <SelectItem value="{InvoiceListTab.ALL.LOWER_CASE}" label="{InvoiceListTab.ALL.PASCAL_CASE}">
            <TabsTrigger value="{InvoiceListTab.ALL.LOWER_CASE}" class="{baseSelectTabClass}">
              All
              <!-- number badge -->
              <div class="rounded-full bg-accent text-slate-400 min-w-8 w-fit px-0.5 ml-3">
                {#if isAllInvoiceLoading}
                  <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                {:else}
                  {formatLongNumber($allInvoicePagination?.totalItems)}
                {/if}
              </div>
            </TabsTrigger>
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <Button
      class="px-6 flex flex-nowrap items-center justify-center sm:w-fit w-full"
      on:click="{() => (location.href = `#/invoice/create`)}">
      <Plus />
      <div class="ml-1">Create Invoice</div>
    </Button>
  </TabsList>

  <TabsContent value="{InvoiceListTab.UNPAID.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.UNPAID.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.unpaid}"
      listApi="invoice/list?tab=unpaid"
      bind:this="{unpaidTab}"
      bind:dataList="{$unpaidTabData}"
      bind:hasData="{$hasUnpaidData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      bind:pagination="{$unpaidInvoicePagination}"
      bind:isLoading="{isUnpaidInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.PAID.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.PAID.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.paid}"
      listApi="invoice/list?tab=paid"
      bind:this="{paidTab}"
      bind:dataList="{$paidTabData}"
      bind:hasData="{$hasPaidData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      params="{{ tab: InvoiceListTab.PAID.LOWER_CASE }}"
      bind:pagination="{$paidInvoicePagination}"
      bind:isLoading="{isPaidInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.EXPIRED.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.EXPIRED.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.expired}"
      listApi="invoice/list?tab=expired"
      bind:this="{expiredTab}"
      bind:dataList="{$expiredTabData}"
      bind:hasData="{$hasExpiredData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      bind:pagination="{$expiredInvoicePagination}"
      bind:isLoading="{isExpiredInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.ARCHIVED.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.ARCHIVED.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.archived}"
      listApi="invoice/list?tab=archived"
      bind:this="{archivedTab}"
      bind:dataList="{$archivedTabData}"
      bind:hasData="{$hasArchivedData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      bind:pagination="{$archivedInvoicePagination}"
      bind:isLoading="{isArchivedInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.CANCELLED.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.CANCELLED.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.cancelled}"
      listApi="invoice/list?tab=cancelled"
      bind:this="{cancelledTab}"
      bind:dataList="{$cancelledTabData}"
      bind:hasData="{$hasCancelledData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      bind:pagination="{$cancelledInvoicePagination}"
      bind:isLoading="{isCancelledInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.TRASHED.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.TRASHED.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.trashed}"
      listApi="invoice/list?tab=trashed"
      bind:this="{trashedTab}"
      bind:dataList="{$trashedTabData}"
      bind:hasData="{$hasTrashedData}"
      {alwaysReload}
      {updateTabList}
      {setTableColumns}
      bind:pagination="{$trashedInvoicePagination}"
      bind:isLoading="{isTrashedInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.ALL.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.ALL.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.all}"
      listApi="invoice/list?tab=all"
      bind:this="{allTab}"
      bind:dataList="{$allTabData}"
      bind:hasData="{$hasAllData}"
      {alwaysReload}
      {updateTabList}
      setTableColumns="{setAllTabTableColumns}"
      bind:pagination="{$allInvoicePagination}"
      bind:isLoading="{isAllInvoiceLoading}" />
  </TabsContent>

  <TabsContent value="{InvoiceListTab.SEARCH.LOWER_CASE}">
    <Tab
      name="invoice"
      tab="{InvoiceListTab.SEARCH.LOWER_CASE}"
      pageToLoad="{$invoiceListPage.search}"
      isSearch="{true}"
      listApi="invoice/list?search={$searchQuery}"
      bind:this="{searchTab}"
      bind:dataList="{$searchTabData}"
      bind:hasData="{$hasSearchData}"
      {updateTabList}
      setTableColumns="{setAllTabTableColumns}"
      bind:pagination="{$searchInvoicePagination}"
      bind:isLoading="{isSearching}" />
  </TabsContent>
</Tabs>
