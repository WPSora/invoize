<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import { Repeat } from "lucide-svelte";

  export let type;
</script>

<div class="flex justify-center text-xs">
  {#if type}
    <div class="flex justify-center items-center w-24 py-0.5 rounded-md bg-cyan-100 text-muted-foreground">
      <Repeat class="h-3  mr-1" />
      {capitalizeFirstLetter(type.interval)}
    </div>
  {:else}
    <div class="flex justify-center w-24 py-0.5 rounded-md bg-[#FFFAA0] text-muted-foreground">One-time</div>
  {/if}
</div>
