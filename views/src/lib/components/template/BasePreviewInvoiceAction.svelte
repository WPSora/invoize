<script>
  import { Card, CardHeader, CardContent } from "$lib/components/ui/card";
  import { Separator } from "$lib/components/ui/separator";
  import { getInvoiceActions } from "$lib/helpers/actionHelper";
  import { PaymentStatus, InvoiceStatus as InvoiceStatusEnum } from "$lib/common/enum";
  import UserActionButtons from "$lib/components/preview/user-action-buttons.svelte";
  import CustomerActionButtons from "$lib/components/preview/customer-action-buttons.svelte";
  import ActionHistory from "$lib/components/preview/action-history.svelte";
  import CreatedBy from "$lib/components/preview/created-by.svelte";
  import InternalNote from "$lib/components/preview/internal-note.svelte";
  import Reminder from "$lib/components/preview/reminder.svelte";
  import LoadingPreviewOption from "$lib/components/preview/loading-preview-option.svelte";
  import ExpiredTime from "$lib/components/preview/expired-time.svelte";
  import ActionStatus from "$lib/components/preview/action-status.svelte";
  import ConfirmationDialog from "$lib/components/preview/confirmation-dialog.svelte";
  import { toast } from "svelte-french-toast";
  import { createPostRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";

  export let token;
  export let page;
  export let publicLink;
  export let isPublic;
  export let invoice;
  export let isLoading;
  export let isDialogOpen;
  export let actionMessage;
  export let actionMessageLowerCase;
  export let actionStatus;
  export let selectedStatusType;
  export let isWoocommerce;

  $: invoiceActions = getInvoiceActions(invoice?.tab);
  $: actionHistory = invoice?.actionHistory ?? [];

  const actionHandler = ({ label, status }) => {
    if (status === InvoiceStatusEnum.EDIT) {
      window.location.href = `#/invoice/edit/${token}`;
      return;
    }

    isDialogOpen = true;
    actionMessage = label;
    actionMessageLowerCase = label.toLowerCase();
    actionStatus = status;
    const isPaymentStatus = status === PaymentStatus.PAID || status === PaymentStatus.UNPAID;
    selectedStatusType = isPaymentStatus ? "paymentStatus" : "invoiceStatus";
  };

  $: dialogProps = {
    actionMessageLowerCase,
    actionMessage,
    actionStatus,
    paymentStatus: invoice?.paymentStatus,
    invoiceNumber: invoice?.invoiceNumber,
    client: invoice?.client,
    isWoocommerce,
  };

  $: customerActionProps = {
    id: invoice?.id,
    token: invoice?.token,
    publicLink,
    paymentStatus: invoice?.paymentStatus,
  };
</script>

<ConfirmationDialog on:updateStatus bind:isDialogOpen bind:isLoading {...dialogProps} />

<Card class="sticky top-16 h-fit rounded-md w-[800px] xl:w-80 space-y-4 print:hidden">
  {#if !invoice}
    <LoadingPreviewOption {isPublic} />
  {:else}
    <CardHeader class="bg-accent flex flex-row items-center justify-around py-3">
      <ActionStatus paymentStatus="{invoice.paymentStatus}" invoiceStatus="{invoice.invoiceStatus}" {isPublic} />
      {#if invoice.paymentStatus === PaymentStatus.UNPAID}
        <ExpiredTime dueDate="{invoice.dueDate}" />
      {/if}
    </CardHeader>

    <!-- Action buttons -->
    <CardContent class="space-y-4">
      <CustomerActionButtons {...customerActionProps} />

      {#if !isPublic}
        <Separator />
      {/if}

      {#if !isPublic && page === "invoice"}
        <UserActionButtons
          {invoiceActions}
          {actionHandler}
          isRecurring="{invoice.recurring}"
          isSent="{invoice.isSent}" />
      {/if}

      {#if !isPublic}
        <InternalNote internalNote="{invoice.invoiceNote?.internalNote}" />
      {/if}

      {#if invoice?.user && !isPublic}
        <CreatedBy email="{invoice.user.email}" name="{invoice.user.name}" />
      {/if}

      {#if !isPublic && page === "invoice"}
        <Reminder reminder="{invoice.reminders}" />
      {/if}

      {#if !isPublic && page === "invoice"}
        <ActionHistory {actionHistory} />
      {/if}
    </CardContent>
  {/if}
</Card>
