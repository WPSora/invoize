<script>
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { createGetRequest } from "$lib/helpers/request";
  import { Button } from "$lib/components/ui/button";
  import toast from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";
  import { CaretSort } from "radix-icons-svelte";
  import { tick } from "svelte";
  import { handleActivePage } from "$lib/stores/navigation";
  import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNextButton,
    PaginationPrevButton,
  } from "$lib/components/ui/pagination";
  /**
   * to display error message
   * @type {string}
   */
  export let name;
  /**
   * @type {string}
   */
  export let tab = "";
  /**
   * @type {{value: number, label: string} | null}
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

  let pageList = [];

  $: firstItem = (pagination?.page - 1) * pagination?.perPage + 1;
  $: lastItem = Math.min(pagination?.page * pagination?.perPage, pagination?.totalItems);
  $: selectedPage = { value: pagination.page ?? 1, label: pagination.page ?? "1" };

  const calculatePageList = async (isOpen) => {
    if (!isOpen) return;
    pageList = Array.from({ length: pagination.totalPages ?? 1 }, (_, i) => i + 1);
    await tick();
  };

  // to get next or previous page
  const getPaginationPage = async (pageToGo) => {
    if (pageToGo == pagination.page) return;
    isLoading = true;
    try {
      const api = `${listApi}&page=${pageToGo}&per_page=${selectedPagination.value}&search=${searchValue}`;
      const response = await createGetRequest(api);
      const { page, per_page, total_items, total_pages, items } = response.data;
      dataList = items;
      pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      handleActivePage(pageToGo, name, tab);
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
      <Select selected="{selectedPagination}" onSelectedChange="{(e) => updateInvoicePagination(e)}">
        <SelectTrigger class="w-[70px] bg-background">
          <SelectValue placeholder="Items" />
        </SelectTrigger>
        <SelectContent>
          {#each paginationOption as option (option)}
            <SelectItem value="{option}">{option}</SelectItem>
          {:else}
            <SelectItem value="{10}">10</SelectItem>
            <SelectItem value="{25}">25</SelectItem>
            <SelectItem value="{50}">50</SelectItem>
            <SelectItem value="{100}">100</SelectItem>
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
    <!-- <Button
      variant="outline"
      size="sm"
      class="bg-background"
      disabled="{pagination.page === 1}"
      on:click="{() => getPaginationPage(pagination.page - 1)}">
      Previous
    </Button>

    <Select
      selected="{selectedPage}"
      onSelectedChange="{(e) => getPaginationPage(e.value)}"
      onOpenChange="{calculatePageList}">
      <SelectTrigger class="bg-background gap-2">
        <SelectValue placeholder="1" />
      </SelectTrigger>
      <SelectContent class="max-h-60 overflow-auto">
        {#each pageList as option (option)}
          <SelectItem value="{option}">{option}</SelectItem>
        {:else}
          <SelectItem value="{1}">1</SelectItem>
        {/each}
      </SelectContent>
    </Select>

    <Button
      variant="outline"
      size="sm"
      disabled="{pagination.page === pagination.totalPages}"
      class="bg-background"
      on:click="{() => getPaginationPage(pagination.page + 1)}">
      Next
    </Button> -->

    <Pagination
      count="{pagination?.totalItems}"
      perPage="{pagination?.perPage}"
      page="{pagination?.page}"
      onPageChange="{getPaginationPage}"
      let:pages
      let:currentPage>
      <PaginationContent>
        <PaginationItem class="bg-background">
          <PaginationPrevButton />
        </PaginationItem>
        {#each pages as page (page.key)}
          {#if page.type === "ellipsis"}
            <PaginationItem class="bg-background">
              <PaginationEllipsis />
            </PaginationItem>
          {:else}
            <PaginationItem>
              <PaginationLink
                {page}
                isActive="{currentPage === page.value}"
                class="bg-background {currentPage === page.value ? 'border-primary text-primary bg-background' : ''}">
                {page.value}
              </PaginationLink>
            </PaginationItem>
          {/if}
        {/each}
        <PaginationItem class="bg-background">
          <PaginationNextButton />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  </div>
</div>
