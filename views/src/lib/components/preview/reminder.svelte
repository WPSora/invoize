<script>
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import Separator from "$lib/components/ui/separator/separator.svelte";

  export let reminder;
</script>

{#if reminder.length === 0 || (reminder?.before?.length === 0 && reminder?.after?.length === 0)}
  <div></div>
{:else}
  <Separator />
  <div>
    <div class="text-base font-medium">Reminder</div>
    <div class="flex justify-between text-xs px-2 rounded-md">
      {#if reminder?.before?.length > 0}
        <div class="mt-2">
          <div>Before:</div>
          <div class="grid grid-cols-2 gap-1 mt-1 font-medium">
            {#each reminder.before as item}
              <span class="border border-gray-200 rounded-md px-2 py-1">{pluralHelper(item.value + " day")}</span>
            {/each}
          </div>
        </div>
      {/if}
      {#if reminder?.after?.length > 0}
        <div class="mt-2">
          <div>After:</div>
          <div class="grid grid-cols-2 gap-1 mt-1 font-medium">
            {#each reminder.after as item}
              <span class="border border-gray-200 rounded-md px-2 py-1">{pluralHelper(item.value + " day")}</span>
            {/each}
          </div>
        </div>
      {/if}
    </div>
  </div>
{/if}
