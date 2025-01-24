<script>
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  export let price;
  export let currencyName;
</script>

<div class="text-nowrap">
  {currencyFormatter(currencyName, price)}
</div>
