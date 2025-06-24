<script>
  import { createEventDispatcher } from "svelte";
  import { client, clients } from "$lib/stores/invoice-store";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";

  const dispatch = createEventDispatcher();

  export let id;

  const handleEdit = () => {
    $client = $clients.find((client) => client.id === id);
    dispatch("edit");
  };

  const handleDelete = () => {
    dispatch("delete", { selectedId: id, isDeleteModalOpen: true });
  };
</script>

<div class="flex flex-nowrap items-center gap-x-2 justify-center">
  <EditButton on:click={() => handleEdit()} />
  <DeleteButton on:click={() => handleDelete()} />
</div>
