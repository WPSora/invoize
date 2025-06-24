<script>
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import { Button } from "$lib/components/ui/button";
  import { formatLongNumber } from "$lib/helpers/longNumberFormatter";
  import Tab from "$lib/components/custom-ui/Tab.svelte";
  import { setTableColumns } from "$lib/components/quotation-list/quotation-table-column";
  import {
    Select,
    SelectItem,
    SelectValue,
    SelectContent,
    SelectTrigger,
  } from "$lib/components/ui/select";

  import {
    hasActiveData,
    activeQuotationPagination,
    activeTabData,
    hasArchiveData,
    archiveQuotationPagination,
    archiveTabData
  } from "$lib/stores/quotation-list-store";

  import {
    Tabs,
    TabsContent,
    TabsTrigger,
    TabsList,
  } from "$lib/components/ui/tabs";

  import { Loader2 } from "lucide-svelte";
  import {
    allTabClass,
    invoiceListBaseTabStyle,
    archiveTabClass,
    baseSelectTabClass,
  } from "$lib/common/styles";
  import { Plus } from "radix-icons-svelte";
  import { QuotationListTab } from "$lib/common/enum";
  import { quotationListPage } from "$lib/stores/navigation";
  import { quotationTab } from "$lib/stores/active-tab-store";
  import { isPro } from "$lib/stores/settings-store";
  import UpgradeToPro from "$lib/components/dashboard/upgrade-to-pro.svelte";

  let activeStatusTabSelect = { label: "Active", value: "active" };
  let activeTab;
  let archiveTab;
  let tabClickCount = 1;
  let isActiveLoading = false;
  let isArchiveLoading = false;
  let alwaysReload = false;

  const nav = [{ name: "Quotation", link: "quotations" }];

  /**
   * This function will be passed to -> tab -> invoice-table-column -> action-cell
   * So when we try to update the status (which is from the action cell)
   * It will tell to InvoiceList which tab to get updated
   */
  const updateTabList = (tab1) => {
    if (tab1 === QuotationListTab.ACTIVE.LOWER_CASE) {
      activeTab.getListItems();
    }
    if (tab1 === QuotationListTab.ARCHIVED.LOWER_CASE) {
      archiveTab.getListItems();
    }
  };

  const refreshTab = (tab) => {
    tabClickCount++;
    if (tabClickCount > 1) {
      updateTabList(tab);
    }
  };

</script>

{#if !$isPro}
  <UpgradeToPro />
{:else}
  <Breadcrumb to={nav} />
  <PageTitle
    title="Quotation"
    description="Manage Your Quotation"
    hasDivider={false} />

  <Tabs bind:value={$quotationTab}>
    <TabsList
      class="bg-background w-full h-fit flex sm:flex-row flex-col-reverse justify-between sm:items-center items-end px-2 gap-x-2 gap-y-4 rounded-md">
      <div class="lg:flex hidden justify- w-4/5 flex-nowrap gap-2">
        <TabsTrigger
          value="{QuotationListTab.ACTIVE.LOWER_CASE}"
          on:click={() => refreshTab(QuotationListTab.ACTIVE.LOWER_CASE)}
          class="{invoiceListBaseTabStyle} {allTabClass}">
          Active
          <!-- number badge -->
          <div
            class="rounded-full bg-red-500 text-background text-center min-w-8 w-fit px-0.5 ml-3">
            {#if isActiveLoading}
              <Loader2 class="h-4 w-4 mx-auto animate-spin" />
            {:else}
              {formatLongNumber($activeQuotationPagination?.totalItems)}
            {/if}
          </div>
        </TabsTrigger>
        <TabsTrigger
          value="{QuotationListTab.ARCHIVED.LOWER_CASE}"
          on:click={() => refreshTab(QuotationListTab.ARCHIVED.LOWER_CASE)}
          class="{invoiceListBaseTabStyle} {archiveTabClass}">
          Archived
          <!-- number badge -->
          <div
            class="rounded-full bg-red-500 text-background text-center min-w-8 w-fit px-0.5 ml-3">
            {#if isActiveLoading}
              <Loader2 class="h-4 w-4 mx-auto animate-spin" />
            {:else}
              {formatLongNumber($archiveQuotationPagination?.totalItems)}
            {/if}
          </div>
        </TabsTrigger>
      </div>
      <div class="lg:hidden flex w-full">
        <Select bind:selected={activeStatusTabSelect}>
          <SelectTrigger
            class="sm:w-60 w-full text-black font-medium"
            id="select-tab">
            <SelectValue placeholder="Select Tab" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="{QuotationListTab.ACTIVE.LOWER_CASE}" label="{QuotationListTab.ACTIVE.PASCAL_CASE}">
              <TabsTrigger value="{QuotationListTab.ACTIVE.LOWER_CASE}" class={baseSelectTabClass}>
                Active
                <div
                  class="rounded-full bg-red-500 text-background min-w-8 w-fit px-0.5 ml-3">
                  {#if isActiveLoading}
                    <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                  {:else}
                    {formatLongNumber($activeQuotationPagination?.totalItems)}
                  {/if}
                </div>
              </TabsTrigger>
            </SelectItem>
            <SelectItem value="{QuotationListTab.ARCHIVED.LOWER_CASE}" label="{QuotationListTab.ARCHIVED.PASCAL_CASE}">
              <TabsTrigger value="{QuotationListTab.ARCHIVED.LOWER_CASE}" class="{baseSelectTabClass} {archiveTabClass}">
                Archived
                <div
                  class="rounded-full bg-red-500 text-background min-w-8 w-fit px-0.5 ml-3">
                  {#if isArchiveLoading}
                    <Loader2 class="h-4 w-4 mx-auto animate-spin" />
                  {:else}
                    {formatLongNumber($archiveQuotationPagination?.totalItems)}
                  {/if}
                </div>
              </TabsTrigger>
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
      <Button class="px-6 flex flex-nowrap items-center justify-center sm:w-fit w-full"
        on:click={() => (location.href = `#/quotation/create`)}>
        <Plus />
        <div class="ml-1">Create Quotation</div>
      </Button>
    </TabsList>
    <TabsContent value="{QuotationListTab.ACTIVE.LOWER_CASE}">
      <Tab
        name="quotation"
        tab="{QuotationListTab.ACTIVE.LOWER_CASE}"
        pageToLoad="{$quotationListPage.active}"
        listApi="quotation/list?tab=active"
        bind:this={activeTab}
        bind:dataList={$activeTabData}
        bind:hasData={$hasActiveData}
        {alwaysReload}
        {updateTabList}
        {setTableColumns}
        bind:pagination={$activeQuotationPagination}
        bind:isLoading={isActiveLoading} />
    </TabsContent>
    <TabsContent value="{QuotationListTab.ARCHIVED.LOWER_CASE}">
      <Tab
        name="quotation"
        tab="{QuotationListTab.ARCHIVED.LOWER_CASE}"
        pageToLoad="{$quotationListPage.archived}"
        listApi="quotation/list?tab=archive"
        bind:this={archiveTab}
        bind:dataList={$archiveTabData}
        bind:hasData={$hasArchiveData}
        {alwaysReload}
        {updateTabList}
        {setTableColumns}
        bind:pagination={$archiveQuotationPagination}
        bind:isLoading={isArchiveLoading} />
    </TabsContent>
  </Tabs>
{/if}