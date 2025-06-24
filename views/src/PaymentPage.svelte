<script>
  import Button from "$lib/components/ui/button/button.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import { Loader2, BadgeCheck, AlertCircle } from "lucide-svelte";
  import { toast, Toaster } from "svelte-french-toast";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";
  import { PaymentMethod } from "$lib/common/enum";
  import Woocommerce from "$lib/components/preview/payment/woocommerce.svelte";

  let paymentOnProgress = false;
  let paymentBankPage = false;
  let paymentDirectPaypalPage = false;
  let paymentWoocommercePage = false;

  const payload = {
    token: invoize_payment.token,
    payment: invoize_payment.default_payment,
  };

  const proceedPayment = () => {
    if (payload.payment == PaymentMethod.BANK) {
      paymentBankPage = true;
    } else if(payload.payment == PaymentMethod.PAYPAL || payload.payment === PaymentMethod.PAYPAL_DIRECT) {
      paymentDirectPaypalPage = true;
    } else if (payload.payment == PaymentMethod.XENDIT) {
      paymentOnProgress = true;
     createPostRequest(`${invoize.api_url}/payment/pay`, payload, (res) => {
        paymentOnProgress = false;
        window.location.href = res.data.link
     })
      .catch((err) => {
        let message = (err.message?.response?.data?.message)
        message = message ? message : "Can't Process your payment"
        toast.error(message)
      }).finally(() => {
        paymentOnProgress = false;
      });
    } else if (payload.payment === PaymentMethod.WOOCOMMERCE) {
      paymentWoocommercePage = true;
    } else {
      toast.error("There is something wrong with this payment. Please contact the merchant to change/update this invoice payment.");
    }
  };
</script>
<Toaster
    position="top-right"
    containerStyle=" font-size: 11pt; margin-top: 30px;"
    toastOptions={{
      style: `
        color: #4100ae;
        border: 1px solid #4100ae;
        box-shadow: 6px 6px 12px 0px gray;
      `,
    }} />
<div
  class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-lg mx-auto">
    <!-- Header -->
    <div class="text-center mb-4">
      <div class="flex justify-center items-center gap-3 mb-8">
        <img
          src={invoize_payment?.business?.logo}
          alt="Business Logo"
          class="h-7 w-auto" />
          <div class="text-xl">{invoize_payment?.business?.name}</div>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-1">
        One last step for finishing your order...
      </h1>
      <p>{invoize_payment.subheading_text}</p>
    </div>

    <!-- Card Section -->
    <div class="p-6 mb-6 shadow-lg bg-white rounded-lg">
      <!-- Invoice is Paid -->
      {#if invoize_payment.payment_status.toLowerCase() == "paid"}
        <div class="justify-center flex flex-col items-center">
          <BadgeCheck class="w-[50px] h-[50px] text-green-400"/>
          <span class="text-xl">Invoice has been paid</span>
        </div>
      {/if}

      <!-- Invoice Not Active -->
      {#if invoize_payment.invoice_status != 'active'}
        <div class="justify-center flex flex-col items-center">
          <AlertCircle class="w-[50px] h-[50px] text-red-400"/>
          <span class="text-xl">
            Invoice is inactive
          </span>
          <small class="text-xs text-gray-400">
            If you believe this status is incorrect, please contact us for assistance.
          </small>
        </div>
      {/if}

      <!-- Invoice active and not paid -->
      {#if invoize_payment.payment_status.toLowerCase() == "unpaid" && invoize_payment.invoice_status == 'active'}
        {#if paymentBankPage || paymentDirectPaypalPage || paymentWoocommercePage}
          <!-- Information -->
          <Label class="mb-3 text-md">Payment Information</Label>
          <div class="border border-gray-300 p-4 rounded-md mb-4">
            {#if paymentBankPage}
              <MultilineText
                text={invoize_payment.bank_information}
                class="text-gray-500" />
            {:else if paymentDirectPaypalPage}
              <a class="text-blue-500" href="https://{invoize_payment.paypal_information}" target="_blank">
                {invoize_payment.paypal_information}
              </a>
            {:else if paymentWoocommercePage}
              <Woocommerce wc={invoize_payment.woocommerce_information}/>
            {/if}
          </div>
          <div class="text-center text-gray-500">
            Please contact us for confirmation after making your payment.
            <!-- <span
              >Please go to <a
                href="#"
                target="_blank"
                class="text-primary font-bold"
                >My Account > Order
              </a>and upload your proof of transfer.</span> -->
          </div>
          <!-- Upload Bukti -->
        {:else}
          <div class="space-y-4">
            <!-- Order ID -->
            {#each invoize_payment.columns as col}
              <div class="flex justify-between items-center pb-4 border-b">
                <div class="flex flex-col">
                  <span class="text-gray-500">{col.label}</span>
                  {#if col.description}
                    <small class="text-xs text-gray-500"
                      >{col.description}</small>
                  {/if}
                </div>

                {#if col.type == "options"}
                  <select
                    class="border border-gray-300 px-4 py-2 rounded-sm capitalize w-1/2"
                    bind:value={payload.payment}>
                    {#each col.value as selectValue}
                      <option value={selectValue.value}
                        >{selectValue.label}</option>
                    {/each}
                  </select>
                {:else}
                  <span class="font-mono">{col.value}</span>
                {/if}
              </div>
            {/each}

            {#each invoize_payment.highlight_columns as hcol}
              <div class="flex justify-between items-center pt-2 gap-12">
                <div class="flex flex-col">
                  <span class="text-gray-700 text-md font-semibold">{hcol.label}</span>
                  {#if hcol.description}
                    <small class="text-xs text-gray-500"
                      >{hcol.description}</small>
                  {/if}
                </div>
                
                <span class="text-xl font-bold text-primary"
                  >{hcol.value}</span>
              </div>
            {/each}
          </div>
        {/if}
      {/if}
    </div>


    <!-- Button -->
    {#if (payload.payment == "bank" || payload.payment == 'paypal' || payload.payment == 'paypal-direct' || payload.payment === PaymentMethod.WOOCOMMERCE) && (paymentBankPage || paymentDirectPaypalPage || paymentWoocommercePage)}
      <Button
        class="w-full h-12 text-lg gap-2 rounded-md flex items-center justify-center transition"
        variant="link"
        on:click={() => {
            if (payload.payment == 'bank') {
              paymentBankPage = false;
            } else if (payload.payment == 'paypal' || payload.payment == 'paypal-direct') {
              paymentDirectPaypalPage = false;
            } else if (payload.payment === PaymentMethod.WOOCOMMERCE) {
              paymentWoocommercePage = false;
            }
        }}>
        <svg
          class="h-5 w-5 ml-2"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
      </Button>
    {:else}
      {#if invoize_payment.payment_status.toLowerCase() == "unpaid"}
      <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="text-center flex items-center gap-2 text-sm text-gray-600">
          <span>{invoize_payment.footer_text}</span>
        </div>
      </div>
        <Button
          class="w-full h-12 text-lg gap-2 text-white rounded-md flex items-center justify-center transition"
          on:click={proceedPayment}
          disabled={paymentOnProgress}>
          {#if paymentOnProgress}
            <Loader2 class="h-5 w-5 animate-spin" />
            Processing
          {:else}
              <svg
                class="h-5 w-5 ml-2"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            Proceed to Payment
          {/if}
        </Button>
      {/if}
    {/if}

    <!-- Footer -->
    <div class="mb-2 fixed bottom-0 left-0 w-full flex items-center justify-center gap-4">
      <span class="text-sm text-gray-500">
        Invoize by 
        <a
          href="https://wpsora.com"
          target="_blank"
          class="text-primary">
          WPSora
        </a>
      </span>
    </div>
  </div>
</div>
