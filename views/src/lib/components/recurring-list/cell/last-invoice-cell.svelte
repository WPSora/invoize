<script>
  import { Button } from "$lib/components/ui/button";

  export let lastInvoice;
</script>

{#if lastInvoice}
  <Button
    class="p-0 text-xs flex justify-start"
    variant="link"
    on:click={() => (location.href = `#/invoice/${lastInvoice.token}`)}>
    <div class="flex text-wrap text-start">
      {lastInvoice.name}
    </div>
  </Button>
{:else}
  -
{/if}
