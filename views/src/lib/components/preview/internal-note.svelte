<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  export let internalNote;
</script>

{#if internalNote}
  <div class="text-xs flexf flex-col bg-muted rounded-md px-4 py-2 border">
    <div class="font-semibold text-black text-nowrap mb-1 text-sm">Internal Note</div>
    <MultilineText text={internalNote} class="text-muted-foreground" />
  </div>
{/if}
