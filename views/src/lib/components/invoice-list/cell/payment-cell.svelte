<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let payments;

  $: paymentTypeList = payments?.map((payment) => payment.method);
</script>

{#if paymentTypeList?.length === 1}
  <div class="text-center text-xs">{capitalizeFirstLetter(paymentTypeList[0] ?? "")}</div>
{:else if paymentTypeList?.length > 1}
  <div class="text-center text-xs">Multiple</div>
{:else}
  <div class="text-center text-xs">-</div>
{/if}
