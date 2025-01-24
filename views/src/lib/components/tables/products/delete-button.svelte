<script>
  import * as AlertDialog from "$lib/components/ui/alert-dialog";
  import * as Tooltip from "$lib/components/ui/tooltip";
  import { Button } from "$lib/components/ui/button";
  import { createPostRequest } from "$lib/helpers/request";
  import { createEventDispatcher } from "svelte";
  import { Loader2 } from "lucide-svelte";
  import { Trash } from "radix-icons-svelte";
  import { toast } from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";
  // import * as DropdownMenu from "$lib/components/ui/dropdown-menu";
  // import { toast } from "svelte-french-toast";
  // import { ChevronDown } from "radix-icons-svelte";

  export let id;

  const dispatch = createEventDispatcher();
  let deleteConfirmationAlertOpened = false;
  let loading = false;

  const deleteButtonHandler = () => {
    deleteConfirmationAlertOpened = true;
  };

  const deleteProduct = async () => {
    if (loading) {
      return;
    }
    loading = true;
    toast.promise(createPostRequest(`product/delete?id=${id}`), {
      loading: "Deleting product...",
      success: () => {
        dispatch("update");
        loading = false;
        return "Product deleted successfully!";
      },
      error: (err) => {
        loading = false;
        return handleError(err, "Failed to delete product");
      },
    });
  };
</script>

<!-- Delete button -->
<Tooltip.Root>
  <Tooltip.Trigger>
    <Button
      variant="outline"
      size="icon"
      class="text-destructive hover:bg-red-500 hover:border-red-500 hover:text-white hover:shadow-red-400"
      on:click={deleteButtonHandler}>
      <Trash class="h-4 w-4" />
    </Button>
  </Tooltip.Trigger>
  <Tooltip.Content class="bg-white shadow-lg">
    <p class="text-red-600">Delete</p>
  </Tooltip.Content>
</Tooltip.Root>

<!-- Delete Confirmation Modal -->
<AlertDialog.Root bind:open={deleteConfirmationAlertOpened}>
  <AlertDialog.Content>
    <AlertDialog.Header>
      <AlertDialog.Title>Are you sure?</AlertDialog.Title>
      <AlertDialog.Description>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialog.Description>
    </AlertDialog.Header>
    <AlertDialog.Footer>
      <AlertDialog.Cancel>Cancel</AlertDialog.Cancel>
      <Button variant="destructive" on:click={deleteProduct} disabled={loading}>
        {#if loading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialog.Footer>
  </AlertDialog.Content>
</AlertDialog.Root>
