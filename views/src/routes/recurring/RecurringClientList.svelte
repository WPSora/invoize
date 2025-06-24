<script>
  import { Table, Row, Body, Cell, Head, Header } from "$lib/components/ui/table";
  import { Subscribe, Render } from "svelte-headless-table";
  import { writable } from "svelte/store";
  import { isCreatingNewRecurring } from "$lib/stores/recurring-store";
  import { createGetRequest } from "$lib/helpers/request";
  import { isPro } from "$lib/stores/settings-store";
  import { onMount } from "svelte";
  import { setTableColumns } from "$lib/components/recurring-client-list/recurring-client-table-column";
  import { Alert, AlertTitle, AlertDescription } from "$lib/components/ui/alert";
  import { Frown as FrownIcon, Plus } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { ScrollText } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import Pagination from "$lib/components/custom-ui/Pagination.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import UpgradeToPro from "$lib/components/dashboard/upgrade-to-pro.svelte";
  import { recurringClientListPage } from "$lib/stores/navigation";
  import { recurringTab } from "$lib/stores/active-tab-store";
  import { RecurringListTab } from "$lib/common/enum";
  import { recurringListPage } from "$lib/stores/navigation";


  const nav = [{ name: "Recurring", link: "recurring" }];

  /** Data name, eg: invoice */
  let name = "recurring invoice";
  let listApi = "recurring/client-list?";
  let dataList = [];
  let hasData = false;
  let pagination = writable({ page: 1, perPage: 10, totalItems: 0, totalPages: 0 });
  let isSearching = false;
  let searchValue;
  let isLoading = false;
  // true means all tabs will get fetch every time it gets mounted. False means it only fetch once and
  // fetch only when an invoice status is updated
  let alwaysReload = true;
  // this is used to check if we already fetch init data. if so, then header won't be loading anymore
  let isFetched = false;

  // we set to store because the table need store to subscribe, not regular variable
  let tableStore = writable(dataList);
  let selectedPagination = { value: $pagination?.perPage ?? 10, label: $pagination?.perPage ?? 10 };

  $: tableStore.set(dataList);
  $: hasNoRecurring = dataList.length < 1;

  const handleSearchRecurringClient = () => {
    if (!searchValue) {
      isSearching = false;
    } else {
      isSearching = true;
    }
    getListItems();
  };

  // for init and refresh after status update
  const getListItems = async () => {
    if (isLoading) return;
    isLoading = true;
    const api = isSearching
      ? `${listApi}per_page=${selectedPagination.value}&search=${searchValue}`
      : `${listApi}per_page=${selectedPagination.value}&page=${$recurringClientListPage}`;
    try {
      const response = await createGetRequest(api);
      const { page, per_page, total_items, total_pages, items } = response.data;
      dataList = items;
      $pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      $isCreatingNewRecurring = false;
      hasData = !alwaysReload;
      isFetched = true;
    } catch (err) {
      handleError(err, `Failed to get ${name}`);
    }
    isLoading = false;
  };

  // @ts-ignore
  const { headerRows, rows, tableAttrs, tableBodyAttrs } = setTableColumns(pagination, tableStore);

  const resetRecurringNavigation = () => {
    $recurringTab = RecurringListTab.ACTIVE.LOWER_CASE;
    $recurringListPage = {
      active: 1,
      inactive: 1,
      invoice: 1,
      search: 1
    };
  }

  onMount(() => {
    if($isPro) {
      // don't fetch data if it's on search tab, wait for search to be triggered instead.
      if (!isSearching && !hasData) {
        getListItems();
      }
  
      // fetch data after create new invoice, so invoice list gets updated
      if (!isSearching && $isCreatingNewRecurring) {
        getListItems();
      }

      resetRecurringNavigation();
    }
  });
</script>

<Breadcrumb to={nav} />
<PageTitle title="Recurring Invoice" description="Manage Your Recurring Invoice" hasDivider={false} />


{#if !$isPro}
  <UpgradeToPro/>
{:else}
  <Alert class="flex sm:flex-row flex-col items-center justify-between mb-2 gap-y-2">
    {#if isLoading && !isFetched}
      <div class="flex flex-nowrap justify-between w-full">
        <Skeleton class="h-10 w-60" />
        <div class="flex flex-nowrap gap-x-6">
          <Skeleton class="h-10 w-52" />
          <Skeleton class="h-10 w-32" />
        </div>
      </div>
    {:else}
      {#if hasNoRecurring && !isSearching && !isLoading}
        <div class="flex items-center">
          <FrownIcon class="mr-2 h-6 w-6 text-primary" />
          <div>
            <AlertTitle>There is no recurring invoice yet</AlertTitle>
            <AlertDescription>Let's create your first recurring invoice</AlertDescription>
          </div>
        </div>
      {:else}
        <SearchBar
          isSearching={isLoading && isSearching}
          handleSearch={handleSearchRecurringClient}
          bind:searchQuery={searchValue}
          placeholder="Search recurring invoice by customer" />
      {/if}
        <div class="flex flex-row flex-nowrap justify-between gap-x-6 sm:w-fit w-full">
          <Button variant="default" class="sm:w-fit w-full" on:click={() => (location.href = `#/recurring/create`)}>
            <Plus class="mr-1 h-4 w-4" />
            <span>Create Recurring</span>
          </Button>
        </div>
    {/if}
  </Alert>

  {#if !isLoading && hasNoRecurring}
    <div class="flex flex-col justify-center items-center h-60 text-muted-foreground bg-background rounded-lg shadow-sm">
      <ScrollText class="h-28 w-28 text-gray-400" strokeWidth={0.5} />
      No {name} found
    </div>
  {:else}
    <div class="shadow-sm border rounded-md bg-background">
      <Table {...$tableAttrs} class="shadow-xl">
        <Header>
          {#each $headerRows as headerRow (headerRow.id)}
            <Subscribe rowAttrs={headerRow.attrs()} let:rowAttrs>
              <Row {...rowAttrs}>
                {#each headerRow.cells as cell (cell.id)}
                  <Subscribe attrs={cell.attrs()} let:attrs>
                    <Head {...attrs}>
                      <Render of={cell.render()} />
                    </Head>
                  </Subscribe>
                {/each}
              </Row>
            </Subscribe>
          {/each}
        </Header>
        <Body {...$tableBodyAttrs}>
          {#if isLoading}
            {#if dataList.length === 0}
              {#each [1, 2, 3, 4, 5] as _}
                <Row>
                  {#each [1, 2, 3, 4, 5, 6, 7, 8] as _}
                    <Cell>
                      <Skeleton class="h-6 w-11/12 m-4 mx-auto rounded-md" />
                    </Cell>
                  {/each}
                </Row>
              {/each}
            {:else}
              {#each dataList as _}
                <Row>
                  {#each [1, 2, 3, 4, 5, 6, 7, 8] as _}
                    <Cell>
                      <Skeleton class="h-6 w-11/12 m-4 mx-auto rounded-md" />
                    </Cell>
                  {/each}
                </Row>
              {/each}
            {/if}
          {:else}
            {#each $rows as row (row.id)}
              <Subscribe rowAttrs={row.attrs()} let:rowAttrs>
                <Row {...rowAttrs} class="border-b-0">
                  {#each row.cells as cell (cell.id)}
                    <Subscribe attrs={cell.attrs()} let:attrs>
                      <Cell {...attrs}>
                        <Render of={cell.render()} />
                      </Cell>
                    </Subscribe>
                  {/each}
                </Row>
              </Subscribe>
            {/each}
          {/if}
        </Body>
      </Table>
    </div>
  {/if}

  {#if !isLoading && dataList.length !== 0}
    <Pagination
      name="recurring-client"
      bind:dataList
      {listApi}
      bind:isLoading
      bind:pagination={$pagination}
      bind:selectedPagination />
  {/if}
{/if}
