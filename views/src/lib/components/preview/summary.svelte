<script>
  import { Separator } from "$lib/components/ui/separator";
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  export let currency = { name: "USD", symbol: "$" };
  export let discounts = { data: [], total: 0 };
  export let taxes = { data: [], total: 0 };
  export let subtotal;
  export let total;

  // calculate based on percentage
  const calculateDiscountPrice = (percentPrice) => {
    return (percentPrice / 100) * subtotal;
  };

  const calculateTaxPrice = (percentPrice) => {
    return (percentPrice / 100) * (subtotal - discounts.total);
  };
</script>

<div class="w-1/2 text-sm flex flex-col items-end">
  <div class="w-9/12 text-base space-y-2">
    <!-- Subtotal -->
    <div class="flex flex-nowrap justify-between text-sm">
      <div class="font-semibold">Subtotal</div>
      <div>{currencyFormatter(currency.name, subtotal) ?? 0}</div>
    </div>

    {#if discounts?.data?.length > 0 || taxes?.data?.length > 0}
      <Separator />
    {/if}

    <!-- Discount -->
    <div>
      {#each discounts?.data ?? [] as discount}
        {@const discountValue = discount.type === "percent" ? calculateDiscountPrice(discount.value) : discount.value}
        <div class="flex flex-nowrap justify-between text-sm text-red-600">
          <div class="font-medium">{discount.name} {discount.type === "percent" ? `${discount.value}%` : ""}</div>
          <div>- {currencyFormatter(currency.name, discountValue)}</div>
        </div>
      {/each}
    </div>

    <!-- Tax -->
    <div>
      {#each taxes?.data ?? [] as tax}
        {@const taxValue = tax.type === "percent" ? calculateTaxPrice(tax.value) : tax.value}
        <div class="flex flex-nowrap justify-between text-sm">
          <div class="font-medium">{tax.name} {tax.type === "percent" ? `${tax.value}%` : ""}</div>
          <div>{currencyFormatter(currency.name, taxValue)}</div>
        </div>
      {/each}
    </div>

    <Separator />

    <!-- Total -->
    <div class="flex flex-nowrap justify-between text-base">
      <div class="font-semibold">Total</div>
      <div class="font-semibold">{currencyFormatter(currency.name, total) ?? 0}</div>
    </div>
  </div>
</div>
