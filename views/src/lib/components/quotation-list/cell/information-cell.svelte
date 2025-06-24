<script>
  import { Button } from "$lib/components/ui/button";

  export let token;
  export let quotationNumber;
  export let customer;
  export let isSent;
</script>

<div class="flex justify-between items-center">
  <Button variant="link" class="p-0 text-xs flex justify-start" on:click={() => (location.href = `#/quotation/${token}`)}>
    <div class="flex text-wrap text-start text-sm">
      {quotationNumber}
      {customer}
    </div>
  </Button>
</div>
<!-- <div>
  <small class="text-gray-500">someone@gmail.com</small>
</div> -->
