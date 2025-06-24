<script>
  import { currencyFormatter } from "$lib/helpers/decimalFormatter";

  export let isRecurring;
  export let isPaid;
  export let xendit;

  $: link = xendit?.checkout?.invoice_url;
</script>

<div class="flex flex-col mb-1">
  <div class="text-xs font-medium">(Credit Card / Other payment method)</div>
  {#if link || isRecurring}
    <div class="flex flex-nowrap gap-3">
      <div>
        <div>Total</div>
        {#if link && !isPaid}
          <div>Link</div>
        {/if}
      </div>
      <div>
        <div>:</div>
        {#if link && !isPaid}
          <div>:</div>
        {/if}
      </div>
      <div>
        <div>{currencyFormatter(xendit.currency, xendit.total)}</div>
        {#if link && !isPaid}
          <a href={link?.includes("http") ? link : `https://${link}`} target="_self" class="w-1/2 text-blue-600">
            {link}
          </a>
        {/if}
      </div>
    </div>
  {/if}
</div>

{#if !link && !isRecurring}
  <div class="text-destructive text-xs">Payment link unavailable.</div>
{/if}
