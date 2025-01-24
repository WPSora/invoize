<script>
  import { Button } from "$lib/components/ui/button";

  export let id;
  export let name;
  export let token;
</script>

<Button
  variant="link"
  class="p-0 text-xs flex justify-start w-full"
  on:click={() => (location.href = `#/recurring/${id}/${token}`)}>
  <div class="flex text-wrap text-start text-sm">
    {name}
  </div>
</Button>
