<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  export let client;
  export let hasBilledTo = false;
</script>
<div class="{hasBilledTo ? 'border border-gray-300 p-3 rounded-md has-billed-to' : ''}">

  <div class="font-bold">Customer</div>
  <div class="w-5/6 text-sm text-muted-foreground mt-1">
    <div class="text-sm font-medium text-black">{client.name}</div>
    <div class="text-xs">
      <div>{client.email ?? ""}</div>
      <MultilineText text={client.customAddress ?? client.address} isShowEmpty={true} />
    </div>
  </div>
  {#if hasBilledTo}
    <div class="leading-3 mt-1">
      <small class="text-xs text-gray-400">Since there is a “Bill To,” it will not appear on the invoice.</small>
    </div>
  {/if}
</div>
