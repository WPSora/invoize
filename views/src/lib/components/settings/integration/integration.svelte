<script>
  import { Button } from "$lib/components/ui/button";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Switch } from "$lib/components/ui/switch";
  import { Label } from "$lib/components/ui/label";
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { Loader2 } from "lucide-svelte";
  import { onMount } from "svelte";
  import { isWcInstalled, woocommerceIntegration } from "$lib/stores/settings-store";
  import { saveIntegrationTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import { slide } from "svelte/transition";
  import toast from "svelte-french-toast";

  let selectedOnNewOrderInput = { label: null, value: null };
  let selectedUpdateToPaidInput = { label: null, value: null };
  let selectedOnCancelOrderInput = { label: null, value: null };

  let isLoading = false;
  let isFetching = false;
  let payload = {
    cancelOnCancelOrder: false,
    createOnNewOrder: false,
    sendOnNewOrder: false,
    setToPaidOn: "processsing", // | 'completed' | 'none'
    sendOnPaid: false,
    disableOnZeroTotal: false,
  };

  $: disableSendInvoiceOnPaidInput = payload.setToPaidOn === "none";

  const saveFromStoreToInput = () => {
    payload = { ...$woocommerceIntegration };
    // Cast from string to bool
    Object.keys(payload).forEach((key) => {
      if (payload[key] === "true") {
        payload[key] = true;
      }

      if (payload[key] === "false") {
        payload[key] = false;
      }
    });

    if (!payload.createOnNewOrder && !payload.sendOnNewOrder) {
      selectedOnNewOrderInput = { label: "Do nothing", value: "nothing" };
    } else if (payload.createOnNewOrder && !payload.sendOnNewOrder) {
      selectedOnNewOrderInput = { label: "Create Invoice", value: "create" };
    } else if (payload.createOnNewOrder && payload.sendOnNewOrder) {
      selectedOnNewOrderInput = { label: "Create & Send Invoice", value: "create-send" };
    }

    if (payload.setToPaidOn === "none") {
      selectedUpdateToPaidInput = { label: "Don't update", value: "none" };
    } else if (payload.setToPaidOn === "processing") {
      selectedUpdateToPaidInput = { label: "On Processing", value: "processing" };
    } else if (payload.setToPaidOn === "completed") {
      selectedUpdateToPaidInput = { label: "On Completed", value: "completed" };
    }

    selectedOnCancelOrderInput = payload.cancelOnCancelOrder
      ? { label: "Cancel Invoice", value: "cancel-invoice" }
      : { label: "Do Nothing", value: "nothing" };
  };

  const handleOnNewOrderInput = (e) => {
    switch (e.value) {
      case "nothing":
        payload.createOnNewOrder = false;
        payload.sendOnNewOrder = false;
        break;
      case "create":
        payload.createOnNewOrder = true;
        payload.sendOnNewOrder = false;
        break;
      case "create-send":
        payload.createOnNewOrder = true;
        payload.sendOnNewOrder = true;
        break;
      default:
        break;
    }
  };

  const handleCancelOrderInput = (e) => {
    payload.cancelOnCancelOrder = e.value === "cancel-invoice";
  };

  const handleUpdatePaidInput = (e) => {
    payload.setToPaidOn = e.value;
    if (e.value == "none") {
      payload.sendOnPaid = false;
    }
  };

  const submit = () => {
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=integration", { woocommerce: payload }), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        return "Setting saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  const getSetting = async () => {
    try {
      isFetching = true;
      const response = await createGetRequest("settings/retrieve?tab=integration");
      const responseWcInstalled = await createGetRequest("settings/wc-status");
      $isWcInstalled = responseWcInstalled.data.installed;
      saveIntegrationTabToSettingsStore(response.data.data);
      saveFromStoreToInput();
      isFetching = false;
    } catch (err) {
      isFetching = false;
      isLoading = false;
      handleError(err, "Failed to retrieve settings data");
    }
  };

  onMount(() => {
    getSetting();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0 mb-2">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Woocommerce Setting</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">Manage Woocommerce integration</CardDescription>
  </CardHeader>
  <CardContent>
    {#if isFetching}
      <Loader2 class="h-5 w-5 text-primary animate-spin" />
    {:else if $isWcInstalled}
      <form on:submit|preventDefault="{submit}" class="flex flex-col gap-4">
        <div class="flex flex-col gap-5">
          <div class="flex flex-col flex-nowrap gap-2 items-start w-96">
            <Label for="on-new-order" class="md:text-sm text-xs">On New Order</Label>
            <Select selected="{selectedOnNewOrderInput}" onSelectedChange="{handleOnNewOrderInput}">
              <SelectTrigger id="on-new-order">
                <SelectValue placeholder="Choose options" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="nothing">Do nothing</SelectItem>
                <SelectItem value="create">Create Invoice</SelectItem>
                <SelectItem value="create-send">Create & Send Invoice</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="flex flex-col flex-nowrap gap-2 items-start w-96">
            <Label for="on-status" class="md:text-sm text-xs">On Canceled Order</Label>
            <Select selected="{selectedOnCancelOrderInput}" onSelectedChange="{handleCancelOrderInput}">
              <SelectTrigger id="on-status">
                <SelectValue placeholder="Choose options" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="cancel-invoice">Cancel Invoice</SelectItem>
                <SelectItem value="nothing">Do Nothing</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="flex flex-col flex-nowrap gap-2 items-start w-96">
            <Label for="update-invoice-to-paid" class="md:text-sm text-xs">Update Invoice to Paid</Label>
            <Select selected="{selectedUpdateToPaidInput}" onSelectedChange="{handleUpdatePaidInput}">
              <SelectTrigger id="update-invoice-to-paid">
                <SelectValue placeholder="Choose options" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="none">Don't update</SelectItem>
                <SelectItem value="processing">On Processing</SelectItem>
                <SelectItem value="completed">On Completed</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {#if !disableSendInvoiceOnPaidInput}
            <div transition:slide class="flex flex-row flex-nowrap justify-between gap-2 w-96 mb-2">
              <Label for="send-invoice" class="md:text-sm text-xs">Send Receipt On Paid</Label>
              <Switch id="send-invoice" bind:checked="{payload.sendOnPaid}" />
            </div>
          {/if}

          <div transition:slide class="flex flex-row flex-nowrap justify-between gap-2 w-96 mb-2">
            <Label for="disable-on-zero-total" class="md:text-sm text-xs">
              Don't create invoice if total transaction is 0
            </Label>
            <Switch id="disable-on-zero-total" bind:checked="{payload.disableOnZeroTotal}" />
          </div>
        </div>

        <Button type="submit" class="w-fit" disabled="{isLoading}">
          {#if isLoading}
            <Loader2 class="h-4 w-4 text-white animate-spin mr-1" />
            Saving
          {:else}
            Save Changes
          {/if}
        </Button>
      </form>
    {:else}
      <div class="text-sm text-muted-foreground my-3">Woocommerce is not installed</div>
    {/if}
  </CardContent>
</Card>
