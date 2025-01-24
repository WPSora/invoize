<script>
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";
  export let total;
  export let currency;
</script>

<div class="text-xs">{currencyFormatter(currency?.name, total)}</div>
