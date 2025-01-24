<script>
  import {
    Select,
    SelectTrigger,
    SelectContent,
    SelectItem,
    SelectValue,
    SelectInput,
  } from "$lib/components/ui/select";
  import {
    hasPaymentTabSettings,
    defaultPayment,
    directPaypals,
    automaticPaypals,
    xenditKey,
    banks,
    isPro
  } from "$lib/stores/settings-store";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Loader2 } from "lucide-svelte";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { savePaymentTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import { Label } from "$lib/components/ui/label";
  import { Button } from "$lib/components/ui/button";
  import { toast } from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { PaymentMethod } from "$lib/common/enum";

  let isLoading = false;
  let isFetching = false;

  // this will update the default payment to null if the current active default payment get deleted
  $: {
    if ($defaultPayment?.value === PaymentMethod.BANK && isEmptyCheck($banks)) {
      $defaultPayment = null;
      submit(emptyData);
    } else if ($defaultPayment?.value === PaymentMethod.PAYPAL_AUTO_CONFIRMATION && isEmptyCheck($automaticPaypals)) {
      $defaultPayment = null;
      submit(emptyData);
    } else if ($defaultPayment?.value === PaymentMethod.PAYPAL_DIRECT && isEmptyCheck($directPaypals)) {
      $defaultPayment = null;
      submit(emptyData);
    } else if ($defaultPayment?.value === PaymentMethod.XENDIT && !$xenditKey) {
      $defaultPayment = null;
      submit(emptyData);
    }
  }

  const submit = (customPayload = null) => {
    if (isLoading) return;
    isLoading = true;
    const payload = customPayload ?? $defaultPayment;
    toast.promise(createPostRequest("settings/update?tab=payment", { default: payload }), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        return "Default payment updated";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  const getPaymentSettings = async () => {
    try {
      isFetching = true;
      const res = await createGetRequest(`settings/retrieve?tab=payment`);
      savePaymentTabToSettingsStore(res.data.data);
      $hasPaymentTabSettings = true;
      isFetching = false;
    } catch (err) {
      isFetching = false;
      return handleError(err, "Failed to retrieve settings data.");
    }
  };

  onMount(() => {
    !$hasPaymentTabSettings && getPaymentSettings();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Default Payment Setting</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">
      Manage your default payment setting. This also will be used by Woocommerce.
    </CardDescription>
  </CardHeader>
  <CardContent class="md:px-6 md:pb-6 p-0">
    <!-- This shouldn't be available if the woocomerce isn't installed -->
    <!-- {#if !isWcInstalled}
      <Alert variant="warning" class="mb-4">
        <Frown class="w-4 h-4" />
        <AlertTitle>Can't found Woocommerce plugin</AlertTitle>
        <AlertDescription>Can't use Woocommerce as a payment method.</AlertDescription>
      </Alert>
    {/if} -->

    <form class="space-y-3" on:submit|preventDefault={() => submit()}>
      <div class="space-y-2 mb-4">
        <Label for="default_payment" class="md:text-sm text-xs">Default Payment</Label>
        <Select bind:selected={$defaultPayment}>
          <SelectTrigger class="sm:w-[400px] w-full" disabled={isFetching}>
            {#if isFetching}
              <div class="flex gap-2 items-center">
                <Loader2 class="h-4 w-4 text-primary animate-spin" />
                Fetching data...
              </div>
            {:else}
              <SelectValue placeholder="Select a default payment" />
            {/if}
          </SelectTrigger>
          <SelectContent>
            <!-- Bank -->
            <SelectItem value={PaymentMethod.BANK} label="Bank" disabled={isEmptyCheck($banks)}>Bank</SelectItem>

            <!-- Paypal Auto Confirmation -->
            <SelectItem
              value={PaymentMethod.PAYPAL_AUTO_CONFIRMATION}
              label="Paypal (Auto-Confirmation payment)"
              disabled={isEmptyCheck($automaticPaypals) || !$isPro}>
              Paypal (Auto-Confirmation payment)
              {#if !$isPro}
                <span class="text-primary">
                  &nbsp;(Pro Only)
                </span>
              {/if}
            </SelectItem>

            <!-- Paypal Direct Payment -->
            <SelectItem
              value={PaymentMethod.PAYPAL_DIRECT}
              label="Paypal (Direct payment)"
              disabled={isEmptyCheck($directPaypals)}>
              Paypal (Direct payment)
            </SelectItem>

            <!-- Xendit -->
            <SelectItem value={PaymentMethod.XENDIT} label="Xendit" disabled={!$xenditKey || !$isPro}>
              Xendit
              {#if !$isPro}
                <span class="text-primary">
                  &nbsp;(Pro Only)
                </span>
              {/if}
            </SelectItem>

            <!-- Woocommerce -->
            <!-- {#if isWcInstalled}
              <SelectItem value="wc" label="Woocommerce">Woocommerce</SelectItem>
            {/if} -->
          </SelectContent>
          <SelectInput name="default_payment" />
        </Select>
      </div>

      <Button disabled={isLoading}>
        {#if isLoading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Saving
        {:else}
          Save Changes
        {/if}
      </Button>
    </form>
  </CardContent>
</Card>
