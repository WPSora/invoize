<script>
  import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
    DialogDescription,
  } from "$lib/components/ui/dialog";
  import { Button } from "$lib/components/ui/button";
  import { Textarea } from "$lib/components/ui/textarea";
  import { createEventDispatcher } from "svelte";
  import { selectedClient } from "$lib/stores/invoice-store";
  import Label from "$lib/components/ui/label/label.svelte";

  const dispatch = createEventDispatcher();

  export let isOpen = false;

  export let address;

  const resetForm = () => {
    address = undefined;
    isOpen = false;
  };
</script>

<Dialog open={isOpen} onOpenChange={() => (isOpen = false)}>
  <DialogTrigger id="address-modal"></DialogTrigger>
  <DialogContent class="sm:max-w-[425px] ">
    <DialogHeader>
      <DialogTitle>Add Custom Address To {$selectedClient?.name ?? "User"}</DialogTitle>
      <DialogDescription>Let's customize your customer address</DialogDescription>
    </DialogHeader>
    <div class="grid gap-4 py-4">
      <div class="grid grid-cols items-center gap-4">
        <Label for="new-customer-name">Custom address</Label>
        <Textarea id="new-customer-name" class="h-52" bind:value={address} />
      </div>
    </div>
    <DialogFooter>
      <Button
        type="submit"
        on:click={() => {
          if ($selectedClient.isWcClient) {
            dispatch("customAddress", { address, isWcClient: true });
          } else {
            dispatch("customAddress", { address, isWcClient: false });
          }
          resetForm();
        }}>
        Add
      </Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
