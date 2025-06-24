<script>
  import { Button } from "$lib/components/ui/button";
  export let id;
  export let name;
</script>

<Button variant="link" class="pl-0 text-sm" on:click={() => (location.href = `#/recurring/${id}`)}>
  <div class="text-wrap text-start">
    {name}
  </div>
</Button>
