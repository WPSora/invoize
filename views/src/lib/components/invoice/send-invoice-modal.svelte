<script>
  import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { Button } from "$lib/components/ui/button";
  import { createEventDispatcher } from "svelte";
  import { Loader2 } from "lucide-svelte";

  let dispatch = createEventDispatcher();

  export let open;
  export let isLoading;
</script>

<Dialog bind:open>
  <DialogContent>
    <DialogHeader>
      <DialogTitle>Confirm to create invoice and send it now?</DialogTitle>
      <DialogDescription>This will create the invoice and send it to customer's email.</DialogDescription>
    </DialogHeader>
    <DialogFooter>
      <Button variant="outline" on:click={() => (open = false)} disabled={isLoading}>No</Button>
      <Button on:click={() => dispatch("createAndSend")} disabled={isLoading}>
        {#if isLoading}
          <Loader2 class="h-4 w-4 mr-1 animate-spin text-background" />
          Saving
        {:else}
          Yes
        {/if}
      </Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
