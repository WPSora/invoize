<script>
  export let name;
  export let description;
  export let isWc;
</script>

<div class="flex flex-nowrap justify-between items-center w-[300px]">
  <div class="flex flex-col">
    <div>{name}</div>
    <div class="text-xs text-muted-foreground">{description}</div>
  </div>
  {#if isWc}
    <div class="w-6 h-4">
      <img alt="wc-icon" src="{invoize.plugin_url}public/wc-icon.png" width="32" height="19" />
    </div>
  {/if}
</div>
