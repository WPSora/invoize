<script>
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";
  export let total;
  export let currency;

</script>

{#if currency.length > 1}
  <div class="text-center text-xs">Multi Currency</div>
{:else}
  <div class="text-center text-xs">{currencyFormatter(currency[0], total) ?? 0}</div>
{/if}
