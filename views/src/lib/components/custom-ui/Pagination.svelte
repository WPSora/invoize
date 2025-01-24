<script>
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { createGetRequest } from "$lib/helpers/request";
  import { Button } from "$lib/components/ui/button";
  import toast from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";

  /**
   * to display error message
   * @type {string}
   */
  export let name;
  /**
   * @type {{value: number, label: string | number} | null}
   */
  export let selectedPagination;
  /**
   * @type {boolean}
   */
  export let isLoading;
  /**
   * @type {string}
   */
  export let listApi;
  /**
   * @type {Array}
   */
  export let dataList;
  /**
   * @type {Object}
   */
  export let pagination;
  /**
   * optional. If isSearching param exist, searchValue param must exist too
   */
  export let isSearching = false;
  /**
   * optional. If isSearching param exist, searchValue param must exist too
   */
  export let searchValue = "";
  /**
   * optional. Option for number of item per page. Ex: [10, 25, 50, 100]
   */
  export let paginationOption = [];

  $: firstItem = (pagination?.page - 1) * pagination?.perPage + 1;
  $: lastItem = Math.min(pagination?.page * pagination?.perPage, pagination?.totalItems);

  // to get next or previous page
  const getPaginationPage = async (isNextPage) => {
    isLoading = true;
    try {
      const nextPage = isSearching
        ? `${listApi}&page=${pagination.page + 1}&per_page=${selectedPagination.value}&search=${searchValue}`
        : `${listApi}&page=${pagination.page + 1}&per_page=${selectedPagination.value}`;
      const prevPage = isSearching
        ? `${listApi}&page=${pagination.page - 1}&per_page=${selectedPagination.value}&search=${searchValue}`
        : `${listApi}&page=${pagination.page - 1}&per_page=${selectedPagination.value}`;
      const response = isNextPage ? await createGetRequest(nextPage) : await createGetRequest(prevPage);
      const { page, per_page, total_items, total_pages, items } = response.data;
      dataList = items;
      pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
    } catch (err) {
      handleError(err, `Failed to get products`);
    }
    isLoading = false;
  };

  // update number of invoice per page
  const updateInvoicePagination = async (selected) => {
    selectedPagination = selected;
    isLoading = true;
    try {
      const api = isSearching
        ? `${listApi}&per_page=${selected.value}&search=${searchValue}`
        : `${listApi}&per_page=${selected.value}`;
      const response = await createGetRequest(api);
      const { page, per_page, total_items, total_pages, items } = response.data;
      dataList = items;
      pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
    } catch (err) {
      handleError(err, `Failed to get ${name}`);
    }
    isLoading = false;
  };
</script>

<div class="flex flex-row flex-nowrap justify-between sm:items-center items-start sm:text-sm text-xs sm:mt-0 mt-2">
  <div class="flex sm:flex-row flex-col flex-nowrap sm:items-center items-start gap-x-4 gap-y-1 text-muted-foreground">
    <div>Items per page</div>
    <div class="text-black">
      <Select selected={selectedPagination} onSelectedChange={(e) => updateInvoicePagination(e)}>
        <SelectTrigger class="w-[70px]">
          <SelectValue placeholder="Items" />
        </SelectTrigger>
        <SelectContent>
          {#each paginationOption as option (option)}
            <SelectItem value={option}>{option}</SelectItem>
          {:else}
            <SelectItem value={10}>10</SelectItem>
            <SelectItem value={25}>25</SelectItem>
            <SelectItem value={50}>50</SelectItem>
            <SelectItem value={100}>100</SelectItem>
          {/each}
        </SelectContent>
      </Select>
    </div>
    {#if firstItem !== lastItem && pagination?.totalItems !== 0}
      <div>Showing {firstItem}-{lastItem} of {pagination?.totalItems ?? 0} items</div>
    {:else}
      <div>Showing {lastItem} of {pagination?.totalItems ?? 0} items</div>
    {/if}
  </div>
  <div class="flex items-center justify-end space-x-2 py-4">
    <Button variant="outline" size="sm" disabled={pagination.page === 1} on:click={() => getPaginationPage(false)}>
      Previous
    </Button>
    <Button variant="outline" disabled>{pagination.page ?? 1}</Button>
    <Button
      variant="outline"
      size="sm"
      disabled={pagination.page === pagination.totalPages}
      on:click={() => getPaginationPage(true)}>
      Next
    </Button>
  </div>
</div>
