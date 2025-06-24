<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import { PaymentMethod, PaypalType } from "$lib/common/enum";
  import Bank from "$lib/components/preview/payment/bank.svelte";
  import PaypalAutoConfirmation from "$lib/components/preview/payment/paypal-auto-confirmation.svelte";
  import PaypalDirect from "$lib/components/preview/payment/paypal-direct.svelte";
  import Xendit from "$lib/components/preview/payment/xendit.svelte";
  import Woocommerce from "$lib/components/preview/payment/woocommerce.svelte";

  import { enablePaymentPage } from "$lib/stores/settings-store";

  export let link = null;
  export let payments = [];
  export let isPaid = false;
  export let isRecurring = false;
  export let isQuotation = false;

  $: payments = payments?.filter(function(p) {
    if($enablePaymentPage) {
      return p.method == PaymentMethod.BANK || (p.method == PaymentMethod.PAYPAL && p.type == 'direct payment');
    }
    return true;
  })
</script>

<div class="w-1/2 space-y-2">
  <div class="font-bold">Payment Method</div>
  <div class="space-y-3">
    {#each payments as payment, i}
      <div class="text-xs">
        <div class="font-semibold text-sm">
          {capitalizeFirstLetter(payment.method)}
        </div>
        {#if payment.method === PaymentMethod.BANK}
          <Bank bank={payment} />
        {:else if payment.method === PaymentMethod.PAYPAL && payment.type === PaypalType.AUTO}
          <PaypalAutoConfirmation paypal={payment} {isRecurring} {isPaid} />
        {:else if payment.method === PaymentMethod.PAYPAL && payment.type === PaypalType.DIRECT}
          <PaypalDirect paypal={payment} {isPaid} />
        {:else if payment.method === PaymentMethod.XENDIT}
          <Xendit xendit={payment} {isRecurring} {isPaid} />
        {:else if payment.method === PaymentMethod.WOOCOMMERCE}
          <Woocommerce wc={payment} />
        {/if}
      </div>
    {/each}
    {#if $enablePaymentPage && isQuotation !== true && link}
    <div class="mt-2 text-xs">
      <div class="font-semibold text-sm">Payment Link</div>
      <div>
        <a class="text-primary" target="_blank" href={link}>{link}</a>
      </div>
    </div>
  {/if}
  </div>
</div>
