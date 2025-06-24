<script>
  import { CardHeader, CardDescription, CardTitle, CardContent, CardFooter } from "$lib/components/ui/card";
  import { Button } from "$lib/components/ui/button";
  import { Plus, Eye, EyeOff, Loader2, Unplug } from "lucide-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import { isPro, isProPopupOpen, automaticPaypals, activeAutomaticPaypalType } from "$lib/stores/settings-store";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Label } from "$lib/components/ui/label";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import toast from "svelte-french-toast";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import DeleteDialog from "$lib/components/settings/payment/payment-method/paypal/delete-dialog.svelte";
  import AddEditAutoPaypalModal from "$lib/components/settings/payment/payment-method/paypal/add-edit-auto-paypal-modal.svelte";
  import Checkbox from "$lib/components/ui/checkbox/checkbox.svelte";
  import ProBadge from "$lib/components/upgrade-to-pro/ProBadge.svelte";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";

  let isDeleteModalOpen = false;
  let isModalOpen = false;
  // true means modal for adding new bank
  let isModalForEdit = false;
  let isLoading = false;
  let isTestingConnection = false;
  let testingConnectionId = 0;
  let selectedPaypalIndex = 0;
  let selectedDeleteName = "";
  let isSecretIdHidden = true;
  let activeAccount = 'live';
  let isSavingActiveAccount = false;

  let sandboxAutomaticPaypal = {
    name: "",
    clientId: "",
    secretId: "",
    isSandbox: "true",
  };

  let liveAutomaticPaypal = {
    name: "",
    clientId: "",
    secretId: "",
    isSandbox: "false",
  };

  const resetAutomaticPaypal = (isSandbox) => {
    return {
      name: "",
      clientId: "",
      secretId: "",
      isSandbox: isSandbox,
    };
  };

  const toggleIsSecretHidden = () => {
    isSecretIdHidden = !isSecretIdHidden;
  };

  const handleCreateAction = () => {
    sandboxAutomaticPaypal = resetAutomaticPaypal("true");
    liveAutomaticPaypal = resetAutomaticPaypal("false");
    isModalOpen = true;
    isModalForEdit = false;
  };

  const handleEditAction = () => {
    sandboxAutomaticPaypal =
      $automaticPaypals.find((item) => item.isSandbox === "true") ?? resetAutomaticPaypal("true");
    liveAutomaticPaypal = $automaticPaypals.find((item) => item.isSandbox === "false") ?? resetAutomaticPaypal("false");
    isModalOpen = true;
    isModalForEdit = true;
  };

  const handleDeleteAction = (data, i) => {
    selectedPaypalIndex = i;
    isDeleteModalOpen = true;
    selectedDeleteName = data?.name;
  };

  const handleDeleteModalOnOpenChange = () => {
    isDeleteModalOpen = false;
  };

  const checkIsValid = (payload) => {
    if (!payload.name) return false;
    if (!payload.clientId) return false;
    if (!payload.secretId) return false;
    return true;
  };

  const getPayload = () => {
    let payload = [];
    if (checkIsValid(sandboxAutomaticPaypal)) {
      payload.push(sandboxAutomaticPaypal);
    }
    if (checkIsValid(liveAutomaticPaypal)) {
      payload.push(liveAutomaticPaypal);
    }
    return payload;
  };

  const checkPaypalCredentials = async (credentials) => {
    if (isTestingConnection) return;
    try {
      isTestingConnection = true;
      testingConnectionId = credentials.clientId;
      await createPostRequest("paypal/authenticate", [credentials]);
      toast.success("Test Connection Successful");
    } catch (err) {
      handleError(err, "null", true, true);
    } finally {
      isTestingConnection = false;
    }
  };

  const authenticate = async () => {
    const payload = getPayload();
    if (isLoading) return;
    isLoading = true;
    toast.loading("Authenticating...");
    try {
      await createPostRequest("paypal/authenticate", payload);
      // console.log(payload);
      toast.dismiss();
      toast.success("Paypal account found");
      submit(payload);
    } catch (err) {
      $automaticPaypals = [];
      isLoading = false;
      handleError(err, null, true, true);
    }
  };

  // add or update
  const submit = async (payload) => {
    isLoading = true;
    toast.loading("Saving...");
    try {
      await createPostRequest("settings/update?tab=payment", { automaticPaypals: payload });
      toast.dismiss();
      toast.success("Setting Saved");
      $automaticPaypals = payload;
      sandboxAutomaticPaypal = resetAutomaticPaypal(true);
      liveAutomaticPaypal = resetAutomaticPaypal(false);
      isModalOpen = false;
      isLoading = false;
    } catch (err) {
      isLoading = false;
      isModalOpen = false;
      handleError(err, null, true, true);
    }
  };

  const deletePaypal = () => {
    isLoading = true;
    toast.loading("Saving...");
    const deletedData = $automaticPaypals.find((_, i) => i === selectedPaypalIndex);
    const isDeleteSandbox = deletedData?.isSandbox === "true";
    $automaticPaypals = $automaticPaypals.filter((_, i) => i !== selectedPaypalIndex);
    if ($automaticPaypals.length === 0) {
      $automaticPaypals = [...emptyData];
    }
    // if we dont delete the refresh token, then it will keep using the old token, even when user
    // update the client & secret ID. So delete the refresh token whenever user delete the client & secret ID.
    createPostRequest("paypal/delete-token", { isSandbox: isDeleteSandbox })
      .then(() => {
        return createPostRequest("settings/update?tab=payment", { automaticPaypals: $automaticPaypals });
      })
      .then(() => {
        if (isDeleteSandbox) {
          sandboxAutomaticPaypal = resetAutomaticPaypal(true);
        } else {
          liveAutomaticPaypal = resetAutomaticPaypal(false);
        }
        isModalOpen = false;
        isLoading = false;
        isDeleteModalOpen = false;
        toast.dismiss();
        toast.success("Setting saved");
      })
      .catch((err) => {
        isLoading = false;
        isModalOpen = false;
        isDeleteModalOpen = false;
        handleError(err, "Failed to save settings");
      });
  };

  const updateActiveAccount = (sandboxActive) => {
    activeAccount = sandboxActive ? 'sandbox' : 'live';
    isSavingActiveAccount = true;
    toast.loading("Saving...");
    createPostRequest("settings/update?tab=payment", { activeAutomaticPaypalType: activeAccount })
      .then(() => {
        toast.dismiss();
        if(activeAccount === "sandbox") {
          toast.success("Sandbox Account is now active");
        } else {
          toast.success("Live Account is now active");
        }
        $activeAutomaticPaypalType = activeAccount;
        isSavingActiveAccount = false;
      })
      .catch((err) => {
        isSavingActiveAccount = false;
        handleError(err, null, true, true);
      });
  };
</script>

<!-- Automatic payment -->
<div class="flex-1">
  <CardHeader class="space-y-0.5 md:px-0 md:pt-0 md:pb-2 px-0">
    <CardTitle class="text-base">
      <div class="flex">
        <span>
          Auto Confirmation
        </span>
        <div>
          <ProBadge/>
        </div>
      </div>
    </CardTitle>
    <CardDescription class="text-xs flex flex-nowrap justify-between items-center">
      <div>Auto update the invoice payment status once payment is success.</div>
      <div>To configure your account, go to this link</div>
    </CardDescription>
  </CardHeader>
  {#if isLoading}
    <div class="mt-5 mb-10 mx-8">
      {#each [1, 2] as _}
        <Skeleton class="h-12 my-8 rounded-full" />
      {/each}
    </div>
  {:else}
    <CardContent class="md:px-0 p-0">
      {#if isEmptyCheck($automaticPaypals)}
        <div class="italic text-sm text-muted-foreground mt-4 mb-8">
          {#if $isPro}
            You have no paypal API account saved.
          {:else}
          <span class="text-primary-600">
            Upgrade to Pro to enable this feature.
          </span>
          {/if}
        </div>
      {:else}
        {#each $automaticPaypals as data, i}
          <div class="flex flex-col bg-muted p-4 mb-4 rounded-lg">
            <div class="space-y-2">
              <div class="flex justify-between items-center w-full">
                <div class="text-base font-medium">{data.name}</div>
                <!-- Action buttons -->
                <div class="flex justify-start gap-2">
                  <Button bind:disabled={isTestingConnection} on:click={() => checkPaypalCredentials(data)}>
                    {#if isTestingConnection && testingConnectionId == data.clientId}
                      <Loader2 class="h-4 w-4 text-background mr-1 animate-spin" />
                      Testing Connection
                    {:else}
                      <Unplug class="h-4 w-4 text-background mr-1" />
                      Test Connection
                    {/if}
                  </Button>
                  <DeleteButton on:click={() => handleDeleteAction(data, i)} />
                </div>
              </div>
              <div class="flex flex-col gap-y-2 ml-2">
                <!-- Client ID -->
                <div class="flex gap-2 items-center text-sm text-muted-foreground">
                  <div class="min-w-20">Client ID</div>
                  <div class="text-wrap break-words">{data.clientId}</div>
                </div>
                <!-- Secret ID -->
                <div class="flex gap-2 items-center text-sm text-muted-foreground">
                  <div class="min-w-20">Secret ID</div>
                  {#if isSecretIdHidden}
                    <div>*************************</div>
                  {:else}
                    <div>{data.secretId}</div>
                  {/if}
                  {#if isSecretIdHidden}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click={toggleIsSecretHidden}>
                      <Eye class="h-4 w-4 text-primary" />
                    </Button>
                  {:else}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click={toggleIsSecretHidden}>
                      <EyeOff class="h-4 w-4 text-primary" />
                    </Button>
                  {/if}
                </div>
                <!-- Is Sandbox -->
                <div class="flex gap-2 items-center text-sm text-muted-foreground">
                  <div class="w-20">Status</div>
                  <div class="{data.isSandbox === 'true' ? 'text-yellow-500' : 'text-green-600'} font-semibold">
                    {data.isSandbox === "true" ? "Sandbox" : "Live"}
                  </div>
                </div>
              </div>
            </div>
          </div>
        {/each}
      {/if}
    </CardContent>

    <CardFooter class="px-0 flex flex-nowrap items-center justify-between">
      {#if !$isPro}
        <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} />
      {:else}
        {#if isEmptyCheck($automaticPaypals)}
          <Button on:click={handleCreateAction}>
            <Plus class="h-4" />
            Paypal API Account
          </Button>
        {:else}
          <Button on:click={handleEditAction}>Edit API Account</Button>
        {/if}
      {/if}
      
      {#if !isEmptyCheck($automaticPaypals)}
        <div class="flex items-center space-x-2">
          <Checkbox id="sandbox_mode" checked={$activeAutomaticPaypalType === 'sandbox'} onCheckedChange={updateActiveAccount} disabled={isSavingActiveAccount} value="sandbox"/>
          <Label for="sandbox_mode" class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            Enable Sandbox Mode
          </Label>
        </div>
      {/if}
    </CardFooter>
  {/if}
</div>

<DeleteDialog
  {handleDeleteModalOnOpenChange}
  {isLoading}
  handleDelete={deletePaypal}
  {isDeleteModalOpen}
  name={selectedDeleteName} />

<AddEditAutoPaypalModal
  bind:isModalAutomaticOpen={isModalOpen}
  {resetAutomaticPaypal}
  submitAutomaticPaypal={authenticate}
  bind:sandboxAutomaticPaypal
  bind:liveAutomaticPaypal
  {isLoading} />
