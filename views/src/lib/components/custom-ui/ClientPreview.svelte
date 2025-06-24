<script>
  import { selectedClient } from "$lib/stores/invoice-store";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
</script>

<div class="text-gray-500 text-xs">
  <div class="text-base font-semibold">{$selectedClient.name}</div>
  <div>{$selectedClient.email ?? ""}</div>
  {#if $selectedClient.customAddress}
    <MultilineText text={$selectedClient.customAddress} />
  {:else if $selectedClient.address}
    <MultilineText text={$selectedClient.address} />
  {/if}
</div>
