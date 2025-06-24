<script>
  import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { Button } from "$lib/components/ui/button";
  import { createEventDispatcher } from "svelte";
  import { Loader2 } from "lucide-svelte";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";

  let dispatch = createEventDispatcher();

  export let open;
  export let isLoading;
  let isSendEmail = false;
</script>

<Dialog bind:open>
  <DialogContent>
    <DialogHeader>
      <DialogTitle>Confirm to create Recurring and Invoice now?</DialogTitle>
      <DialogDescription>
        The invoice date and order date will be set as today, with the recurring invoice end time starting after this
        point.
      </DialogDescription>
      <div class="flex flex-row flex-nowrap items-center gap-x-2 py-2">
        <Checkbox id="send-email" bind:checked={isSendEmail} />
        <Label for="send-email">Send invoice to customer's email</Label>
      </div>
    </DialogHeader>
    <DialogFooter>
      <Button variant="outline" on:click={() => (open = false)} disabled={isLoading}>No</Button>
      <Button on:click={() => dispatch("createInvoice", { isSendEmail })} disabled={isLoading}>
        {#if isLoading}
          <Loader2 class="h-4 w-4 animate-spin mr-1 text-background" />
          Saving
        {:else}
          Yes
        {/if}
      </Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
