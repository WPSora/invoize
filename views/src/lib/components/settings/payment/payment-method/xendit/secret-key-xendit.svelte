<script>
  import { CardHeader, CardDescription, CardTitle, CardContent, CardFooter } from "$lib/components/ui/card";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { xenditKey as xenditKeyStore, xenditToken as xenditTokenStore } from "$lib/stores/settings-store";
  import { Button } from "$lib/components/ui/button";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import { Plus, Eye, EyeOff, Copy } from "lucide-svelte";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import toast from "svelte-french-toast";

  import AddEditModal from "$lib/components/settings/payment/payment-method/paypal/add-edit-direct-paypal-modal.svelte";
  import DeleteDialog from "$lib/components/settings/payment/payment-method/paypal/delete-dialog.svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import { handleError } from "$lib/helpers/errorHelper";

  const webhookUrl = `${invoize.base_url}/invoize-xendit-webhook/`;

  let isLoading = false;
  let isDeleteModalOpen = false;
  let isModalOpen = false;
  let isModalForEdit = false;
  // for input value
  let xenditKey;
  let xenditToken;
  let isSecretKeyHidden = true;
  let isTokenHidden = true;

  const resetXenditKey = () => {
    xenditKey = null;
    xenditToken = null;
  };
  const resetXenditKeyStore = () => {
    $xenditKeyStore = null;
    $xenditTokenStore = null;
  };

  const updateStore = (key, token) => {
    $xenditKeyStore = key;
    $xenditTokenStore = token;
  };

  const handleCreate = () => {
    resetXenditKey();
    isModalOpen = true;
    isModalForEdit = false;
  };

  const handleEdit = () => {
    isModalForEdit = true;
    xenditKey = $xenditKeyStore;
    xenditToken = $xenditTokenStore;
    isModalOpen = true;
  };

  const handleDelete = () => {
    xenditKey = $xenditKeyStore;
    xenditToken = $xenditTokenStore;
    isDeleteModalOpen = true;
  };

  const handleDeleteModalOnOpenChange = () => {
    isDeleteModalOpen = false;
  };

  const submit = () => {
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=payment", { xenditKey, xenditToken }), {
      loading: "Saving...",
      success: () => {
        updateStore(xenditKey, xenditToken);
        resetXenditKey();
        isLoading = false;
        isModalOpen = false;
        return "Setting saved";
      },
      error: (err) => {
        isLoading = false;
        isModalOpen = false;
        return handleError(err, "Failed to save settings", false);
      },
    });
  };

  const deleteXenditKey = () => {
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=payment", { xenditKey: "", xenditToken: "" }), {
      loading: "Saving...",
      success: () => {
        resetXenditKey();
        resetXenditKeyStore();
        isModalOpen = false;
        isLoading = false;
        isDeleteModalOpen = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        isModalOpen = false;
        isDeleteModalOpen = false;
        return handleError(err, "Failed to save settings", false);
      },
    });
  };

  const toggleIsSecretHidden = () => {
    isSecretKeyHidden = !isSecretKeyHidden;
  };

  const toggleIsTokenHidden = () => {
    isTokenHidden = !isTokenHidden;
  };

  const copyUrl = () => {
    window.navigator.clipboard
      .writeText(webhookUrl)
      .then(() => {
        toast.success("Copied to clipboard");
      })
      .catch(() => {
        toast.error("Failed to copy to clipboard");
      });
  };
</script>

<div class="flex-1">
  <CardHeader class="space-y-0.5 md:px-0 md:pt-0 md:pb-2 px-0">
    <CardTitle class="md:text-base">Xendit Secret Key</CardTitle>
    <CardDescription class="text-xs flex justify-between items-center">
      <div>Connect to your Xendit account using secret key</div>
      <div>
        To configure your account, go to <a
          href="https://docs.xendit.co/id/api-integration/api-keys"
          class="text-primary"
          target="_blank">this link</a>
      </div>
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
      {#if !$xenditKeyStore}
        <div class="flex justify-between items-center bg-secondary p-4 rounded-md">
          <div class="text-sm text-muted-foreground italic">No saved secret key</div>
          <div class="flex items-center">
            <Button variant="link" class="p-0 text-xs" on:click="{handleCreate}">
              <Plus class="h-4 w-4 text-primary mr-1" />
              Add key
            </Button>
          </div>
        </div>
      {:else}
        <div class="flex justify-between items-center bg-muted p-4 rounded-lg">
          <div
            class="flex md:flex-row flex-col gap-2 {isSecretKeyHidden
              ? 'items-center'
              : 'items-start'} text-sm text-muted-foreground">
            <div class="space-y-4">
              <div class="flex items-center gap-2">
                <div class="text-nowrap w-32">Secret key</div>
                <div class="flex items-center gap-2">
                  :
                  <div
                    class="text-muted-foreground italic text-wrap break-words xl:max-w-[400px] md:max-w-[200px] max-w-[200px]">
                    {#if isSecretKeyHidden}
                      ***************************
                    {:else}
                      {$xenditKeyStore}
                    {/if}
                  </div>
                  {#if isSecretKeyHidden}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click="{toggleIsSecretHidden}">
                      <Eye class="h-4 w-4 text-primary" />
                    </Button>
                  {:else}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click="{toggleIsSecretHidden}">
                      <EyeOff class="h-4 w-4 text-primary" />
                    </Button>
                  {/if}
                </div>
              </div>
              <div class="flex items-center gap-2">
                <div class="text-nowrap w-32">Webhook Token</div>
                <div class="flex items-center gap-2">
                  :
                  <div
                    class="text-muted-foreground italic text-wrap break-words xl:max-w-[400px] md:max-w-[200px] max-w-[200px]">
                    {#if isTokenHidden}
                      ***************************
                    {:else}
                      {$xenditTokenStore}
                    {/if}
                  </div>
                  {#if isTokenHidden}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click="{toggleIsTokenHidden}">
                      <Eye class="h-4 w-4 text-primary" />
                    </Button>
                  {:else}
                    <Button
                      size="icon"
                      variant="outline"
                      class="aspect-square p-0 hover:shadow-md"
                      on:click="{toggleIsTokenHidden}">
                      <EyeOff class="h-4 w-4 text-primary" />
                    </Button>
                  {/if}
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-nowrap items-center gap-1 ml-1">
            <EditButton on:click="{handleEdit}" />
            <DeleteButton on:click="{handleDelete}" />
          </div>
        </div>
      {/if}
    </CardContent>
  {/if}
</div>

<DeleteDialog
  {handleDeleteModalOnOpenChange}
  {isLoading}
  handleDelete="{deleteXenditKey}"
  {isDeleteModalOpen}
  name="{'Xendit secret key'}" />
<AddEditModal
  modalTitle="Xendit Secret Key"
  modalDescription="Add secret key and token to connect to your Xendit account"
  label="Secret key"
  placeholder="Your Xendit secret key"
  {isLoading}
  {submit}
  resetInputValue="{resetXenditKey}"
  bind:isOpen="{isModalOpen}"
  bind:inputValue="{xenditKey}">
  <div class="space-y-2 py-2">
    <Label for="xendit-token">Webhook verification token <MiniStar /></Label>
    <Input
      type="text"
      id="xendit-token"
      required
      bind:value="{xenditToken}"
      placeholder="Your Xendit webhook verification token" />
  </div>
  <div class="text-xs space-y-2 bg-muted p-2 rounded-md">
    <div>Add this URL to your Xendit account. Go to:</div>
    <div class="font-semibold">Settings > Webhook > Webhook URL > Invoices paid</div>
    <div class="p-3 flex items-center justify-between rounded-md border bg-white border-white text-primary">
      {webhookUrl}
      <Button size="icon" variant="outline" type="button" on:click="{copyUrl}">
        <Copy />
      </Button>
    </div>
  </div>
</AddEditModal>
