<script>
  import { CardHeader, CardDescription, CardTitle, CardContent, CardFooter } from "$lib/components/ui/card";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { directPaypals } from "$lib/stores/settings-store";
  import { Button } from "$lib/components/ui/button";
  import { Plus } from "lucide-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import toast from "svelte-french-toast";
  import AddEditModal from "$lib/components/settings/payment/payment-method/paypal/add-edit-direct-paypal-modal.svelte";
  import DeleteDialog from "$lib/components/settings/payment/payment-method/paypal/delete-dialog.svelte";
  import { handleError } from "$lib/helpers/errorHelper";

  let isDeleteModalOpen = false;
  let isModalOpen = false;
  // true means modal for adding new paypal
  let isModalForEdit = false;
  let isLoading = false;
  let selectedPaypalIndex = 0;
  let directPaypal = "";

  const resetDirectPaypal = () => {
    directPaypal = "";
  };

  const handleCreate = () => {
    resetDirectPaypal();
    isModalOpen = true;
    isModalForEdit = false;
  };

  const handleDelete = (data, i) => {
    selectedPaypalIndex = i;
    directPaypal = data;
    isDeleteModalOpen = true;
  };

  const handleDeleteModalOnOpenChange = () => {
    isDeleteModalOpen = false;
  };

  const handleAddOrEdit = () => {
    // add new paypal
    if (!isModalForEdit) {
      if (isEmptyCheck($directPaypals)) {
        $directPaypals = [];
      }
      $directPaypals = [...$directPaypals, directPaypal];
    } else {
      $directPaypals[selectedPaypalIndex] = directPaypal;
    }
  };

  // add or update
  const submit = () => {
    if (isLoading) return;
    handleAddOrEdit();
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=payment", { directPaypals: $directPaypals }), {
      loading: "Saving...",
      success: () => {
        resetDirectPaypal();
        isLoading = false;
        isModalOpen = false;
        return "Setting saved";
      },
      error: (err) => {
        isLoading = false;
        isModalOpen = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  const deletePaypal = () => {
    isLoading = true;
    $directPaypals = $directPaypals.filter((_, i) => i !== selectedPaypalIndex);
    if ($directPaypals.length === 0) {
      $directPaypals = [...emptyData];
    }
    toast.promise(createPostRequest("settings/update?tab=payment", { directPaypals: $directPaypals }), {
      loading: "Saving...",
      success: () => {
        resetDirectPaypal();
        isModalOpen = false;
        isLoading = false;
        isDeleteModalOpen = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        isModalOpen = false;
        isDeleteModalOpen = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };
</script>

<div class="flex-1">
  <CardHeader class="space-y-0.5 md:px-0 md:pt-0 md:pb-2 px-0">
    <CardTitle class="md:text-base">Direct Payment</CardTitle>
    <CardDescription class="text-xs flex flex-nowrap justify-between items-center">
      <div>Use paypal direct payment (paypal.me)</div>
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
      {#if isEmptyCheck($directPaypals)}
        <div class="flex justify-between items-center bg-secondary p-4 rounded-md">
          <div class="text-sm text-muted-foreground italic">No saved link</div>
          <div class="flex items-center">
            <Button variant="link" class="p-0 text-xs" on:click={handleCreate}>
              <Plus class="h-4 w-4 text-primary mr-1" />
              Add link
            </Button>
          </div>
        </div>
      {:else}
        {#each $directPaypals as data, i}
          <div class="flex justify-between items-center bg-muted p-4 rounded-lg">
            <a target="_blank" class="text-blue-600 text-sm" href={data?.includes("http") ? data : `https://${data}`}>
              {data?.includes("http") ? data : `https://${data}`}
            </a>
            <div>
              <DeleteButton on:click={() => handleDelete(data, i)} />
            </div>
          </div>
        {/each}
      {/if}
    </CardContent>
  {/if}
</div>

<DeleteDialog
  {handleDeleteModalOnOpenChange}
  {isLoading}
  {isDeleteModalOpen}
  handleDelete={deletePaypal}
  name="direct payment link" />

<AddEditModal
  modalTitle="Direct Payment Link"
  modalDescription="Add your paypal.me link for payment"
  label="Link"
  placeholder="Your paypal.me link"
  {isLoading}
  {submit}
  resetInputValue={resetDirectPaypal}
  bind:isOpen={isModalOpen}
  bind:inputValue={directPaypal} />
