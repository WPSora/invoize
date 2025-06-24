<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  export let business;
</script>

<div>
  <div class="font-bold">Issued by</div>
  <div class="w-5/6 text-muted-foreground mt-1">
    <div class="text-sm font-medium text-black">{business.business_name}</div>
    <div class="text-xs">
      <div>{business.email ?? ""}</div>
      <div>{business.phone_number ?? ""}</div>
      <div>{business.web ?? ""}</div>
      <MultilineText text={business.address} />
    </div>
  </div>
</div>
