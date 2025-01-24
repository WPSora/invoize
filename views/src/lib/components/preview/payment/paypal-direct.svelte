<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let isPaid;
  export let paypal;
  // Need to be fixed, don't use if
  $: checkout = paypal.checkout ? paypal.checkout : paypal.name;
</script>

<div class="flex flex-nowrap gap-3 mb-1">
  <div>
    <div>Method</div>
    {#if !isPaid}
      <div>Link</div>
    {/if}
  </div>
  <div>
    <div>:</div>
    {#if !isPaid}
      <div>:</div>
    {/if}
  </div>
  <div>
    <div>{capitalizeFirstLetter(paypal.type)}</div>
    {#if !isPaid}
      <a
        target="_blank"
        class="text-blue-600 italic"
        href={checkout?.includes("http") ? checkout : `https://${checkout}`}>
        {checkout?.includes("http") ? checkout : `https://${checkout}`}
      </a>
    {/if}
  </div>
</div>
