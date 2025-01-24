<script>
  import { createGetRequest } from "$lib/helpers/request";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { businesses as invoiceBusinesses, selectedBusiness, selectedBusinessInput } from "$lib/stores/invoice-store";
  import { defaultBusinessId } from "$lib/stores/settings-store";
  import { Label } from "$lib/components/ui/label";
  import { onMount } from "svelte";
  import { Loader2 } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { handleError } from "$lib/helpers/errorHelper";

  /** @type {boolean} */
  export let isLoading = false;

  /** @type {boolean} */
  export let isEdit = false;

  /** @type {boolean} */
  let open = false;

  /** @type {boolean} */
  let isFetchingMore = false;

  /** @type {number}*/
  let lastPage = 1;

  /** @type {number} */
  let maxPage = 1;

  $: $selectedBusiness = $selectedBusinessInput?.value;

  /** @function */
  const getBusinesses = () => {
    isLoading = true;
    createGetRequest("settings/retrieve?tab=business")
      .then((res) => {
        const defaultBusiness = res.data.data.find((item) => item.name === "default");
        $defaultBusinessId = defaultBusiness ? parseInt(defaultBusiness.value) : $defaultBusinessId;
        return createGetRequest("business/list");
      })
      .then((response) => {
        const { items, total_pages } = response.data;
        if (items.length > 0) {
          $invoiceBusinesses = items;
          $selectedBusiness = items.find((item) => item.id === $defaultBusinessId);
          $selectedBusinessInput = { label: $selectedBusiness.business_name, value: $selectedBusiness };
        }
        maxPage = total_pages;
        isLoading = false;
      })
      .catch((err) => {
        isLoading = false;
        handleError(err, "Failed to retrieve businesses data");
      });
  };

  /** @async
   * @function
   */
  const getBusinessList = async () => {
    try {
      isLoading = true;
      const res = await createGetRequest("business/list");
      const { items, total_pages } = res.data;
      $invoiceBusinesses = items.length > 0 ? items : $invoiceBusinesses;
      maxPage = total_pages;
      isLoading = false;
    } catch (err) {
      isLoading = false;
      handleError(err, "Failed to retireve business list");
    }
  };

  /** @async
   * @function
   */
  const getMoreBusiness = async () => {
    try {
      isFetchingMore = true;
      lastPage++;
      const response = await createGetRequest(`business/list?per_page=5&page=${lastPage}`);
      $invoiceBusinesses = [...$invoiceBusinesses, ...response.data.items];
      isFetchingMore = false;
    } catch (err) {
      handleError(err, "Failed to retrieve more businesses data");
    }
  };

  onMount(() => {
    // on edit, get business list
    // on create, get default business and business list
    !isEdit ? getBusinesses() : getBusinessList();
  });
</script>

<!-- Basic Select. If you need checkbox before showing the Select, then use Select-2 component -->
<Label for="issue" class="text-sm">Issued by</Label>
{#if isLoading}
  <div class="flex flex-row flex-nowrap gap-x-2 items-center text-muted-foreground text-sm">
    <Loader2 class="w-4 h-4 animate-spin text-primary" />
    Fetching businesses...
  </div>
{/if}
<div class="hidden">
  <Select required disabled={isLoading} bind:selected={$selectedBusinessInput} {open}>
    <SelectTrigger id="issue">
      {#if isLoading}
        <div class="flex flex-row flex-nowrap gap-x-2 items-center">
          <Loader2 class="w-4 h-4 animate-spin text-primary" />
          Fetching businesses...
        </div>
      {:else}
        <SelectValue placeholder="Choose issuer" />
      {/if}
    </SelectTrigger>
    <SelectContent class="max-h-60 overflow-y-auto">
      {#each $invoiceBusinesses as business}
        <SelectItem value={business} label={business.business_name}>
          {business.business_name}
        </SelectItem>
      {:else}
        <div class="p-2 text-sm text-muted-foreground">
          <div>No business saved</div>
          <a href="#/setting" class="text-xs text-blue-500">Go to Setting</a>
        </div>
      {/each}
      {#if isFetchingMore}
        <div class="flex flex-nowrap justify-center text-xs text-muted-foreground items-center">
          <Loader2 class="h-3 w-3 mr-2 animate-spin text-primary" />
          Loading
        </div>
      {/if}
      {#if lastPage < maxPage}
        <Button class="w-full flex flex-nowrap justify-center" variant="link" on:click={getMoreBusiness}>
          Load more
        </Button>
      {/if}
    </SelectContent>
  </Select>
</div>
