<script>
  import { createPostRequest } from "$lib/helpers/request";
  import { Dialog, Content, Title } from "$lib/components/ui/dialog";
  import { Button } from "$lib/components/ui/button";
  import { clients, client, selectedClientBind, defaultClient } from "$lib/stores/invoice-store";
  import { Loader2 } from "lucide-svelte";
  import { Switch } from "$lib/components/ui/switch";
  import { Textarea } from "$lib/components/ui/textarea";
  import { handleError } from "$lib/helpers/errorHelper";
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import toast from "svelte-french-toast";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";

  let isLoading = false;
  export let isOpen = false;
  export let isEditing = false;
  export let isCreateFromInvoicePage = false;

  const resetForm = () => {
    $client = { ...$defaultClient };
  };

  const addCustomer = async () => {
    isLoading = true;
    toast.loading("Saving...");
    createPostRequest("client/create", $client, (res) => {
      toast.dismiss();
      toast.success("Customer added successfully");
      const { data } = res.data;
      $clients = [data, ...$clients];
      // this update the actual value, but we don't need to do it because
      // everytime selectedClientBind is updated, it also will update selectedClient
      // $selectedClient = { ...$client };
      // so we only update this, which will update UI
      if (isCreateFromInvoicePage) {
        $selectedClientBind = { label: $client.name, value: { ...data } };
      }
      isOpen = false;
      isLoading = false;
    }).catch((err) => {
      isLoading = false;
      handleError(err, "Failed to add customer", true, true);
    });
  };

  const updateCustomer = async () => {
    isLoading = true;
    toast.loading("Updating...");
    createPostRequest("client/update", $client, (res) => {
      toast.dismiss();
      toast.success("Customer data updated");
      $clients = $clients.map((c) => (c.id === $client.id ? $client : c));
      isOpen = false;
      isLoading = false;
    }).catch((err) => {
      isLoading = false;
      handleError(err, "Failed to update customer", true, true);
    });
  };
</script>

<!-- Add or Edit client modal -->
<Dialog bind:open={isOpen} onOpenChange={() => resetForm()}>
  <Content>
    <Title>{isEditing ? "Edit" : "Add"} Client Information</Title>
    <form on:submit|preventDefault={() => (isEditing ? updateCustomer() : addCustomer())}>
      <div class="space-y-6 p-4">
        <div class="space-y-2">
          <Label for="name">Name <MiniStar /></Label>
          <Input type="text" id="name" required bind:value={$client.name} placeholder="Customer name" />
        </div>
        <div class="space-y-2">
          <Label for="email">Email <MiniStar /></Label>
          <Input type="email" id="email" required bind:value={$client.email} placeholder="Customer email" />
        </div>
        <!-- <div class="grid sm:grid-cols-2 grid-cols-1 gap-6"> -->
        <!-- <div class="space-y-2">
            <Label for="phone-number">Phone Number</Label>
            <Input type="tel" id="phone-number" bind:value={$client.phoneNumber} placeholder="Customer phone number" />
          </div> -->
        <!-- </div> -->
        <div class="space-y-2">
          <Label for="address">Other Information</Label>
          <Textarea
            id="address"
            bind:value={$client.address}
            placeholder="Add additional customer information such as address, company origin, etc..."
            class="h-44" />
        </div>
        <!-- <div class="grid sm:grid-cols-2 grid-cols-1 gap-6">
          <div class="space-y-2">
            <Label for="zip">Zip Code</Label>
            <Input type="text" id="zip" bind:value={$client.zip} placeholder="Customer ZIP Code" />
          </div>
          <div class="space-y-2">
            <Label for="website">Website</Label>
            <Input type="text" id="website" bind:value={$client.website} placeholder="Example-website.com" />
          </div>
        </div> -->
        <div class="space-y-1">
          <div class="flex flex-nowrap items-center gap-2">
            <Label for="preview-access">Grant Preview Access</Label>
            <Switch id="preview-access" bind:checked={$client.preview_access} />
          </div>
          <div class="text-xs text-muted-foreground">
            Granting preview access means it will create User with "Invoize Customer" role and send user credentials to
            their email now. They will be able to access invoice preview after you send them the invoice.
          </div>
        </div>

        <div class="space-y-2 pt-4 w-full flex justify-end">
          <Button disabled={isLoading} type="submit">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Save Changes
            {/if}
          </Button>
        </div>
      </div>
    </form>
  </Content>
</Dialog>
