<script>
  import { Button } from "$lib/components/ui/button";
  import { Card, CardContent, CardHeader } from "$lib/components/ui/card";
  import { RecurringStatus } from "$lib/common/enum";
  import { getRecurringAction } from "$lib/helpers/actionHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { isDebug, enablePaymentPage } from "$lib/stores/settings-store";
  import { handleError } from "$lib/helpers/errorHelper";
  import ConfirmationDialog from "$lib/components/recurring/confirmation-dialog.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import RecurringHeaderTitle from "$lib/components/recurring-preview/header-title.svelte";
  import LoadingPreview from "$lib/components/preview/loading-preview.svelte";
  import LoadingPreviewOption from "$lib/components/preview/loading-preview-option.svelte";
  import BasePreview from "$lib/components/template/BasePreview.svelte";
  import RecurringDate from "$lib/components/recurring-preview/recurring-date.svelte";
  import toast from "svelte-french-toast";
  import Status from "$lib/components/recurring-preview/status.svelte";
  import NextInvoiceDate from "$lib/components/recurring-preview/next-invoice-date.svelte";
  import ActionButtons from "$lib/components/recurring-preview/action-buttons.svelte";
  import CreatedBy from "$lib/components/preview/created-by.svelte";
  import Reminder from "$lib/components/preview/reminder.svelte";
  import InternalNote from "$lib/components/preview/internal-note.svelte";
  import RecurringInfo from "$lib/components/recurring-preview/recurring-info.svelte";
  import CreatedInvoices from "$lib/components/recurring-preview/created-invoices.svelte";
  import ActionHistory from "$lib/components/preview/action-history.svelte";

  export let params = {};

  const navList = location.hash.split("/");
  const nav = [
    { name: "Recurring", link: "recurrings" },
    { name: "List", link: `${navList[1]}/${navList[2]}` },
    { name: "Detail", link: `${navList[1]}/${navList[2]}/${navList[3]}` },
  ];

  let id;
  let recurring; // same with invoice
  let recurringDetail;
  let invoiceList = [];
  let hasMoreInvoice = false;
  let user;
  let actionHistory;
  let isPaid = false;
  let isLoading = false;
  let publicLink;
  let isDialogOpen = false;
  let actionMessageLowerCase;
  let actionMessage;
  let actionStatus;
  let isSendEmail = false;

  $: recurringActions = getRecurringAction(recurringDetail?.status);

  const actionHandler = ({ label, status }) => {
    if (status === RecurringStatus.EDIT) {
      window.location.href = `#/recurring/edit/${params.invoizeToken}`;
      return;
    }
    isDialogOpen = true;
    actionMessage = label;
    actionMessageLowerCase = label.toLowerCase();
    actionStatus = status;
  };

  const updateStatus = () => {
    if (isLoading) return;
    if (actionStatus === RecurringStatus.CREATE) {
      createInvoice();
      return;
    }
    isLoading = true;
    const payload = { id, recurringStatus: actionStatus };
    toast.promise(createPostRequest(`recurring/update`, payload), {
      loading: "Updating...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        // reset to null to display loading and prevent null error
        recurring = null;
        recurringDetail = null;
        getRecurringDetail();
        return "Status updated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to update status");
      },
    });
  };

  const createInvoice = () => {
    if (isLoading) return;
    isLoading = true;
    toast.promise(createPostRequest(`recurring/create-invoice`, { id, email: isSendEmail }), {
      loading: "Creating...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        // reset to null to display loading and prevent null error
        recurring = null;
        recurringDetail = null;
        getRecurringDetail();
        return "Invoice created successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to create invoice");
      },
    });
  };

  const getRecurringDetail = async () => {
    try {
      const response = await createGetRequest(`recurring/detail?token=${params.invoizeToken}`);
      $isDebug && console.log(response.data);
      id = response.data.id;
      recurring = response.data.invoice;
      recurringDetail = response.data.recurring;
      user = response.data.user;
      actionHistory = response.data.actionHistory;
      invoiceList = recurringDetail.createdInvoices.items ?? [];
      if (invoiceList.length > 0) {
        hasMoreInvoice =
          recurringDetail.createdInvoices.page < recurringDetail.createdInvoices.total_pages ? true : false;
      }
    } catch (err) {
      handleError(err, "Failed to fetch recurring invoice detail");
    }
  };

  onMount(() => {
    getRecurringDetail();
    createGetRequest("settings/get?key=payment.enablePaymentPage").then((res) => {
      $enablePaymentPage = res.data.value;
    });
  });

  $: dialogProps = {
    isLoading,
    actionMessage,
    actionMessageLowerCase,
    recurring,
    actionStatus,
  };

  $: recurringPreviewProps = {
    invoice: recurring,
    isPaid,
    note: recurring?.invoiceNote,
    isRecurring: true,
  };
</script>

<ConfirmationDialog on:updateStatus="{updateStatus}" bind:isDialogOpen bind:isSendEmail {...dialogProps} />
<Breadcrumb to="{nav}" />

<div class="flex flex-wrap xl:flex-nowrap justify-center xl:justify-center gap-8 mt-8">
  {#if !recurring && !recurringDetail}
    <LoadingPreview />
  {:else}
    <BasePreview {...recurringPreviewProps}>
      <svelte:fragment slot="header-title">
        <RecurringHeaderTitle />
      </svelte:fragment>
      <svelte:fragment slot="date">
        <RecurringDate dueDateInterval="{recurring.dueDateInterval}" />
      </svelte:fragment>
    </BasePreview>
  {/if}

  <!-- Actions -->
  <Card class="sticky top-16 h-fit rounded-md w-[800px] xl:w-80 space-y-4 print:hidden">
    {#if !recurring}
      <LoadingPreviewOption />
    {:else}
      <CardHeader class="bg-accent flex flex-row items-center justify-around">
        <Status status="{recurringDetail.status}" />
        <NextInvoiceDate nextInvoiceDate="{recurringDetail.nextInvoiceDate}" />
      </CardHeader>

      <CardContent class="space-y-4">
        <InternalNote internalNote="{recurring.invoiceNote.internalNote}" />
        <CreatedBy name="{user.name}" email="{user.email}" />
        <RecurringInfo recurring="{recurringDetail}" />
        <CreatedInvoices
          bind:createdInvoices="{recurringDetail.createdInvoices}"
          bind:invoiceList
          bind:hasMoreInvoice
          token="{params.invoizeToken}" />
        <Reminder reminder="{recurring.reminders}" />
        <ActionHistory {actionHistory} />
      </CardContent>
    {/if}
  </Card>
</div>
