<script>
  import { Button } from "$lib/components/ui/button";
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest } from "$lib/helpers/request";
  import { Loader2 } from "lucide-svelte";
  import moment from "moment";
  import toast from "svelte-french-toast";

  export let createdInvoices;
  export let invoiceList;
  export let hasMoreInvoice;
  export let token;
  let isLoadingMore = false;

  const loadMoreInvoice = async () => {
    try {
      isLoadingMore = true;
      const response = await createGetRequest(`recurring/invoice-list?token=${token}&page=${createdInvoices.page + 1}`);
      const { items, page, total_pages } = response.data;
      createdInvoices = items;
      invoiceList = [...invoiceList, ...items];
      hasMoreInvoice = page < total_pages ? true : false;
      isLoadingMore = false;
    } catch (err) {
      isLoadingMore = false;
      handleError(err, "Failed to load more invoices");
    }
  };
</script>

{#if invoiceList.length > 0}
  <div class="space-y-2">
    <div class="font-medium">Invoice Created</div>
    <div class="max-h-28 overflow-y-auto">
      {#each invoiceList as invoice}
        <div class="flex justify-between items-center text-xs">
          <Button
            variant="link"
            on:click={() => (location.href = `#/invoice/${invoice.token}`)}
            class="text-xs h-fit py-1 pl-2">
            {invoice.name}
          </Button>
          <div>{moment(invoice.invoiceDate).format(invoize.date_format)}</div>
        </div>
      {/each}
      {#if hasMoreInvoice && isLoadingMore}
        <div class="w-full flex justify-center items-center text-xs text-muted-foreground">
          <Loader2 class="w-4 h-4 animate-spin text-primary mr-1" />
          Loading
        </div>
      {:else if hasMoreInvoice && !isLoadingMore}
        <Button variant="link" on:click={loadMoreInvoice} class="w-full py-0 text-xs">Load more</Button>
      {/if}
    </div>
  </div>
{/if}
