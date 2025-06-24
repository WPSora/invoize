<script>
  import { Card, CardContent, CardHeader, CardTitle } from "$lib/components/ui/card";
  import download from "downloadjs";
  import { onMount } from "svelte";
  import toast from "svelte-french-toast";
  import LoadingPreview from "$lib/components/preview/loading-preview.svelte";
  import LoadingPreviewOption from "$lib/components/preview/loading-preview-option.svelte";
  import BasePreview from "$lib/components/template/BasePreview.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import moment from "moment";
  import Status from "$lib/components/preview/status.svelte";
  import InternalNote from "$lib/components/preview/internal-note.svelte";
  import CreatedBy from "$lib/components/preview/created-by.svelte";
  import Reminder from "$lib/components/preview/reminder.svelte";
  import ActionHistory from "$lib/components/preview/action-history.svelte";
  import { Archive, Copy, Download, ExternalLink, FolderSync, Loader2, Mail, Pencil, Printer } from "lucide-svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import Separator from "$lib/components/ui/separator/separator.svelte";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import { quotation as QuotationHelper } from "$lib/helpers/quotation";

  export let params = {};

  let id;
  let token;
  let quotation; // same with invoice
  let invoice = null;
  let user;
  let actionHistory;
  let isLoading = false;
  let publicLink;
  let isDialogOpen = false;
  let actionMessageLowerCase;
  let actionMessage;
  let actionStatus;
  let isSendEmail = false;
  let isPublic = false;

  const navList = location.hash.split("/");
  const nav = [
    { name: "Quotation", link: "quotations" },
    { name: "List", link: `${navList[1]}/${navList[2]}` },
    { name: "Detail", link: `${navList[1]}/${navList[2]}/${navList[3]}` },
  ];

  const setPublicLink = () => {
    publicLink = `${invoize.base_url}/invoize-quotation/${token}`;
  };

  const fetchDetail = async () => {
    try {
      quotation = await QuotationHelper.detail(token);
      user = quotation.user;
      id = quotation.id;
      actionHistory = quotation.actionHistory;
      invoice = quotation.invoice;
    } catch (err) {
      handleError(err, "Failed to fetch quotation detail");
    }
  };

  $: previewProps = {
    invoice: quotation, // invoice but quotation ?????
    note: quotation?.notes,
    publicLink: publicLink,
    isQuotation: true,
  };

  const ActionsLoading = {
    convertToInvoice: false,
    archive: false,
    send: false,
    download: false,
  };

  const Actions = {
    download: () => {
      ActionsLoading.download = true;
      createGetRequest(`download/quotation?token=${token}`).then((res) => {
        download(atob(res.data.content), `${res.data.filename}`, "application/pdf");
        ActionsLoading.download = false;
      });
    },
    send: () => {
      ActionsLoading.send = true;
      toast.promise(QuotationHelper.send(token), {
        loading: "Sending Quotation...",
        success: () => {
          ActionsLoading.send = false;
          // isDialogOpen = false;
          // triggerInvoiceTabUpdate(invoice.tab);
          // getInvoiceDetail();
          return "Quotation sent successfully";
        },
        error: (err) => {
          ActionsLoading.send = false;
          return handleError(err, err.message.response?.data?.message, false);
        },
      });
    },
    copy: () => {
      navigator.clipboard.writeText(publicLink);
      toast.success("Link copied to clipboard");
    },

    archive: () => {
      ActionsLoading.archive = true;
      toast.promise(QuotationHelper.archive(token), {
        loading: "Processing...",
        success: () => {
          ActionsLoading.archive = false;
          fetchDetail();
          return "Successfully archived";
        },
        error: (err) => {
          ActionsLoading.archive = false;
          return handleError(err, "Failed to archive");
        },
      });
    },
    print: () => {
      window.print();
    },
    convertToInvoice: () => {
      ActionsLoading.convertToInvoice = true;
      toast.promise(QuotationHelper.convertToInvoice(token), {
        loading: "Converting to invoice...",
        success: () => {
          ActionsLoading.convertToInvoice = false;
          fetchDetail();
          return "Successfully converted to invoice";
        },
        error: (err) => {
          ActionsLoading.convertToInvoice = false;
          return handleError(err, "Failed to convert to invoice");
        },
      });
    },
  };

  const getToken = () => {
    if (isPublic) {
      const path = window.location.pathname;
      const segments = path.split("/");
      token = segments.pop() || segments.pop();
    } else {
      token = params?.token;
    }
  }

  const checkIsPublic = () => {
    isPublic = window.location.href.includes(`${invoize.base_url}/invoize-quotation/`);
  }

  onMount(() => {
    checkIsPublic();
    getToken();
    setPublicLink();
    fetchDetail();
  });
</script>

{#if !isPublic}
  <div class="print:hidden">
    <Breadcrumb to="{nav}" />
  </div>
{/if}

<div class="flex flex-wrap xl:flex-nowrap justify-center xl:justify-center gap-8 mt-8" id="invoice-content-wrap">
  {#if quotation}
    <BasePreview {...previewProps}>
      <svelte:fragment slot="header-title">
        <div class="flex flex-col items-end">
          <CardTitle class="sm:text-[32px] text-[24px] font-bold mb-2">Quotation</CardTitle>
          <p class="text-base italic font-semibold text-muted-foreground">
            {quotation.quotationNumber}
          </p>
        </div>
      </svelte:fragment>
      <svelte:fragment slot="status">
        <div class="flex flex-row flex-nowrap">
          <div class="w-32 font-medium space-y-1">
            <div>Status</div>
          </div>
          <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
            <div>:</div>
          </div>
          <div class="flex flex-col items-end w-32 space-y-1 font-semibold">
            <div
              id="invoice-status"
              class="w-24 rounded-md text-center text-white {quotation.status == 'active'
                ? 'bg-green-600'
                : 'bg-gray-600'}">
              {quotation.status?.toUpperCase()}
            </div>
          </div>
        </div>
      </svelte:fragment>
      <svelte:fragment slot="date">
        <div class="flex flex-row flex-nowrap">
          <div class="w-32 font-medium space-y-1">
            <div>Quotation date</div>
            <div>Expired date</div>
          </div>
          <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
            <div>:</div>
            <div>:</div>
          </div>
          <div class="flex flex-col flex-nowrap items-end w-32 space-y-1">
            <div>
              {moment(quotation.quotationDate).format(invoize.date_format)}
            </div>
            <div>{moment(quotation.dueDate).format(invoize.date_format)}</div>
          </div>
        </div>
      </svelte:fragment>
    </BasePreview>
    <!-- Actions -->
    {#if !quotation}
      <LoadingPreviewOption />
    {:else}
      <Card class="sticky top-16 h-fit rounded-md w-[800px] xl:w-80 space-y-4 print:hidden">
        <CardHeader class="bg-accent flex flex-row items-center justify-around">
          <div class="flex flex-col justify-center items-center">
            <div class="font-medium">Status:</div>
            <div
              class="text-white font-bold px-2 rounded-md {quotation.status === 'active'
                ? 'bg-green-600'
                : 'bg-destructive'}">
              {capitalizeFirstLetter(quotation.status)}
            </div>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Button
              variant="outline"
              on:click="{Actions.download}"
              class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3"
              disabled="{ActionsLoading.download}">
              {#if !ActionsLoading.download}
                <Download class="h-4 w-4 text-success" />
                <div class="w-full text-start text-nowrap">Download Quotation as PDF</div>
              {:else}
                <Loader2 class="h-4 w-4 animate-spin text-primary" />
                <div class="w-full text-start text-nowrap">Processing...</div>
              {/if}
            </Button>
            <div class="grid {isPublic ? 'grid-cols-1' : 'grid-cols-2'} gap-x-1 gap-y-2">
              <Button
                on:click="{Actions.print}"
                variant="outline"
                class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3">
                <Printer class="h-4 w-4 text-primary" />
                <div class="w-full text-start text-nowrap">Print</div>
              </Button>
              {#if !isPublic}
                <Button
                  on:click="{Actions.copy}"
                  variant="outline"
                  class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3">
                  <Copy class="h-4 w-4 text-primary" />
                  <div class="w-full text-start text-nowrap">Copy Link</div>
                </Button>
              {/if}
            </div>
            <Separator />
            {#if invoice.length !== 0}
              <Button
                variant="outline"
                class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3"
                target="_blank"
                href="#/invoice/{invoice.token}">
                <ExternalLink class="h-4 w-4 text-primary" />
                <div class="w-full text-start text-nowrap">
                  Invoice {invoice.invoiceNumber}
                </div>
              </Button>
            {:else if quotation.status == "active" && !isPublic}
              <Button
                on:click="{Actions.convertToInvoice}"
                variant="outline"
                class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3"
                disabled="{ActionsLoading.convertToInvoice}">
                {#if ActionsLoading.convertToInvoice}
                  <Loader2 class="h-4 w-4 animate-spin text-primary" />
                  <div class="w-full text-start text-nowrap">Converting...</div>
                {:else}
                  <FolderSync class="h-4 w-4 text-primary" />
                  <div class="w-full text-start text-nowrap">Convert to Invoice</div>
                {/if}
              </Button>
            {/if}
            <div class="grid grid-cols-2 gap-x-1 gap-y-2">
              <!-- {#if status == "active"}
                <Button
                  variant="outline"
                  class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3"
                >
                  <Pencil class="h-4 w-4 text-blue-400" />
                  <div class="w-full text-start text-nowrap">Edit</div>
                </Button>
              {/if} -->
              {#if quotation.status == "active" && !isPublic}
                <Button
                  on:click="{Actions.send}"
                  variant="outline"
                  class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3">
                  <Mail class="h-4 w-4 text-blue-400" />
                  <div class="w-full text-start text-nowrap">Send</div>
                </Button>
              {/if}
              {#if quotation.status == "active" && !isPublic}
                <Button
                  on:click="{Actions.archive}"
                  variant="outline"
                  class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3">
                  {#if ActionsLoading.archive}
                    <Loader2 class="h-4 w-4 animate-spin text-warning" />
                    <div class="w-full text-start text-nowrap">Processing...</div>
                  {:else}
                    <Archive class="h-4 w-4 text-warning" />
                    <div class="w-full text-start text-nowrap">Archive</div>
                  {/if}
                </Button>
              {/if}
            </div>
          </div>
          {#if !isPublic}
            <InternalNote internalNote="{quotation.internalNote}" />
            <CreatedBy name="{user.name}" email="{user.email}" />
            <Reminder reminder="{quotation.reminders}" />
            <ActionHistory {actionHistory} />
            <!-- <RecurringInfo recurring={recurringDetail} /> -->
            <!-- <CreatedInvoices
            bind:createdInvoices={recurringDetail.createdInvoices}
            bind:invoiceList
            bind:hasMoreInvoice
            token={token} /> -->
          {/if}
        </CardContent>
      </Card>
    {/if}
  {:else}
    <LoadingPreview />
  {/if}
</div>

<style>
  @media print {
    @page {
      margin: 0;
    }
  }
</style>
