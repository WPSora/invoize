<script>
  import { Button } from "$lib/components/ui/button";

  export let currencyName;
  export let suffix = null;
  export let isActive = false;

</script>

<Button
  on:click
  variant="outline"
  class="rounded-full text-xs py-1 px-3 h-fit hover:bg-primary hover:text-background hover:border-primary active:bg-background active:text-primary duration-100 {isActive &&
    'bg-primary border-primary text-background'}">
  {currencyName}
  {#if suffix}
    <span>&nbsp;</span>
    <span class="text-xs">{suffix}</span>
  {/if}
</Button>
