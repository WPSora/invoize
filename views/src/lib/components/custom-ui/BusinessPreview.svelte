<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  export let data;
</script>

<div class="text-gray-500 text-xs">
  <div class="text-base font-semibold">{data.business_name}</div>
  <div>{data.web ?? ""}</div>
  <div>{data.email ?? ""}</div>
  <div>{data.phone_number ?? ""}</div>
  <MultilineText text={data.address} />
</div>
