<script>
  import { Search, RefreshCcw } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { Input } from "$lib/components/ui/input";

  export let searchQuery;
  export let isSearching;
  export let placeholder;
  export let handleSearch;
  let className = "";
  export { className as class };
</script>

<form on:submit|preventDefault={handleSearch} class="flex flex-row justify-end sm:w-80 w-full gap-x-1 {className}">
  <Input {placeholder} type="text" bind:value={searchQuery} />
  <Button
    type="submit"
    size="icon"
    variant="outline"
    disabled={isSearching}
    class="sm:h-9 h-10 sm:w-9 w-10 aspect-square text-primary shadow-none hover:text-background hover:bg-primary active:text-primary active:bg-background duration-200">
    {#if isSearching}
      <RefreshCcw class="h-5 animate-spin text-primary" />
    {:else}
      <Search class="h-5" />
    {/if}
  </Button>
</form>
