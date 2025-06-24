<script>
  import { CardHeader, CardDescription, CardTitle } from "$lib/components/ui/card";
  import { xenditSuccessRedirectUrl, xenditFailedRedirectUrl, hasPaymentTabSettings } from "$lib/stores/settings-store";
  import RedirectUrlInput from "$lib/components/settings/payment/payment-method/xendit/redirect-url-input.svelte";

  let successUrl;
  let failedUrl;
  // update input value when API data already saved to store.
  $: $hasPaymentTabSettings && saveStoreToInput();

  const saveStoreToInput = () => {
    successUrl = $xenditSuccessRedirectUrl;
    failedUrl = $xenditFailedRedirectUrl;
  };
</script>

<div class="flex-1">
  <CardHeader class="space-y-0.5 md:px-0 md:pt-0 md:pb-2 px-0">
    <CardTitle class="md:text-base">Redirect URL</CardTitle>
    <CardDescription class="text-xs">
      Manage your Xendit payment redirect URL after payment is complete. If it's empty, customer will be redirected to
      the default page.
    </CardDescription>
  </CardHeader>

  <div class="flex flex-col gap-4">
    <RedirectUrlInput
      name="Success"
      settingName="xenditSuccessRedirectUrl"
      bind:value="{successUrl}"
      bind:redirectUrlStore="{$xenditSuccessRedirectUrl}" />
    <RedirectUrlInput
      name="Failed"
      settingName="xenditFailedRedirectUrl"
      bind:value="{failedUrl}"
      bind:redirectUrlStore="{$xenditFailedRedirectUrl}" />
  </div>
</div>
