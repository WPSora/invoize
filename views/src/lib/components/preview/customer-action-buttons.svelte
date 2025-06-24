<script>
  import download from "downloadjs";
  import { Download, Copy } from "radix-icons-svelte";
  import { Button } from "$lib/components/ui/button";
  import { PaymentStatus } from "$lib/common/enum";
  import { Check, Printer } from "lucide-svelte";
  import { slide } from "svelte/transition";
  import { Loader2 } from "lucide-svelte";
  import toast from "svelte-french-toast";
  import { createGetRequest } from "$lib/helpers/request";

  export let token;
  export let publicLink;
  export let paymentStatus;

  let isDownloadingInvoice = false;
  let isDownloadingReceipt = false;
  let isShowCheck = false;

  const downloadInvoice = () => {
    isDownloadingInvoice = true;
    createGetRequest(`download/invoice?token=${token}`).then((res) => {
      download(atob(res.data.content), `${res.data.filename}`, "application/pdf");
      isDownloadingInvoice = false;
    });
  };

  const downloadReceipt = () => {
    isDownloadingReceipt = true;
    createGetRequest(`download/receipt?token=${token}`).then((res) => {
      download(atob(res.data.content), `${res.data.filename}`, "application/pdf");
      isDownloadingReceipt = false;
    });
  };

  const printInvoice = () => {
    window.print();
  };

  const copyLink = () => {
    navigator.clipboard.writeText(publicLink);
    toast.success("Link copied to clipboard");
    isShowCheck = true;
    setTimeout(() => {
      isShowCheck = false;
    }, 2000);
  };
</script>

<style>
  @media print {
    @page {
      margin: 0;
    }
  }
</style>

<div class="space-y-2">
  <!-- Download Invoice -->
  <Button
    on:click={downloadInvoice}
    disabled={isDownloadingInvoice}
    variant="outline"
    class="w-full flex flex-row flex-nowrap justify-start items-center text-xs">
    {#if isDownloadingInvoice}
      <Loader2 class="h-4 w-4 mr-3 text-primary animate-spin" />
    {:else}
      <Download class="h-4 w-4 mr-3 text-green-600" />
    {/if}
    Download Invoice as PDF
  </Button>
  <!-- Download Receipt -->
  {#if paymentStatus === PaymentStatus.PAID}
    <Button
      on:click={downloadReceipt}
      disabled={isDownloadingReceipt}
      variant="outline"
      class="w-full flex flex-row flex-nowrap justify-start items-center text-xs">
      {#if isDownloadingReceipt}
        <Loader2 class="h-4 w-4 mr-3 text-primary animate-spin" />
      {:else}
        <Download class="h-4 w-4 mr-3 text-cyan-600" />
      {/if}
      Download Receipt as PDF
    </Button>
  {/if}

  <div class="flex gap-x-2">
    <!-- Print -->
    <Button variant="outline" class="w-full flex flex-row flex-nowrap justify-start text-xs" on:click={printInvoice}>
      <Printer class="h-4 w-4 mr-3 text-primary" />
      Print
    </Button>

    <!-- Copy -->
    <Button variant="outline" class="w-full flex flex-row flex-nowrap justify-start text-xs" on:click={copyLink}>
      <div class="grid grid-cols-1">
        {#if isShowCheck}
          <div transition:slide>
            <Check class="h-4 w-4 mr-3 text-green-600 col-span-1" />
          </div>
        {:else}
          <Copy class="h-4 w-4 mr-3 text-primary col-span-1" />
        {/if}
      </div>
      Copy link
    </Button>
  </div>
</div>
