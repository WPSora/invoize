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
    enablePaymentPage,
    directPaypals,
    automaticPaypals,
    xenditKey,
    xenditToken,
    banks,
    paymentOnWoocommerce,
    isPro,
  } from "$lib/stores/settings-store";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Loader2 } from "lucide-svelte";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { savePaymentTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import { Label } from "$lib/components/ui/label";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Button } from "$lib/components/ui/button";
  import { toast } from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { PaymentMethod } from "$lib/common/enum";
  import Separator from "$lib/components/ui/separator/separator.svelte";
  import { selectedClient } from "$lib/stores/invoice-store";

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
    } else if ($defaultPayment?.value === PaymentMethod.XENDIT && (!$xenditKey || !$xenditToken)) {
      $defaultPayment = null;
      submit(emptyData);
    }
  }

  const submit = (customPayload = null) => {
    if (isLoading) return;
    isLoading = true;
    const defaultPaymentData = customPayload ?? $defaultPayment;
    toast.promise(
      createPostRequest("settings/update?tab=payment", {
        default: defaultPaymentData,
        enablePaymentPage: $enablePaymentPage,
        paymentOnWoocommerce: $paymentOnWoocommerce,
      }),
      {
        loading: "Saving...",
        success: () => {
          isLoading = false;
          return "Default payment updated";
        },
        error: (err) => {
          isLoading = false;
          return handleError(err, "Failed to save settings");
        },
      },
    );
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

  const handlePaymentOnWoocommerceChange = (payment, isChecked) => {
    if (isChecked) {
      // Add payment if it's not already in the array
      if (!$paymentOnWoocommerce.includes(payment)) {
        $paymentOnWoocommerce.push(payment);
      }
    } else {
      // Remove payment if it's in the array
      $paymentOnWoocommerce = $paymentOnWoocommerce.filter((selectedPayment) => payment !== selectedPayment);
    }

    if (!$paymentOnWoocommerce.length) {
      $paymentOnWoocommerce = [""];
    }
  };

  onMount(() => {
    !$hasPaymentTabSettings && getPaymentSettings();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">General</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">Manage your general payment setting.</CardDescription>
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

    <form class="space-y-3" on:submit|preventDefault="{() => submit()}">
      <div class="space-y-2 mb-4">
        <Label for="default_payment" class="md:text-sm text-xs">Default Payment</Label>
        <Select bind:selected="{$defaultPayment}">
          <SelectTrigger class="sm:w-[400px] w-full" disabled="{isFetching}">
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
            <SelectItem value="{PaymentMethod.BANK}" label="Bank" disabled="{isEmptyCheck($banks)}">Bank</SelectItem>

            <!-- Paypal Auto Confirmation -->
            <SelectItem
              value="{PaymentMethod.PAYPAL_AUTO_CONFIRMATION}"
              label="Paypal (Auto-Confirmation payment)"
              disabled="{true}">
              Paypal (Auto-Confirmation payment)
              {#if !$isPro}
                <span class="text-primary"> &nbsp;(Pro Only) </span>
              {/if}
            </SelectItem>

            <!-- Paypal Direct Payment -->
            <SelectItem
              value="{PaymentMethod.PAYPAL_DIRECT}"
              label="Paypal (Direct payment)"
              disabled="{isEmptyCheck($directPaypals)}">
              Paypal (Direct payment)
            </SelectItem>

            <!-- Xendit -->
            <SelectItem
              value="{PaymentMethod.XENDIT}"
              label="Xendit"
              disabled="{!$xenditKey || !$xenditToken || !$isPro}">
              Xendit
              {#if !$isPro}
                <span class="text-primary"> &nbsp;(Pro Only) </span>
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
      <Separator />
      <!-- Payment Page -->
      <div class="space-y-4">
        <div class="items-top flex space-x-2">
          <Checkbox id="payment-page" bind:checked="{$enablePaymentPage}" />
          <div class="grid gap-1.5 leading-none">
            <Label
              for="payment-page"
              class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
              Payment Page
            </Label>
            <p class="text-muted-foreground text-sm">
              Using the payment page means all payments are handled on a single page within your WordPress site, <a
                href="#"
                target="_blank"
                class="text-primary m-0 p-0">Read more</a>
            </p>
          </div>
        </div>
      </div>
      <Separator />
      <div class="space-y-2">
        <Label class="text-sm">Display Payment Method on WooCommerce Invoice</Label>
        <small class="text-gray-500">
          It will show the chosen payment method on invoices generated from WooCommerce, <a
            href="#"
            class="text-primary">Read more</a>
        </small>
        <div class="space-y-4">
          <div class="items-top flex space-x-2">
            <Checkbox
              id="payment-bank"
              value="{PaymentMethod.BANK}"
              checked="{$paymentOnWoocommerce.includes(PaymentMethod.BANK)}"
              onCheckedChange="{(isChecked) => {
                handlePaymentOnWoocommerceChange(PaymentMethod.BANK, isChecked);
              }}" />
            <div class="grid gap-1.5 leading-none">
              <Label
                for="payment-bank"
                class="text-sm font-normal leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                Bank
              </Label>
            </div>
          </div>
          <div class="items-top flex space-x-2">
            <Checkbox
              id="payment-paypal-auto"
              value="{PaymentMethod.PAYPAL_AUTO_CONFIRMATION}"
              checked="{$paymentOnWoocommerce.includes(PaymentMethod.PAYPAL_AUTO_CONFIRMATION)}"
              disabled="{true}"
              onCheckedChange="{(isChecked) => {
                handlePaymentOnWoocommerceChange(PaymentMethod.PAYPAL_AUTO_CONFIRMATION, isChecked);
              }}" />
            <div class="grid gap-1.5 leading-none">
              <Label
                for="payment-paypal-auto"
                class="text-sm font-normal leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 {isEmptyCheck(
                  $automaticPaypals,
                ) || !$isPro
                  ? 'text-gray-500'
                  : ''}">
                Paypal (Auto-Confirmation payment)
                <span class="text-primary"> (Coming Soon) </span>
                <!-- {#if !$isPro}
                  <span class="text-primary"> (Pro Only) </span>
                {/if} -->
              </Label>
              <p class="text-muted-foreground text-sm">
                Currently, PayPal Auto-Confirmation is not available on the payment page, but you can still use it
                without payment page.
              </p>
            </div>
          </div>
          <div class="items-top flex space-x-2">
            <Checkbox
              id="payment-paypal-direct"
              value="{PaymentMethod.PAYPAL_DIRECT}"
              checked="{$paymentOnWoocommerce.includes(PaymentMethod.PAYPAL_DIRECT)}"
              disabled="{isEmptyCheck($directPaypals)}"
              onCheckedChange="{(isChecked) => {
                handlePaymentOnWoocommerceChange(PaymentMethod.PAYPAL_DIRECT, isChecked);
              }}" />
            <div class="grid gap-1.5 leading-none">
              <Label
                for="payment-paypal-direct"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 {isEmptyCheck(
                  $directPaypals,
                )
                  ? 'text-gray-500'
                  : ''}">
                Paypal (Direct Payment)
              </Label>
            </div>
          </div>
          <div class="items-top flex space-x-2">
            <Checkbox
              id="payment-xendit"
              value="{PaymentMethod.XENDIT}"
              checked="{$paymentOnWoocommerce.includes(PaymentMethod.XENDIT)}"
              disabled="{!$xenditKey || !$xenditToken || !$isPro}"
              onCheckedChange="{(isChecked) => {
                handlePaymentOnWoocommerceChange(PaymentMethod.XENDIT, isChecked);
              }}" />
            <div class="grid gap-1.5 leading-none">
              <Label
                for="payment-xendit"
                class="text-sm font-normal leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                Xendit
                {#if !$isPro}
                  <span class="text-primary"> (Pro Only) </span>
                {/if}
              </Label>
            </div>
          </div>
        </div>
      </div>

      <Button disabled="{isLoading}">
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
