<script>
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  export let name;
  export let total;
</script>

<div class="text-center text-xs">
  {currencyFormatter(name, total)}
</div>
