<script>
  import { Table, Row, Body, Cell, Head, Header } from "$lib/components/ui/table";
  import { Subscribe, Render } from "svelte-headless-table";
  import { writable } from "svelte/store";
  import { createGetRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { ScrollText } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import Pagination from "$lib/components/custom-ui/Pagination.svelte";

  /** Data name, eg: invoice */
  export let name;
  export let tab = "";
  export let pageToLoad = 1;
  export let listApi = "";
  export let dataList = [];
  export let hasData = false;
  export let pagination = {};
  export let isSearch = false;
  export let isLoading = false;
  export let isCreatingNew = false;
  export let params = {};
  // true means all tabs will get fetch every time it gets mounted. False means it only fetch once and
  // fetch only when an invoice status is updated
  export let alwaysReload = false;
  export let updateTabList = () => {};
  export let setTableColumns = (pagination, tableStore, updateTabList, params = {}) => {};

  // we set to store because the table need store to subscribe, not regular variable
  let tableStore = writable(dataList);
  $: tableStore.set(dataList);

  // This is used as argument to setTableColumns to be able to get the updated value.
  // If we pass regular variable, it won't detect when value changes.
  let paginationStore = writable(pagination);
  $: paginationStore.set(pagination);

  $: selectedPagination = { value: pagination?.perPage ?? 10, label: pagination?.perPage ?? 10 };

  // for init and refresh after status update
  export const getListItems = async (callback) => {
    isLoading = true;
    try {
      const response = await createGetRequest(`${listApi}&per_page=${selectedPagination.value}&page=${pageToLoad}`);
      const { page, per_page, total_items, total_pages, items } = response.data;
      dataList = items;
      pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      isCreatingNew = false;
      hasData = !alwaysReload;
    } catch (err) {
      handleError(err, `Failed to get ${name}`);
    }
    isLoading = false;
  };

  // @ts-ignore
  const { headerRows, rows, tableAttrs, tableBodyAttrs } = setTableColumns(
    paginationStore,
    tableStore,
    updateTabList,
    params,
  );

  onMount(() => {
    // don't fetch data if it's on search tab, wait for search to be triggered instead.
    // also to fetch data again if alwaysReload is true.
    if (!isSearch && !hasData) {
      getListItems();
    }

    // fetch data after create new invoice, so invoice list gets updated
    if (!isSearch && isCreatingNew) {
      getListItems();
    }
  });
</script>

{#if !isLoading && dataList.length === 0}
  <div class="flex flex-col justify-center items-center h-60 text-muted-foreground bg-background rounded-lg shadow-sm">
    <ScrollText class="h-28 w-28 text-gray-400" strokeWidth="{0.5}" />
    <div>No {name} found</div>
  </div>
{:else}
  <div class="shadow-sm border rounded-md bg-background">
    <Table {...$tableAttrs} class="shadow-xl">
      <Header>
        {#each $headerRows as headerRow (headerRow.id)}
          <Subscribe rowAttrs="{headerRow.attrs()}" let:rowAttrs>
            <Row {...rowAttrs}>
              {#each headerRow.cells as cell (cell.id)}
                <Subscribe attrs="{cell.attrs()}" let:attrs>
                  <Head {...attrs}>
                    <Render of="{cell.render()}" />
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
                {#each $headerRows as headerRow (headerRow.id)}
                  {#each headerRow.cells as cell (cell.id)}
                    <Cell>
                      <Skeleton class="h-6 w-11/12 m-4 mx-auto rounded-md" />
                    </Cell>
                  {/each}
                {/each}
              </Row>
            {/each}
          {:else}
            {#each dataList as _}
              <Row>
                {#each $headerRows as headerRow (headerRow.id)}
                  {#each headerRow.cells as cell (cell.id)}
                    <Cell>
                      <Skeleton class="h-6 w-11/12 m-4 mx-auto rounded-md" />
                    </Cell>
                  {/each}
                {/each}
              </Row>
            {/each}
          {/if}
        {:else}
          {#each $rows as row (row.id)}
            <Subscribe rowAttrs="{row.attrs()}" let:rowAttrs>
              <Row {...rowAttrs} class="border-b-0">
                {#each row.cells as cell (cell.id)}
                  <Subscribe attrs="{cell.attrs()}" let:attrs>
                    <Cell {...attrs}>
                      <Render of="{cell.render()}" />
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

{#if dataList.length !== 0}
  <Pagination {name} {tab} bind:dataList {listApi} bind:isLoading bind:pagination bind:selectedPagination />
{/if}
