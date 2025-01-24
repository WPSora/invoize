<script>
  import { Button } from "$lib/components/ui/button";
  import { Plus, Ban, CheckCircle, Pencil } from "lucide-svelte";
  import { RecurringStatus } from "$lib/common/enum";

  export let recurringActions;
  export let actionHandler;
</script>

<div class="grid grid-cols-2 gap-x-1 gap-y-2">
  {#each recurringActions as action}
    <Button
      variant="outline"
      on:click={() => actionHandler(action)}
      class="w-full flex flex-row h-10 p-4 min-w-fit flex-nowrap items-center text-xs gap-2 px-3">
      {#if action.status === RecurringStatus.ACTIVE}
        <CheckCircle class="h-4 w-4 text-green-600" />
      {:else if action.status === RecurringStatus.INACTIVE}
        <Ban class="h-4 w-4 text-destructive" />
      {:else if action.status === RecurringStatus.CREATE}
        <Plus class="h-4 w-4 text-green-600" />
      {:else if action.status === RecurringStatus.EDIT}
        <Pencil class="h-4 w-4 text-blue-400" />
      {/if}
      <div class="w-full text-start text-nowrap">
        {action.label}
      </div>
    </Button>
  {/each}
</div>
