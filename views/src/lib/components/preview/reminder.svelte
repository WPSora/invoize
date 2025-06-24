<script>
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import Separator from "$lib/components/ui/separator/separator.svelte";

  export let reminder;

  $: hasReminder = reminder?.before?.length || reminder?.after?.length;
  $: hasClientReminder = reminder?.forClient && hasReminder;
  $: hasAdminReminder = reminder?.forAdmin && hasReminder;
</script>

<Separator />
<div>
  <div class="text-base font-medium">Reminder</div>
  <div class="text-xs my-1 gap-2 grid grid-cols-2">
    <div class="flex flex-col items-center gap-1 bg-slate-100 rounded-md py-1 px-2">
      <div>Customer</div>
      <div class="px-4 py-0.5 rounded-sm font-semibold {hasClientReminder ? 'text-green-500' : 'text-slate-500'}">
        {hasClientReminder ? "Active" : "Disabled"}
      </div>
    </div>
    <div class="flex flex-col items-center gap-1 bg-slate-100 rounded-md py-1 px-2">
      <div>WP Admin</div>
      <div class="px-4 py-0.5 rounded-sm font-semibold {hasAdminReminder ? 'text-green-500' : 'text-slate-500'}">
        {hasAdminReminder ? "Active" : "Disabled"}
      </div>
    </div>
  </div>

  {#if !reminder || reminder?.length === 0 || (reminder?.before?.length === 0 && reminder?.after?.length === 0)}
    <div></div>
  {:else}
    <div class="flex flex-col justify-between text-xs px-2 gap-1 rounded-md">
      {#if reminder?.before?.length > 0}
        <div class="mt-2">
          <div>Before:</div>
          <div class="grid grid-cols-3 gap-1 mt-1 font-medium">
            {#each reminder.before as item}
              <span class="border border-gray-200 rounded-md px-2 py-1 text-center"
                >{pluralHelper(item.value + " day")}</span>
            {/each}
          </div>
        </div>
      {/if}
      {#if reminder?.after?.length > 0}
        <div class="mt-2">
          <div>After:</div>
          <div class="grid grid-cols-3 gap-1 mt-1 font-medium">
            {#each reminder.after as item}
              <span class="border border-gray-200 rounded-md px-2 py-1 text-center"
                >{pluralHelper(item.value + " day")}</span>
            {/each}
          </div>
        </div>
      {/if}
    </div>
  {/if}
</div>
