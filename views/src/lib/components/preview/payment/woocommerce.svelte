<script>
  import { PaymentMethod } from "$lib/common/enum";
  import { Separator } from "$lib/components/ui/separator";

  export let wc;
</script>

<!-- Woocommerce bank transfer -->
{#if wc.name.includes("Direct bank transfer") || wc.type === PaymentMethod.WOOCOMMERCE_BANK}
  {#each wc.detail as detail, i}
    {#if wc.detail.length > 1 && i > 0}
      <Separator class="w-11/12 my-3" />
    {/if}
    <div class="text-justify {i === 0 && 'mt-2'}">
      {#if detail.account_name}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">Account name</div>
          <div class="mr-1">:</div>
          <div>{detail.account_name}</div>
        </div>
      {/if}
      {#if detail.account_number}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">Account number</div>
          <div class="mr-1">:</div>
          <div>{detail.account_number}</div>
        </div>
      {/if}
      {#if detail.bank_name}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">Bank name</div>
          <div class="mr-1">:</div>
          <div>{detail.bank_name}</div>
        </div>
      {/if}
      {#if detail.sort_code}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">Sort code</div>
          <div class="mr-1">:</div>
          <div>{detail.sort_code}</div>
        </div>
      {/if}
      {#if detail.iban}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">IBAN</div>
          <div class="mr-1">:</div>
          <div>{detail.iban}</div>
        </div>
      {/if}
      {#if detail.bic}
        <div class="flex flex-nowrap">
          <div class="min-w-24 font-medium">BIC/Swift</div>
          <div class="mr-1">:</div>
          <div>{detail.bic}</div>
        </div>
      {/if}
    </div>
  {/each}

  <!-- Woocommerce paypal -->
{:else if wc.name === PaymentMethod.WOOCOMMERCE_PAYPAL || wc.type === PaymentMethod.WOOCOMMERCE_PAYPAL}
  <div class="flex flex-nowrap gap-3 mb-1">
    <div>
      <div>Method</div>
      <div>Detail</div>
    </div>
    <div>
      <div>:</div>
      <div>:</div>
    </div>
    <div>
      <div>{wc.name}</div>
      <div>{wc.detail}</div>
    </div>
  </div>

  <!-- Woocommerce other payment -->
{:else}
  <div class="flex flex-nowrap gap-3 mb-1">
    <div>
      <div>Method</div>
      <div>Detail</div>
    </div>
    <div>
      <div>:</div>
      <div>:</div>
    </div>
    <div>
      <div>{wc.name}</div>
      <a
        target="_blank"
        class="text-blue-600 italic"
        href={wc.checkout?.includes("http") ? wc.checkout : `https://${wc.checkout}`}>
        {wc.checkout?.includes("http") ? wc.checkout : `https://${wc.checkout}`}
      </a>
    </div>
  </div>
{/if}
