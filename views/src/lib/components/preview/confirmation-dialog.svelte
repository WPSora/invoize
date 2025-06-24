<script>
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { PaymentStatus, InvoiceStatus as InvoiceStatusEnum } from "$lib/common/enum";
  import { Button } from "$lib/components/ui/button";
  import { Loader2 } from "lucide-svelte";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";
  import { createEventDispatcher } from "svelte";
  import CreateRecurringModal from "$lib/components/recurring/CreateRecurringModal.svelte";
  import { dueDateInterval } from "$lib/stores/settings-store";

  const dispatch = createEventDispatcher();

  export let isDialogOpen = false;
  export let isLoading = false;
  export let invoiceNumber;
  export let client;
  export let paymentStatus;
  export let actionStatus;
  export let actionMessage;
  export let actionMessageLowerCase;
  export let isWoocommerce;
  let isSendEmail = false;

  let recurringPayload;
  let recurringDueDateInterval;
</script>

<AlertDialog bind:open="{isDialogOpen}" onOpenChange="{() => (isSendEmail = false)}">
  <AlertDialogContent>
    {#if actionStatus === InvoiceStatusEnum.TO_RECURRING}
      <AlertDialogHeader>
        <AlertDialogTitle class="my-0">Recurring Details</AlertDialogTitle>
        <AlertDialogDescription>Input the recurring details</AlertDialogDescription>
      </AlertDialogHeader>
      <form
        on:submit|preventDefault="{() =>
          dispatch('updateStatus', { recurring: recurringPayload, dueDateInterval: recurringDueDateInterval })}">
        <CreateRecurringModal bind:payload="{recurringPayload}" bind:dueDateInterval="{recurringDueDateInterval}" />
        <AlertDialogFooter class="mt-5">
          <AlertDialogCancel disabled="{isLoading}">Cancel</AlertDialogCancel>
          <Button type="submit" disabled="{isLoading}">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Save
            {/if}
          </Button>
        </AlertDialogFooter>
      </form>
    {:else}
      <AlertDialogHeader>
        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
        <AlertDialogDescription>
          <div>
            Are you sure you want to <span class="text-primary">{actionMessageLowerCase}</span>
            invoice <span class="text-primary">{invoiceNumber} {client?.name}?</span>
          </div>
          <div class="text-xs">
            {#if actionStatus === PaymentStatus.PAID && !isWoocommerce}
              This action will create the receipt.
            {:else if actionStatus === PaymentStatus.PAID && isWoocommerce}
              This will also create Receipt, set the order status in Woocommerce to COMPLETE and send receipt to
              customer's email
            {:else if actionStatus === PaymentStatus.UNPAID}
              This action will delete the receipt.
            {:else if paymentStatus === PaymentStatus.PAID && (actionStatus === InvoiceStatusEnum.ARCHIVED || actionStatus === InvoiceStatusEnum.CANCELLED || actionStatus === InvoiceStatusEnum.TRASHED || actionStatus === InvoiceStatusEnum.ACTIVE)}
              This action will also affect the corresponding receipt.
            {:else if actionStatus === InvoiceStatusEnum.REGENERATE}
              It will create new payment link if the previous one is unavailable or expired. The rest of invoice will be
              the same.
            {/if}
          </div>
        </AlertDialogDescription>
        {#if actionStatus === PaymentStatus.PAID && !isWoocommerce}
          <div class="flex flex-row flex-nowrap items-center gap-x-2 py-2">
            <Checkbox id="send-email" bind:checked="{isSendEmail}" />
            <Label for="send-email">Also send email to customer</Label>
          </div>
        {/if}
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Cancel</AlertDialogCancel>
        <Button
          on:click="{() => dispatch('updateStatus', { isSendEmail })}"
          disabled="{isLoading}"
          variant="{actionStatus === PaymentStatus.UNPAID ||
          actionStatus === InvoiceStatusEnum.CANCELLED ||
          actionStatus === InvoiceStatusEnum.TRASHED
            ? 'destructive'
            : 'default'}">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else if actionStatus === InvoiceStatusEnum.CANCELLED}
            Yes
          {:else}
            {actionMessage}
          {/if}
        </Button>
      </AlertDialogFooter>
    {/if}
  </AlertDialogContent>
</AlertDialog>
