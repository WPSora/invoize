<script>
  import { Button } from "$lib/components/ui/button";
  export let lastInvoice;
</script>

<div class="flex justify-center">
  {#if lastInvoice?.name}
    <Button variant="link" class="text-xs" on:click="{() => (location.href = `#/invoice/${lastInvoice?.token}`)}">
      {lastInvoice?.name}
    </Button>
  {:else}
    <div>-</div>
  {/if}
</div>
