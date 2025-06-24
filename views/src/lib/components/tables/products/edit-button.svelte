<script>
  import * as Tooltip from "$lib/components/ui/tooltip";
  import { createEventDispatcher } from "svelte";
  import { Button } from "$lib/components/ui/button";
  import { Pencil } from "lucide-svelte";

  export let id;

  const dispatch = createEventDispatcher();

  const editButtonHandler = async () => {
    dispatch("open-modal", { id });
  };
</script>

<Tooltip.Root>
  <Tooltip.Trigger>
    <Button
      variant="outline"
      size="icon"
      class="hover:bg-primary hover:text-white text-primary hover:shadow-primary-600 hover:shadow-md transition-all hover:border-primary"
      on:click={editButtonHandler}>
      <Pencil class="h-4 w-4" />
    </Button>
  </Tooltip.Trigger>
  <Tooltip.Content class="bg-white shadow-lg border">
    <p class="text-black">Edit</p>
  </Tooltip.Content>
</Tooltip.Root>
