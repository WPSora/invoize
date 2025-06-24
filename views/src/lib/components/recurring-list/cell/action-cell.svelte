<script>
  import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuGroup,
  } from "$lib/components/ui/dropdown-menu";
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { Button } from "$lib/components/ui/button";
  import { RefreshCcw, Settings2, Eye, CheckCircle, Ban, Loader2, Plus, Pencil } from "lucide-svelte";
  import { getRecurringAction } from "$lib/helpers/actionHelper";
  import { createPostRequest } from "$lib/helpers/request";
  import { RecurringStatus, RecurringStatusAction } from "$lib/common/enum";
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { InvoiceListTab } from "$lib/common/enum";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";
  import toast from "svelte-french-toast";

  export let id;
  export let clientId;
  export let name;
  export let token;
  export let recurringStatus;
  export let updateTabList = (invoiceTab = false) => {};

  let isLoading = false;
  let isDialogOpen = false;
  // for dialog button message
  let actionMessage;
  // recurring status
  let actionStatus;
  let isSendEmail = false;

  $: recurringAction = getRecurringAction(recurringStatus);

  const actionHandler = ({ label, status }) => {
    if (status == RecurringStatus.EDIT) {
      window.location.href = `#/recurring/edit/${token}`;
      return;
    }
    actionMessage = label.toLowerCase();
    actionStatus = status;
    isDialogOpen = true;
  };

  const updateStatus = () => {
    isLoading = true;
    const payload = { id, recurringStatus: actionStatus };

    toast.promise(createPostRequest("recurring/update", payload), {
      loading: "Updating...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        updateTabList();
        return "Recurring status updated successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to update status");
      },
    });
  };

  const createInvoice = () => {
    isLoading = true;
    toast.promise(createPostRequest("recurring/create-invoice", { id, email: isSendEmail }), {
      loading: "Creating invoice...",
      success: () => {
        isSendEmail = false;
        isLoading = false;
        isDialogOpen = false;
        triggerInvoiceTabUpdate(InvoiceListTab.UNPAID.LOWER_CASE, null);
        // this has param true to update invoice tab list after creating invoice
        updateTabList(true);
        return "Invoice created successfully";
      },
      error: (err) => {
        isSendEmail = false;
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to create invoice");
      },
    });
  };
</script>

<DropdownMenu>
  <div class="flex justify-center">
    <DropdownMenuTrigger class="p-0">
      <Button
        size="icon"
        variant="link"
        disabled={isLoading}
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
      <DropdownMenuItem
        on:click={() => (location.href = `#/recurring/${clientId}/${token}`)}
        class="cursor-pointer gap-2 flex justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white">
        Preview
        <Eye class="text-inherit" size={16} />
      </DropdownMenuItem>

      {#each recurringAction as action}
        <DropdownMenuItem
          on:click={() => actionHandler(action)}
          class="cursor-pointer gap-2 flex justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white {action.status ===
            RecurringStatus.ACTIVE || action.status === RecurringStatus.CREATE
            ? 'text-green-600'
            : action.status === RecurringStatus.INACTIVE
              ? 'text-destructive'
              : 'text-black'}">
          {action.label}
          {#if action.status === RecurringStatus.ACTIVE}
            <CheckCircle class="text-green-600" size={16} />
          {:else if action.status === RecurringStatus.INACTIVE}
            <Ban class="text-destructive" size={16} />
          {:else if action.status === RecurringStatus.CREATE}
            <Plus class="text-green-700" size={16} />
          {:else if action.status === RecurringStatus.EDIT}
            <Pencil class="text-black" size={16} />
          {/if}
        </DropdownMenuItem>
      {/each}
    </DropdownMenuGroup>
  </DropdownMenuContent>
</DropdownMenu>

<!-- Confirmation Dialog -->
<AlertDialog bind:open={isDialogOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        Are you sure you want to <span class="text-primary">{actionMessage}</span> recurring invoice
        <span class="text-primary">{name}?</span>
        {#if actionStatus === RecurringStatus.CREATE}
          <div class="flex flex-row flex-nowrap items-center gap-x-2 py-2">
            <Checkbox id="send-email" bind:checked={isSendEmail} />
            <Label for="send-email">Send invoice to customer's email</Label>
          </div>
        {/if}
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel>Cancel</AlertDialogCancel>
      {#if actionStatus === RecurringStatus.ACTIVE}
        <Button variant="default" disabled={isLoading} on:click={updateStatus}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            {RecurringStatusAction.ACTIVATE}
          {/if}
        </Button>
      {:else if actionStatus === RecurringStatus.INACTIVE}
        <Button variant="destructive" disabled={isLoading} on:click={updateStatus}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            {RecurringStatusAction.INACTIVATE}
          {/if}
        </Button>
      {:else if actionStatus === RecurringStatus.CREATE}
        <Button variant="default" disabled={isLoading} on:click={createInvoice}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            {RecurringStatusAction.CREATE}
          {/if}
        </Button>
      {/if}
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
