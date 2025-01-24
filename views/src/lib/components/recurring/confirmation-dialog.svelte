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
  import { Button } from "$lib/components/ui/button";
  import { RecurringStatus } from "$lib/common/enum";
  import { createEventDispatcher } from "svelte";
  import { Loader2 } from "lucide-svelte";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";

  const dispatch = createEventDispatcher();

  export let isDialogOpen = false;
  export let actionMessageLowerCase;
  export let recurring;
  export let actionStatus;
  export let actionMessage;
  export let isLoading;
  export let isSendEmail;
</script>

<AlertDialog bind:open={isDialogOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription class="space-y-2">
        <div>
          Are you sure you want to <span class="text-primary">{actionMessageLowerCase}</span>
          invoice <span class="text-primary">{recurring?.client?.name}?</span>
        </div>
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
      <Button
        variant={actionStatus === RecurringStatus.ACTIVE || actionStatus === RecurringStatus.CREATE
          ? "default"
          : "destructive"}
        disabled={isLoading}
        on:click={() => dispatch("updateStatus")}>
        {#if isLoading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Saving
        {:else}
          {actionMessage}
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
