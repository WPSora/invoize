<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let bank;
</script>

<div class="flex flex-nowrap gap-3">
  <div>
    <div>Name</div>
    <div>Detail</div>
  </div>
  <div>
    <div>:</div>
    <div>:</div>
  </div>
  <div>
    <div>{capitalizeFirstLetter(bank?.name)}</div>
    <MultilineText text={bank?.detail?.toString()} />
  </div>
</div>
