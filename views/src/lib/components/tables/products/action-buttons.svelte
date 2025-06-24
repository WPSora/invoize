<script>
  import Button from "$lib/components/ui/button/button.svelte";
  import DeleteButton from "./delete-button.svelte";
  import EditButton from "./edit-button.svelte";

  export let id;
  export let isWc;
</script>

{#if isWc}
  <Button variant="outline" class="w-24" on:click={() => (location.href = `post.php?post=${id}&action=edit`)}>
    Detail
  </Button>
{:else}
  <div class="flex flex-nowrap items-center gap-1 justify-center">
    <EditButton {id} on:open-modal />
    <DeleteButton {id} on:update />
  </div>
{/if}
