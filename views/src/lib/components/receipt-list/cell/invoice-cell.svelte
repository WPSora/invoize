<script>
  import { Button } from "$lib/components/ui/button";

  export let invoiceId;
  export let token;
</script>

<div class="flex justify-center">
  <Button variant="link" class="p-0 text-xs flex justify-start" on:click={() => (location.href = `#/invoice/${token}`)}>
    <div class="flex text-wrap text-start">
      {invoiceId}
    </div>
  </Button>
</div>
