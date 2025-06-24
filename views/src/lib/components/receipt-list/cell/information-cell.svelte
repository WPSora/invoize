<script>
  import { Button } from "$lib/components/ui/button";

  export let token;
  export let name;
</script>

<Button
  variant="link"
  class="p-0 text-xs flex justify-start w-full"
  on:click={() => (location.href = `#/receipt/${token}`)}>
  <div class="flex text-wrap text-start text-sm">
    {name}
  </div>
</Button>
