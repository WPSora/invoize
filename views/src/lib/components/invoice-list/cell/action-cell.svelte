<script>
  import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuGroup,
    DropdownMenuSeparator,
  } from "$lib/components/ui/dropdown-menu";
  import {
    Eye,
    Trash,
    Package,
    Send,
    Settings2,
    Files,
    CircleDollarSign,
    Ban,
    History,
    RefreshCcw,
    Loader2,
    Pencil,
    CalendarClock,
  } from "lucide-svelte";
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { createPostRequest } from "$lib/helpers/request";
  import { getInvoiceActions } from "$lib/helpers/actionHelper";
  import { PaymentStatus, PaymentStatusAction, InvoiceStatus, InvoiceListTab } from "$lib/common/enum";
  import { receiptStartFromNumber } from "$lib/stores/settings-store";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Button } from "$lib/components/ui/button";
  import { toast } from "svelte-french-toast";
  import moment from "moment";
  import CreateRecurringModal from "$lib/components/recurring/CreateRecurringModal.svelte";

  export let id;
  export let token;
  export let prefix;
  export let clientName;
  export let paymentStatus;
  export let tab;
  export let dueDate;
  export let isWoocommerce;
  export let updateTabList = (tab1, tab2) => {};

  let isLoading;
  let isDialogOpen;
  let payloadKey;
  let tab1;
  let tab2;
  let isSendEmail = false;
  // for dialog button message
  let actionMessage;
  // action to do / tab to go
  let actionStatus;

  let isCreateRecurringModalOpen = false;
  let recurringPayload;
  let isRecurringModalLoading = false;
  /** @type {number} */
  let recurringDueDateInterval = 7;

  const invoiceActions = getInvoiceActions(tab);

  const actionHandler = ({ label, status }) => {
    // console.log(status);
    if (status === InvoiceStatus.EDIT) {
      window.location.href = `#/invoice/edit/${token}`;
      return;
    } else if (status === InvoiceStatus.TO_RECURRING) {
      isCreateRecurringModalOpen = true;
      return;
    }

    isDialogOpen = true;
    tab1 = tab;
    // update tab2 based on action that want to do
    tab2 = status !== InvoiceStatus.ACTIVE ? status : paymentStatus;

    // if want to RESTORE from TRASH, CANCEL, ARCHIVE tab to UNPAID tab AND it's expired, update EXPIRED tab instead.
    if (tab2 === PaymentStatus.UNPAID && moment(dueDate).isBefore(moment())) {
      tab2 = InvoiceStatus.EXPIRED;
    }
    actionMessage = label.toLowerCase();
    actionStatus = status;

    if (status === PaymentStatus.PAID || status === PaymentStatus.UNPAID) {
      payloadKey = "paymentStatus";
    } else if (
      status === InvoiceStatus.ACTIVE ||
      status === InvoiceStatus.ARCHIVED ||
      status === InvoiceStatus.CANCELLED ||
      status === InvoiceStatus.TRASHED
    ) {
      payloadKey = "invoiceStatus";
    }
  };

  // duplicate and send won't run here
  const updateStatus = () => {
    isLoading = true;
    const payload = { id, [payloadKey]: actionStatus };

    toast.promise(createPostRequest(`invoice/update?email=${isSendEmail}`, payload), {
      loading: "Updating...",
      success: () => {
        updateSettingStore();
        isLoading = false;
        isDialogOpen = false;
        // this is what trigger the refresh on each affected tabs
        updateTabList(tab1, tab2);
        return "Invoice updated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to update invoice status", false);
      },
    });
  };

  const duplicateInvoice = () => {
    isLoading = true;
    toast.promise(createPostRequest(`invoice/duplicate`, { id }), {
      loading: "Duplicating...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        // this is what trigger the refresh on each affected tabs.
        // if expired, duplicate to expired tab instead.
        if (moment(dueDate).isBefore(moment())) {
          updateTabList(InvoiceListTab.EXPIRED.LOWER_CASE, null);
        }
        updateTabList(InvoiceListTab.UNPAID.LOWER_CASE, null);
        return "Invoice duplicated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to duplicate invoice", false);
      },
    });
  };

  const updateSettingStore = () => {
    if (actionStatus === PaymentStatus.PAID) {
      $receiptStartFromNumber++;
    }
  };

  const regenerateInvoice = () => {
    isLoading = true;
    toast.promise(createPostRequest(`invoice/regenerate`, { id }), {
      loading: "Regenerating invoice...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        updateTabList(tab, null);
        return "Invoice regenerated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to regenerate invoice", false);
      },
    });
  };

  const sendInvoice = () => {
    isLoading = true;
    const payload = setEmailContent();
    toast.promise(createPostRequest(`invoice/send-mail`, payload), {
      loading: "Sending invoice...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        updateTabList(tab, null);
        return "Invoice sent successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to send invoice", false);
      },
    });
  };

  const setEmailContent = () => {
    if (tab === InvoiceListTab.EXPIRED.LOWER_CASE) {
      return { id, status: InvoiceStatus.EXPIRED };
    }
    if (tab === InvoiceListTab.CANCELLED.LOWER_CASE) {
      return { id, status: InvoiceStatus.CANCELLED };
    }
    return { id, status: paymentStatus };
  };

  const toRecurring = async () => {
    isRecurringModalLoading = true;
    toast.loading("Updating invoice to recurring...");
    const payload = {
      token,
      dueDateInterval: recurringDueDateInterval,
      recurring: recurringPayload,
    };
    try {
      await createPostRequest(`invoice/to-recurring`, payload);
      isRecurringModalLoading = false;
      isCreateRecurringModalOpen = false;
      updateTabList(tab, null);
      toast.dismiss();
      toast.success("Invoice successfully updated into recurring");
    } catch (e) {
      isRecurringModalLoading = false;
      isCreateRecurringModalOpen = false;
      toast.dismiss();
      handleError(e, "Failed to update invoice to recurring", true, true);
    }
  };
</script>

<DropdownMenu>
  <div class="flex justify-center">
    <DropdownMenuTrigger class="p-0">
      <Button
        size="icon"
        variant="link"
        disabled="{isLoading}"
        class="hover:text-background w-6 h-6 hover:bg-primary active:bg-primary-300">
        {#if isLoading}
          <RefreshCcw class="h-4 animate-spin" />
        {:else}
          <Settings2 class="h-4" />
        {/if}
      </Button>
    </DropdownMenuTrigger>
  </div>
  <DropdownMenuContent class="w-48 p-2 shadow-xl bg-gradient-to-tr from-slate-200 to-white">
    <DropdownMenuGroup>
      <!-- Preview -->
      <DropdownMenuItem
        on:click="{() => (location.href = `#/invoice/${token}`)}"
        class="cursor-pointer gap-2 flex justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white">
        Preview
        <Eye class="text-inherit" size="{16}" />
      </DropdownMenuItem>

      <!-- Actions -->
      {#each invoiceActions as action}
        <DropdownMenuItem
          on:click="{() => actionHandler(action)}"
          class="cursor-pointer gap-2 flex justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white {action.status ===
          PaymentStatus.PAID
            ? 'text-green-600'
            : action.status === PaymentStatus.UNPAID
              ? 'text-destructive'
              : action.status === InvoiceStatus.TRASHED
                ? 'text-red-500'
                : 'text-black'}">
          {action.label}
          {#if action.status === PaymentStatus.PAID}
            <CircleDollarSign class="text-inherit" size="{16}" />
          {:else if action.status === PaymentStatus.UNPAID}
            <CircleDollarSign class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.SEND}
            <Send class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.DUPLICATE}
            <Files class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.CANCELLED}
            <Ban class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.ARCHIVED}
            <Package class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.TRASHED}
            <Trash class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.EDIT}
            <Pencil class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.REGENERATE}
            <RefreshCcw class="text-inherit" size="{16}" />
          {:else if action.status === InvoiceStatus.TO_RECURRING}
            <CalendarClock class="text-inherit" size="{16}" />
          {:else}
            <History class="text-inherit" size="{16}" />
          {/if}
        </DropdownMenuItem>
        {#if action.status === PaymentStatus.PAID || action.status === PaymentStatus.UNPAID}
          <DropdownMenuSeparator class="bg-background/80" />
        {/if}
      {/each}
    </DropdownMenuGroup>
  </DropdownMenuContent>
</DropdownMenu>

<!-- Confirmation dialog -->
<AlertDialog bind:open="{isDialogOpen}">
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        Are you sure you want to <span class="text-primary">{actionMessage}</span>
        invoice
        <span class="text-primary">{prefix} {clientName}?</span>
        {#if actionStatus === PaymentStatus.PAID}
          <div class="text-xs text-muted-foreground">This action will create the receipt.</div>
        {:else if paymentStatus === PaymentStatus.PAID && (actionStatus === InvoiceStatus.ARCHIVED || actionStatus === InvoiceStatus.TRASHED || InvoiceStatus.CANCELLED)}
          <div class="text-xs text-muted-foreground">This action will also affect the corresponding receipt.</div>
        {/if}
      </AlertDialogDescription>
      {#if actionStatus === PaymentStatus.PAID && !isWoocommerce}
        <div class="flex flex-row flex-nowrap items-center gap-x-2 py-2">
          <Checkbox id="send-email" bind:checked="{isSendEmail}" />
          <Label for="send-email">Also send email to customer</Label>
        </div>
      {:else if actionStatus === PaymentStatus.PAID && isWoocommerce}
        <div class="text-muted-foreground text-xs">
          This will also create Receipt, set the order status in Woocommerce to COMPLETE and send receipt to customer's
          email
        </div>
      {/if}
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel>Cancel</AlertDialogCancel>
      {#if actionStatus === PaymentStatus.PAID}
        <Button variant="default" disabled="{isLoading}" on:click="{updateStatus}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            {PaymentStatusAction.PAY}
          {/if}
        </Button>
      {:else if actionStatus === PaymentStatus.UNPAID}
        <Button variant="destructive" disabled="{isLoading}" on:click="{updateStatus}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            {PaymentStatusAction.UNPAY}
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.ARCHIVED}
        <Button variant="default" disabled="{isLoading}" on:click="{updateStatus}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Archive
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.CANCELLED}
        <Button variant="destructive" disabled="{isLoading}" on:click="{updateStatus}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Yes
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.TRASHED}
        <Button variant="destructive" disabled="{isLoading}" on:click="{updateStatus}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Move to Trash
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.DUPLICATE}
        <Button variant="default" disabled="{isLoading}" on:click="{duplicateInvoice}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Duplicate
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.SEND}
        <Button variant="default" disabled="{isLoading}" on:click="{sendInvoice}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Send
          {/if}
        </Button>
      {:else if actionStatus === InvoiceStatus.REGENERATE}
        <Button variant="default" disabled="{isLoading}" on:click="{regenerateInvoice}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Regenerate
          {/if}
        </Button>
      {:else if actionStatus === "active"}
        {#if actionMessage === "unarchive"}
          <Button variant="default" disabled="{isLoading}" on:click="{updateStatus}">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Unarchive
            {/if}
          </Button>
        {:else if actionMessage === "uncancel"}
          <Button variant="default" disabled="{isLoading}" on:click="{updateStatus}">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Uncancel
            {/if}
          </Button>
        {:else if actionMessage === "restore"}
          <Button variant="default" disabled="{isLoading}" on:click="{updateStatus}">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Restore
            {/if}
          </Button>
        {/if}
      {/if}
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>

<AlertDialog bind:open="{isCreateRecurringModalOpen}">
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle class="my-0">Recurring Details</AlertDialogTitle>
      <AlertDialogDescription>Input the recurring details</AlertDialogDescription>
    </AlertDialogHeader>
    <form on:submit|preventDefault="{toRecurring}">
      <CreateRecurringModal bind:payload="{recurringPayload}" bind:dueDateInterval="{recurringDueDateInterval}" />
      <AlertDialogFooter class="mt-5">
        <AlertDialogCancel disabled="{isRecurringModalLoading}">Cancel</AlertDialogCancel>
        <Button type="submit" disabled="{isRecurringModalLoading}">
          {#if isRecurringModalLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </AlertDialogFooter>
    </form>
  </AlertDialogContent>
</AlertDialog>
