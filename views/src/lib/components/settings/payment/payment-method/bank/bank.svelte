<script>
  import {
    AlertDialog,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { Table, Row, Body, Cell, Head, Header, Caption } from "$lib/components/ui/table";
  import { Dialog, DialogHeader, DialogTitle, DialogContent, DialogDescription } from "$lib/components/ui/dialog";
  import { CardContent, CardFooter } from "$lib/components/ui/card";
  import { Button } from "$lib/components/ui/button";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Loader2, Plus, Check } from "lucide-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import { Separator } from "$lib/components/ui/separator";
  import { currencies, banks, defaultBank, defaultCurrency } from "$lib/stores/settings-store";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { isPro, isProPopupOpen } from "$lib/stores/settings-store";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import toast from "svelte-french-toast";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  /** @typedef {Object} Bank
   * @property {number} id
   * @property {string} name
   * @property {string} type
   * @property {Object | null} currency
   * @property {string} detail
   */

  let deleteModalOpen = false;
  let isModalOpen = false;
  // true means modal for adding new bank
  let isModalEdit = false;
  let isLoading = false;
  let isLoadingDefaultButton = false;
  let selectedBankIndex = 0;

  /** @type {Bank}*/
  let bank = {
    id: 0,
    name: "",
    type: "",
    currency: null,
    detail: "",
  };

  /** @param {Bank} data*/
  const updateStore = (data) => {
    // for edit bank
    if (isModalEdit) {
      $banks[selectedBankIndex] = data;
      // for add new bank
    } else {
      $banks = [...$banks, data];
    }
  };

  /** @param {Bank} data*/
  const setDefaultBank = (data) => {
    if (!isEmptyCheck($banks) && $banks.length === 1) {
      saveDefaultBank(data.id);
    }
  };

  const resetBank = () => {
    bank = {
      id: 0,
      name: "",
      type: "",
      currency: { label: "", value: "" },
      detail: "",
    };
  };

  const handleAddButton = () => {
    resetBank();
    setSelectedCurrency();
    isModalOpen = true;
    isModalEdit = false;
  };

  const setSelectedCurrency = () => {
    bank.currency = { label: $defaultCurrency.name, value: { ...$defaultCurrency } };
  };

  /** @param {Number} i*/
  const handleEditAction = (i) => {
    selectedBankIndex = i;
    isModalEdit = true;
    // here we copy the data instead of referencing it, so that the original data is not changed
    const selectedBank = { ...$banks[selectedBankIndex] };
    bank = selectedBank;
    // update currency so it become selected
    const currency = { label: selectedBank.currency.name, value: selectedBank.currency };
    bank.currency = currency;
    isModalOpen = true;
  };

  /** @param {Number} i*/
  const handleDeleteAction = (i) => {
    deleteModalOpen = true;
    selectedBankIndex = i;
  };

  const handleDeleteModalOnOpenChange = () => {
    deleteModalOpen = false;
  };

  const checkIsValidInput = () => {
    if (bank.currency?.label === "") {
      toast.error("Please select a currency.");
      return false;
    }
    return true;
  };

  const submit = () => {
    if (isLoading) return;
    if (!checkIsValidInput()) return;
    let payload = { ...bank };
    payload.currency = payload.currency.value;
    isLoading = true;
    toast.loading("Saving...");
    const api = isModalEdit ? "bank/edit" : "bank/add";
    createPostRequest(api, payload, (res) => {
      toast.dismiss();
      toast.success("Setting Saved");
      updateStore(res.data.data);
      setDefaultBank(res.data.data);
      resetBank();
      isModalOpen = false;
      isLoading = false;
    }).catch((err) => {
      toast.dismiss();
      isLoading = false;
      isModalOpen = false;
      handleError(err, "Failed to save settings");
    });
  };

  const deleteBank = () => {
    isLoading = true;
    const id = $banks[selectedBankIndex].id;
    toast.promise(createPostRequest("bank/delete", { id }), {
      loading: "Saving...",
      success: () => {
        const deletedBank = $banks.find((_, i) => i === selectedBankIndex);
        $banks = $banks.filter((_, i) => i !== selectedBankIndex);
        // update default bank if the default is deleted
        if (deletedBank.id === $defaultBank && $banks.length > 0) {
          saveDefaultBank($banks[0].id);
        } else if (deletedBank.id === $defaultBank && $banks.length === 0) {
          saveDefaultBank(0);
        }
        resetBank();
        isModalOpen = false;
        isLoading = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        isModalOpen = false;
        return handleError(err, "Failed to save settings", false);
      },
    });
    deleteModalOpen = false;
  };

  /** @param {number} id*/
  const saveDefaultBank = (id) => {
    isLoadingDefaultButton = true;
    toast.promise(createPostRequest("settings/update?tab=payment", { defaultBank: id }), {
      loading: "Saving...",
      success: () => {
        $defaultBank = id;
        isLoadingDefaultButton = false;
        return "Default bank updated";
      },
      error: (err) => {
        isLoadingDefaultButton = false;
        return handleError(err, "Failed to save default bank", false);
      },
    });
  };
</script>

{#if isLoading}
  <div class="mt-5 mb-10 mx-8">
    {#each $banks as _}
      <Skeleton class="h-[20px] my-8 rounded-full" />
    {/each}
  </div>
{:else}
  <CardContent class="md:px-6 md:pb-6 p-0">
    {#if isEmptyCheck($banks) || $banks.length === 0}
      <Separator />
      <div class="italic text-sm text-muted-foreground mt-4 mb-8">You have no bank account saved.</div>
    {:else}
      <div class="bg-secondary p-4 rounded-lg">
        <Table class="w-full bg-white rounded-lg md:text-sm text-xs">
          <Caption>List of banks used on your invoices.</Caption>
          <Header class="border-b-secondary border-b-4">
            <Row>
              <Head class="text-center">Name</Head>
              <Head class="text-center">Type</Head>
              <Head class="text-center">Currency</Head>
              <Head class="text-center">Detail</Head>
              <Head class="text-center">Actions</Head>
            </Row>
          </Header>
          <Body>
            {#if !isEmptyCheck($banks)}
              {#each $banks as _bank, i (i)}
                <Row class="border-b-0">
                  <Cell class="font-medium truncate text-center">
                    {_bank.name}
                  </Cell>
                  <Cell class="text-center">
                    {_bank.type ? _bank.type : "-"}
                  </Cell>
                  <Cell class="text-center">
                    {_bank.currency?.name}
                  </Cell>
                  <Cell class="text-start">
                    <MultilineText text={_bank.detail} />
                  </Cell>
                  <Cell>
                    <div class="flex justify-end space-x-1">
                      {#if _bank.id === $defaultBank}
                        <!-- Default button -->
                        <Button variant="outline" class="text-green-600 text-xs" disabled>
                          <Check class="h-4 w-4 mr-1" />
                          Default
                        </Button>
                      {:else}
                        <!-- Set default button -->
                        <Button
                          variant="outline"
                          class="text-black text-xs"
                          disabled={isLoadingDefaultButton}
                          on:click={() => saveDefaultBank(_bank.id)}>
                          Set as default
                        </Button>
                      {/if}
                      <EditButton on:click={() => handleEditAction(i)} />
                      <DeleteButton on:click={() => handleDeleteAction(i)} />
                    </div>
                  </Cell>
                </Row>
              {/each}
            {/if}
          </Body>
        </Table>
      </div>
    {/if}
  </CardContent>

  <CardFooter class="flex mb-2">
    {#if !$isPro && $banks.length >= 3}
      <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} customText="Upgrade to Pro to Add More Bank Accounts" />
    {:else}
      <Button on:click={handleAddButton}>
        <Plus class="h-4" />
        Bank Account
      </Button>
    {/if}
  </CardFooter>
{/if}

<!-- Modal -->
<Dialog bind:open={isModalOpen} onOpenChange={resetBank}>
  <DialogContent class="sm:max-w-lg">
    <DialogHeader>
      <DialogTitle>Payment Account</DialogTitle>
      <DialogDescription>Manage your bank account for receiving payment.</DialogDescription>
    </DialogHeader>
    <form class="space-y-4" on:submit|preventDefault={submit}>
      <div class="space-y-2">
        <Label for="name">Name <MiniStar /></Label>
        <Input type="text" id="name" required bind:value={bank.name} placeholder="Bank name" />
      </div>
      <div class="space-y-2">
        <Label for="type">Type</Label>
        <Input type="text" id="type" bind:value={bank.type} placeholder="Bank account type" />
      </div>
      <div class="space-y-2">
        <Label for="bank-currency-select">Currency <MiniStar /></Label>
        <Select bind:selected={bank.currency} required>
          <SelectTrigger id="bank-currency-select">
            <SelectValue placeholder="Select currency" />
          </SelectTrigger>
          <SelectContent class="max-h-60 overflow-y-auto">
            {#each $currencies as currency}
              <SelectItem value={currency} label={currency.name}>{currency.name}</SelectItem>
            {/each}
          </SelectContent>
        </Select>
      </div>
      <div class="space-y-2">
        <Label for="detail">Detail <MiniStar /></Label>
        <Textarea
          id="detail"
          required
          bind:value={bank.detail}
          rows={5}
          placeholder="Bank account number and other information here" />
      </div>
      <Button disabled={isLoading} type="submit">
        {#if isLoading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Saving
        {:else}
          Save
        {/if}
      </Button>
    </form>
  </DialogContent>
</Dialog>

<AlertDialog open={deleteModalOpen} onOpenChange={handleDeleteModalOnOpenChange}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete the{" "}
        <b>{bank.name}</b> account.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel>Cancel</AlertDialogCancel>
      <Button variant="destructive" on:click={deleteBank}>Delete</Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
