<script>
  import CurrencyInput from "@canutin/svelte-currency-input";

  export let currencyName;
  export let value;
  export let placeholder = "price";
  export let isNegativeAllowed = false;
  export let onValueChange = (e) => {};
  export let inputClass = "";
  export let wrapperClass = "";
</script>

{#if currencyName === "IDR"}
  <CurrencyInput
    autocomplete="off"
    locale="id"
    inputClasses={{
      formatted: `text-sm text-center w-full h-9 !border-border placeholder-muted-foreground ${inputClass}`,
      wrapper: `w-full ${wrapperClass}`,
    }}
    currency="IDR"
    {onValueChange}
    {placeholder}
    {isNegativeAllowed}
    bind:value />
{:else}
  <!-- key here used so the component get rebuilt if currency is updated -->
  {#key currencyName}
    <CurrencyInput
      autocomplete="off"
      locale="en-US"
      inputClasses={{
        formatted: `text-sm text-center w-full h-9 !border-border placeholder-muted-foreground ${inputClass}`,
        wrapper: `w-full ${wrapperClass}`,
      }}
      currency={currencyName && currencyName !== "none" ? currencyName : "USD"}
      {onValueChange}
      {placeholder}
      {isNegativeAllowed}
      bind:value />
  {/key}
{/if}
