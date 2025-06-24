<script>
  import { createGetRequest } from "$lib/helpers/request";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Input } from "$lib/components/ui/input";
  import { isDebug, isWcInstalled } from "$lib/stores/settings-store";
  import { Label } from "$lib/components/ui/label";
  import { onMount } from "svelte";
  import { Loader2, Search, X } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { Switch } from "$lib/components/ui/switch";
  import { Plus } from "radix-icons-svelte";
  import { Separator } from "$lib/components/ui/separator";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import { handleError } from "$lib/helpers/errorHelper";
  import { getContext } from "svelte";

  export let isModalOpen = false;
  export let dataStore;
  export let selectedDataStore;
  export let api;
  export let detailApi = null;
  export let perPage = 10;
  export let wcApi;
  export let listStore;
  export let wcListStore;
  export let searchResult;
  export let searchWcResult;
  export let hasLabel = true;
  export let name;
  /** optional. fired when an item is selected
   * @function
   */
  export let callback = (e) => {};
  /** use this to update the currently selected.
   * updating this wil also update selectedDataStore.
   * {label: string, value: any}
   */
  export let selectedBind = null;
  $: $isDebug && console.log({ selectedBind });

  let searchQuery;
  let searchWcQuery;
  let isSelectOpen = false;

  export let isWcData = false;
  let hasWcData = false;
  let isFetchingMoreData = false;
  let isLoadingData = false;
  let isLoadingWcData = false;

  let lastDataPage = 0;
  let lastWcDataPage = 0;
  let lastSearchDataPage = 0;
  let lastSearchWcDataPage = 0;

  let currentDataPage = 0;
  let currentWcDataPage = 0;
  let currentSearchDataPage = 0;
  let currentSearchWcDataPage = 0;

  let maxDataPage = 0;
  let maxWcDataPage = 0;
  let maxSearchDataPage = 0;
  let maxSearchWcDataPage = 0;

  // to know if currently loading
  let isSearchingData = false;
  let isSearchingWcData = false;
  // to know if on search state/search input has data
  export let isSearchingState = false;
  export let isSearchingWcState = false;

  let clientIdParam = getContext("clientIdParam");

  // basically just getting the ACTUAL value, without label & disabled value
  // this also will update store value if this is passed as store.
  // so updating selectedBind WILL ALSO update selectedDataStore
  $: selectedDataStore = selectedBind?.value ?? selectedDataStore;
  $: capitalName = capitalizeFirstLetter(name);

  /** @async
   * @function
   */
  const getDataList = async () => {
    try {
      isLoadingData = true;
      const response = await createGetRequest(`${api}?per_page=${perPage}`);
      const { items, total_pages, page } = response.data;
      listStore = items;
      maxDataPage = total_pages;
      lastDataPage = page;
      currentDataPage = page;
      isLoadingData = false;
    } catch (err) {
      isLoadingData = false;
      handleError(err, `Failed to retrieve ${name} data`);
    }
  };

  /**
   * @async
   * @function
   * @param {boolean} e
   */
  const getWcDataList = async (e) => {
    if (hasWcData) {
      return;
    }
    if (e) {
      isLoadingWcData = true;
      try {
        const response = await createGetRequest(`${wcApi}?per_page=${perPage}`);
        const { items, total_pages, page } = response.data;
        wcListStore = includeVariations(items);
        $isDebug && console.log({ wcListStore });
        maxWcDataPage = total_pages;
        lastWcDataPage = page;
        currentWcDataPage = page;
        hasWcData = true;
        isLoadingWcData = false;
      } catch (err) {
        isLoadingWcData = false;
        handleError(err, `Failed to retrieve Woocommerce ${name} data`);
      }
    }
  };

  /**
   * @async
   * @function
   */
  const getMoreData = async () => {
    try {
      isFetchingMoreData = true;
      lastDataPage++;
      const response = await createGetRequest(`${api}?per_page=${perPage}&page=${lastDataPage}`);
      const { items, page } = response.data;
      // This will remove duplicate data on the fetch data
      const moreData = items.filter((item) => !listStore.some((data) => data.id === item.id));
      listStore = [...listStore, ...moreData];
      currentDataPage = page;
      isFetchingMoreData = false;
    } catch (err) {
      isFetchingMoreData = false;
      handleError(err, `Failed to retrieve ${name} data`);
    }
  };

  /**
   * @async
   * @function
   */
  const getMoreWcData = async () => {
    try {
      isFetchingMoreData = true;
      lastWcDataPage++;
      const response = await createGetRequest(`${wcApi}?per_page=${perPage}&page=${lastWcDataPage}`);
      const { items, page } = response.data;
      const hasVarian = includeVariations(items);
      // This will remove duplicate data on the fetch data
      const moreData = hasVarian.filter((item) => !wcListStore.some((data) => data.id === item.id));
      wcListStore = [...wcListStore, ...moreData];
      currentWcDataPage = page;
      isFetchingMoreData = false;
    } catch (err) {
      isFetchingMoreData = false;
      handleError(err, `Failed to retrieve Woocommerce ${name}`);
    }
  };

  /**
   * @async
   * @function
   */
  const searchData = async () => {
    // when user search query is empty, we show non-search result
    if (!searchQuery) {
      isSearchingState = false;
      return;
    }
    try {
      isSearchingData = true;
      isSearchingState = true;
      const response = await createGetRequest(`${api}?per_page=${perPage}&search=${searchQuery}`);
      const { items, total_pages, page } = response.data;
      searchResult = items;
      $isDebug && console.log({ searchResult });
      maxSearchDataPage = total_pages;
      lastSearchDataPage = page;
      currentSearchDataPage = page;
      isSearchingData = false;
    } catch (err) {
      isSearchingData = false;
      handleError(err, `Failed to retrieve ${name}`);
    }
  };

  /**
   * @async
   * @function
   */
  const searchWcData = async () => {
    // when user search query is empty, we show non-search result
    if (!searchWcQuery) {
      isSearchingWcState = false;
      return;
    }
    try {
      isSearchingWcData = true;
      isSearchingWcState = true;
      const response = await createGetRequest(`${wcApi}?per_page=${perPage}&search=${searchWcQuery}`);
      const { items, total_pages, page } = response.data;
      searchWcResult = includeVariations(items);
      $isDebug && console.log({ searchWcResult });
      maxSearchWcDataPage = total_pages;
      lastSearchWcDataPage = page;
      currentSearchWcDataPage = page;
      isSearchingWcData = false;
    } catch (err) {
      isSearchingWcData = false;
      handleError(err, `Failed to retrieve Woocommerce ${name}`);
    }
  };

  /** @async
   * @function
   */
  const getMoreSearchData = async () => {
    try {
      isFetchingMoreData = true;
      lastSearchDataPage++;
      const response = await createGetRequest(
        `${api}?per_page=${perPage}&page=${lastSearchDataPage}&search=${searchQuery}`,
      );
      const { items, page } = response.data;
      // This will remove duplicate data on the fetch data
      const moreData = items.filter((item) => !searchResult.some((data) => data.id === item.id));
      searchResult = [...searchResult, ...moreData];
      currentSearchDataPage = page;
      isFetchingMoreData = false;
    } catch (err) {
      isFetchingMoreData = false;
      handleError(err, `Failed to retrieve ${name} data`);
    }
  };

  /**
   * @async
   * @function
   */
  const getMoreSearchWcData = async () => {
    try {
      isFetchingMoreData = true;
      lastSearchWcDataPage++;
      const response = await createGetRequest(
        `${wcApi}?per_page=${perPage}&page=${lastSearchWcDataPage}&search=${searchWcQuery}`,
      );
      const { items, page } = response.data;
      const withVarian = includeVariations(items);
      // This will remove duplicate data on the fetch data
      const moreData = withVarian.filter((item) => !searchWcResult.some((data) => data.id === item.id));
      searchWcResult = [...searchWcResult, ...moreData];
      $isDebug && console.log({ searchWcResult });
      currentSearchWcDataPage = page;
      isFetchingMoreData = false;
    } catch (err) {
      isFetchingMoreData = false;
      handleError(err, `Failed to retrieve Woocommerce ${name}`);
    }
  };

  const getClient = async () => {
    if (!detailApi) return;
    try {
      const response = await createGetRequest(`${detailApi}?id=${clientIdParam}`);
      const { data } = response;
      selectedBind = { label: data.name, value: data };
    } catch (err) {
      handleError(err, "Failed to retriever customer data");
    }
  };

  const handleAddData = () => {
    dataStore = {};
    isSelectOpen = false;
    isModalOpen = true;
  };

  const clearSearch = () => {
    searchQuery = null;
    isSearchingState = false;
  };

  const clearWcSearch = () => {
    searchWcQuery = null;
    isSearchingWcState = false;
  };

  const includeVariations = (items) => {
    let result = [];
    items.map((item) => {
      result.push(item);
      item.variation && item.variation.map((varian) => result.push(varian));
    });
    return result;
  };

  onMount(() => {
    getDataList();
    // if there's parameter on url, then set the default selected client
    if (clientIdParam) {
      getClient();
    }

    return () => {
      selectedBind = null;
      selectedDataStore = null;
    };
  });
</script>

{#if hasLabel}
  <Label for="{name}" class="text-sm">{capitalName}</Label>
{/if}
<Select
  required
  disabled="{isLoadingData}"
  onSelectedChange="{(e) => callback && callback(e)}"
  bind:selected="{selectedBind}"
  bind:open="{isSelectOpen}">
  <SelectTrigger id="{name}" class="h-fit">
    {#if isLoadingData}
      <div class="flex flex-row flex-nowrap gap-x-2 items-center">
        <Loader2 class="w-4 h-4 animate-spin text-primary" />
        Fetching {name}s...
      </div>
    {:else}
      <SelectValue placeholder="{`Choose ${name}`}" class="text-start" />
    {/if}
  </SelectTrigger>

  <SelectContent class="h-60 overflow-y-scroll">
    <!-- Search wc input -->
    {#if isWcData}
      <form on:submit|preventDefault="{searchWcData}" class="flex items-center gap-x-2 px-2 pt-2">
        <Button
          variant="outline"
          size="icon"
          class="aspect-square text-primary hover:bg-primary hover:text-white"
          type="submit">
          <Search class="h-4 w-4" />
        </Button>
        <Input
          type="text"
          placeholder="{`Search ${name}s`}"
          bind:value="{searchWcQuery}"
          on:keydown="{(e) => {
            // this makes whenever user type a letter that exist in the list of value,
            // it will prevent blur input & prevent focus on the selection
            e.stopPropagation();
          }}" />
        {#if searchWcQuery}
          <Button variant="link" size="icon" type="reset" class="absolute right-2" on:click="{clearWcSearch}">
            <X class="h-5 w-5 text-destructive" />
          </Button>
        {/if}
      </form>
      <!-- Search input -->
    {:else}
      <form on:submit|preventDefault="{searchData}" class="flex items-center gap-x-2 px-2 pt-2">
        <Button
          variant="outline"
          size="icon"
          class="aspect-square text-primary hover:bg-primary hover:text-white"
          type="submit">
          <Search class="h-4 w-4" />
        </Button>
        <Input
          type="text"
          placeholder="{`Search ${name}s`}"
          bind:value="{searchQuery}"
          on:keydown="{(e) => {
            // this makes whenever user type a letter that exist in the list of value,
            // it will prevent blur input & prevent focus on the selection
            e.stopPropagation();
          }}" />
        {#if searchQuery}
          <Button variant="link" size="icon" type="reset" class="absolute right-2" on:click="{clearSearch}">
            <X class="h-5 w-5 text-destructive" />
          </Button>
        {/if}
      </form>
    {/if}

    <div class="flex items-center text-xs {isWcData ? 'justify-end' : 'justify-between'} p-2">
      <!-- New data button -->
      {#if !isWcData}
        <Button variant="outline" size="sm" on:click="{handleAddData}">
          <Plus class="h-4 w-4 mr-1 text-primary" />
          New
        </Button>
      {/if}
      <!-- Woocommerce data switch -->
      {#if $isWcInstalled}
        <div class="flex justify-end items-center gap-x-2 h-8">
          <Label for="{`wc-${name}`}" class="text-xs">Woocommerce {name}s</Label>
          <Switch id="{`wc-${name}`}" bind:checked="{isWcData}" onCheckedChange="{getWcDataList}" />
        </div>
      {:else}
        <div class="text-xs text-wrap text-center italic text-muted-foreground">
          <div>Can't sync.</div>
          <div>Woocommerce is not installed.</div>
        </div>
      {/if}
    </div>

    <Separator class="w-11/12 mx-auto my-1" />

    <!-- List of Data -->
    {#if isLoadingWcData}
      <!-- Loading -->
      <SelectItem disabled value="loading" class="flex flex-nowrap items-end justify-center">
        <Loader2 class="h-4 w-4 mr-2 animate-spin text-primary" />
        Loading ...
      </SelectItem>
    {:else}
      <!-- Woocommerce data list -->
      {#if isWcData}
        {#if isSearchingWcData}
          <div class="flex flex-nowrap justify-center text-xs text-muted-foreground items-center">
            <Loader2 class="h-4 w-4 mr-2 animate-spin text-primary" />
            Searching...
          </div>
        {:else if isSearchingWcState}
          {#each searchWcResult as data (data.id)}
            <SelectItem value="{data}" label="{data.name}">
              <div class="w-full flex justify-between">
                <div>{data.name}</div>
                <slot name="item-right-side" {data} />
              </div>
            </SelectItem>
          {:else}
            <div class="text-muted-foreground italic flex justify-center text-sm pt-3">
              {capitalName} not found
            </div>
          {/each}
        {:else}
          <!-- Normal state -->
          {#each wcListStore as data (data.id)}
            <SelectItem value="{data}" label="{data.name}">
              <div class="w-full flex justify-between">
                <div>{data.name}</div>
                <slot name="item-right-side" {data} />
              </div>
            </SelectItem>
          {:else}
            <div class="text-muted-foreground italic flex justify-center text-sm pt-3">{capitalName} not found</div>
          {/each}
        {/if}

        <!-- Invoize data list -->
      {:else if !isWcData}
        {#if isSearchingData}
          <div class="flex flex-nowrap justify-center text-xs text-muted-foreground items-center">
            <Loader2 class="h-4 w-4 mr-2 animate-spin text-primary" />
            Searching...
          </div>
        {:else if isSearchingState}
          {#each searchResult as data (data.id)}
            <SelectItem value="{data}" label="{data.name}">
              <div class="w-full flex justify-between items-center">
                <div>{data.name}</div>
                <slot name="item-right-side" {data} />
              </div>
            </SelectItem>
          {:else}
            <div class="text-muted-foreground italic flex justify-center text-sm pt-3">{capitalName} not found</div>
          {/each}
        {:else}
          <!-- Normal state -->
          {#each listStore as data (data.id)}
            <SelectItem value="{data}" label="{data.name}">
              <div class="w-full flex justify-between items-center">
                <div>{data.name}</div>
                <slot name="item-right-side" {data} />
              </div>
            </SelectItem>
          {:else}
            <div class="text-muted-foreground italic flex justify-center text-sm pt-3">{capitalName} not found</div>
          {/each}
        {/if}
      {/if}

      <!-- Loading more data -->
      {#if isFetchingMoreData}
        <div class="flex flex-nowrap justify-center text-xs text-muted-foreground items-center">
          <Loader2 class="h-3 w-3 mr-2 animate-spin text-primary" />
          Loading
        </div>
      {:else if isWcData}
        <!-- Load more Wc data -->
        <!-- Searching state -->
        {#if isSearchingWcState && maxSearchWcDataPage > currentSearchWcDataPage && !isSearchingWcData}
          <Button class="text-xs flex justify-center w-full" variant="link" on:click="{getMoreSearchWcData}">
            Load more
          </Button>
          <!-- Not searching state -->
        {:else if !isSearchingWcState && maxWcDataPage > currentWcDataPage && !isSearchingWcData}
          <Button class="text-xs flex justify-center w-full" variant="link" on:click="{getMoreWcData}"
            >Load more</Button>
        {/if}
      {:else if !isWcData}
        <!-- Load more invoize data -->
        <!-- Searching state -->
        {#if isSearchingState && maxSearchDataPage > currentSearchDataPage && !isSearchingData}
          <Button class="text-xs flex justify-center w-full" variant="link" on:click="{getMoreSearchData}">
            Load more
          </Button>
          <!-- Not searching state -->
        {:else if !isSearchingState && maxDataPage > currentDataPage && !isSearchingData}
          <Button class="text-xs flex justify-center w-full" variant="link" on:click="{getMoreData}">Load more</Button>
        {/if}
      {/if}
    {/if}
  </SelectContent>
</Select>
