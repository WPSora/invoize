<script>
  import moment from "moment";
  import { Button } from "$lib/components/ui/button";

  export let lastInvoice;
</script>

{#if lastInvoice}
  <div class="flex items-center text-sm">
    <div>Last Invoice:</div>
    <Button variant="link" on:click={() => (location.href = `#/invoice/${lastInvoice.token}`)}>
      {lastInvoice.name}
    </Button>
  </div>
{/if}
