<script>
    import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
    export let billedTo;
  </script>
  
  <div>
    <div class="font-bold">Bill to</div>
    <div class="w-5/6 text-sm text-muted-foreground mt-1">
      <div class="text-sm font-medium text-black">{billedTo.name}</div>
      <div class="text-xs">
        <MultilineText text={billedTo.detail} isShowEmpty={true} />
      </div>
    </div>
  </div>
  