<script>
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Loader2 } from "lucide-svelte";
  import { removeArrowOnInputStyle } from "$lib/common/styles";
  import {
    hasReceiptTabSettings,
    receiptPrefix,
    receiptStartFromNumber,
    receiptTermsAndConditions,
  } from "$lib/stores/settings-store";
  import { handleError } from "$lib/helpers/errorHelper";
  import { onMount } from "svelte";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Input from "$lib/components/ui/input/input.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import toast from "svelte-french-toast";

  let isLoading = false;
  let payload = {
    prefix: "#",
    termsAndConditions: "",
    startFromNumber: 1,
  };

  const submit = () => {
    if (isLoading) {
      return;
    }
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=receipt", payload), {
      loading: "Saving...",
      success: () => {
        saveFromInputToStore(payload);
        isLoading = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  // save from input to store
  const saveFromInputToStore = (res) => {
    $receiptPrefix = res?.prefix ?? "";
    $receiptStartFromNumber = res?.startFromNumber ?? 1;
    $receiptTermsAndConditions = res?.termsAndConditions ?? "";
  };

  // save from api to store
  const saveFromApiToStore = (res) => {
    const settings = {};
    res.map((item) => {
      settings[item.name] = item.value;
    });

    $receiptPrefix = settings?.prefix ?? "";
    $receiptStartFromNumber = settings?.startFromNumber ?? 1;
    $receiptTermsAndConditions = settings?.termsAndConditions ?? "";

    saveFromStoreToInput();
  };

  // this will update input value from store, because we don't bind the value from store to input directly
  // this only for general tab
  const saveFromStoreToInput = () => {
    payload.startFromNumber = $receiptStartFromNumber;
    payload.prefix = $receiptPrefix;
    payload.termsAndConditions = $receiptTermsAndConditions;
  };

  const getInvoiceTabSettings = async () => {
    try {
      const res = await createGetRequest(`settings/retrieve?tab=receipt`);
      saveFromApiToStore(res.data.data);
      $hasReceiptTabSettings = true;
    } catch (err) {
      handleError(err, "Failed to retrieve settings data.");
    }
  };

  onMount(() => {
    !$hasReceiptTabSettings && getInvoiceTabSettings();
    // when user first fetch API, this won't save the value to store. We call this again inside saveFromApiToStore.
    // So this only works for whenever user navigate away and back to this page.
    saveFromStoreToInput();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">General</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">Manage receipt general settings.</CardDescription>
  </CardHeader>
  <CardContent class="md:px-6 md:pb-6 p-0">
    <form on:submit|preventDefault={() => submit()} class="space-y-10">
      <div class="flex md:flex-row flex-col justify-between gap-10">
        <div class="w-full md:w-1/2 space-y-8">
          <!-- Invoice Prefix -->
          <div class="w-full">
            <Label for="prefix" class="md:text-sm text-xs">Prefix</Label>
            <Input
              type="text"
              id="prefix"
              class="md:text-sm text-xs"
              disabled={isLoading}
              bind:value={payload.prefix} />
          </div>

          <!-- Start From Number -->
          <div class="w-full">
            <Label for="startFromNumber" class="md:text-sm text-xs">Start From Number</Label>
            <Input
              type="number"
              id="startFromNumber"
              class="{removeArrowOnInputStyle} md:text-sm text-xs"
              disabled={isLoading}
              bind:value={payload.startFromNumber} />
          </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="w-full md:w-1/2">
          <Label class="cursor-default md:text-sm text-xs">Terms & Conditions</Label>
          <Textarea
            id="note"
            disabled={isLoading}
            placeholder="Your terms & conditions here"
            bind:value={payload.termsAndConditions}
            class="w-full md:h-32 h-60 p-4 md:text-sm text-xs" />
        </div>
      </div>

      <!-- Save button -->
      <div class="mt-5">
        <Button disabled={isLoading} class="sm:w-fit w-full">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save Changes
          {/if}
        </Button>
      </div>
    </form>
  </CardContent>
</Card>
