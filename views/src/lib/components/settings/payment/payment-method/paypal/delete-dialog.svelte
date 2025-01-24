<script>
  import {
    AlertDialog,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { Button } from "$lib/components/ui/button";
  import { Loader2 } from "lucide-svelte";

  export let isLoading;
  export let isDeleteModalOpen;
  export let handleDeleteModalOnOpenChange;
  export let handleDelete;
  export let name;
</script>

<AlertDialog open={isDeleteModalOpen} onOpenChange={handleDeleteModalOnOpenChange}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete the{" "}
        <b>{name}</b> in settings.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel>Cancel</AlertDialogCancel>
      <Button variant="destructive" disabled={isLoading} on:click={handleDelete}>
        {#if isLoading}
          <Loader2 class="h-4 w-4 text-background mr-1 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
