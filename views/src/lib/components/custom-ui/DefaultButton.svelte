<script>
  import { Button } from "$lib/components/ui/button";
  import { Check, Loader2 } from "lucide-svelte";
  import { fly } from "svelte/transition";
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();

  /** @type {{id: number}}*/
  export let data;

  /** @type {number} */
  export let storeDataId;

  /** @type {boolean}*/
  export let isLoading = false;

  /**@type {Array<boolean>} */
  export let buttonState;

  /** @type {number} */
  export let index;
</script>

{#if data.id === storeDataId}
  <!-- Default button -->
  <div in:fly={{ x: 50, duration: 400 }} out:fly={{ x: 50, duration: 400 }}>
    <Button variant="outline" class="text-green-600 text-xs" disabled>
      <Check class="h-4 w-4 mr-1" />
      Default
    </Button>
  </div>
{:else}
  <!-- Set default button -->
  <div in:fly={{ x: 50, duration: 400 }} out:fly={{ x: 50, duration: 400 }}>
    <Button
      variant="outline"
      class="text-black text-xs"
      disabled={isLoading}
      on:click={() => dispatch("updateDefault", { id: data.id, index: index - 1 })}>
      {#if buttonState[index - 1]}
        <Loader2 class="mr-2 w-4 h-4 animate-spin" />
        Setting as default
      {:else}
        Set as default
      {/if}
    </Button>
  </div>
{/if}
