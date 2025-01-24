<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let isRecurring;
  export let isPaid;
  export let paypal;

  $: link = paypal?.checkout?.links?.find((link) => link.rel === "payer-action")?.href;
</script>

<div class="flex flex-nowrap gap-3 mb-1">
  <div>
    <div>Method</div>
    <div>Name</div>
    {#if link && !isPaid}
      <div>Link</div>
    {/if}
  </div>
  <div>
    <div>:</div>
    <div>:</div>
    {#if link && !isPaid}
      <div>:</div>
    {/if}
  </div>
  <div>
    <div>{capitalizeFirstLetter(paypal.type)}</div>
    <div>{paypal.name}</div>
    {#if link && !isPaid}
      <a href={link?.includes("http") ? link : `https://${link}`} target="_self" class="w-1/2 text-blue-600">
        {link}
      </a>
    {/if}
  </div>
</div>

{#if !link && !isRecurring}
  <div class="text-destructive text-xs">Payment link unavailable.</div>
{/if}
