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
  import { Separator } from "$lib/components/ui/separator";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent, Title } from "$lib/components/ui/card";
  import { Table, Row, Body, Cell, Head, Header, Caption } from "$lib/components/ui/table";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { Plus } from "radix-icons-svelte";
  import { taxes, defaultCurrency } from "$lib/stores/settings-store";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { isPro, isProPopupOpen } from "$lib/stores/settings-store";
  import { Loader2 } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { numberFormatter, currencyFormatter } from "$lib/helpers/decimalFormatter";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import TaxModal from "$lib/components/invoice/add-tax-modal.svelte";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";
  import * as Alert from "$lib/components/ui/alert";

  let isEditState = false;
  let isTaxModalOpen = false;
  let isAlertDialogOpen = false;
  let isLoading = false;
  let selectedTaxIndex;
  let payload;
  let submit;

  const handleNewTaxButton = () => {
    isTaxModalOpen = true;
    isEditState = false;
  };

  const handleEditButton = (tax, i) => {
    isTaxModalOpen = true;
    isEditState = true;
    addEditValues(tax, i);
  };

  const handleDeleteButton = (i) => {
    isAlertDialogOpen = true;
    selectedTaxIndex = i;
  };

  const handleDelete = () => {
    $taxes = $taxes.filter((_, i) => i !== selectedTaxIndex);
    // add default value if no tax is saved, because it can't save null value
    if ($taxes.length === 0) {
      $taxes = [...emptyData];
    }
    selectedTaxIndex = null;
    submit();
  };

  const addEditValues = (tax, i) => {
    payload = {
      id: i,
      name: tax.name,
      description: tax.description,
      value: tax.value,
      currency: tax.type === "fixed" ? { label: tax.currency.name, value: tax.currency } : { label: null, value: null },
      type: tax.type === "percent" ? { label: "Percent (%)", value: "percent" } : { label: "Fixed", value: "fixed" },
    };
  };
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0 flex flex-row justify-between">
    <div>
      <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Tax</CardTitle>
      <CardDescription class="ml-3 md:text-sm text-xs">Manage tax options for your invoices.</CardDescription>
    </div>
    <div class="flex flex-nowrap mt-4">
      {#if !$isPro && $taxes.length >= 3}
        <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} customText="Upgrade to Pro to Add More Taxes" />
      {:else}
        <Button
          variant="default"
          class="hover:shadow-lg hover:shadow-primary/80 transition-all w-full sm:w-fit"
          on:click={handleNewTaxButton}>
          <Plus />
          <div class="ml-1">New Tax</div>
        </Button>
      {/if}
    </div>
  </CardHeader>
  {#if isLoading}
    <div class="mt-5 mb-10 mx-8">
      {#each $taxes as _}
        <Skeleton class="h-[20px] my-8 rounded-full" />
      {/each}
    </div>
  {:else}
    <CardContent class="md:px-6 md:pb-6 p-0">
      {#if !isEmptyCheck($taxes)}
        <div class="bg-secondary p-4 rounded-lg">
          <Table class="bg-white rounded-lg md:text-sm text-xs">
            <Caption class="md:text-sm text-xs">List of taxes used on your invoice.</Caption>
            <Header class="border-b-secondary border-b-4">
              <Row>
                <Head class="min-w-[100px]">Name</Head>
                <Head class="text-center">Description</Head>
                <Head class="text-center min-w-[100px]">Value</Head>
                <Head class="text-center">Actions</Head>
              </Row>
            </Header>
            <Body>
              {#each $taxes as tax, i}
                <Row class="border-b-0">
                  <Cell>
                    <div>{tax.name}</div>
                  </Cell>
                  <Cell class="text-center">
                    <div>{tax.description ?? ""}</div>
                  </Cell>
                  <Cell class="text-center">
                    {#if tax.type === "fixed"}
                      {currencyFormatter(tax.currency.name, tax.value)}
                    {:else}
                      {numberFormatter($defaultCurrency.name, tax.value)} %
                    {/if}
                  </Cell>
                  <Cell class="text-center md:space-y-0 space-y-1">
                    <EditButton on:click={() => handleEditButton(tax, i)} />
                    <DeleteButton on:click={() => handleDeleteButton(i)} />
                  </Cell>
                </Row>
              {/each}
            </Body>
          </Table>
        </div>
      {:else}
        <Separator />
        <div class="italic text-sm text-muted-foreground mt-4 mb-8">You have no default tax configuration saved.</div>
      {/if}
    </CardContent>
  {/if}
</Card>

<!-- Create and edit modal -->
<TaxModal
  {isEditState}
  bind:isLoading
  bind:payload
  bind:submit
  bind:isOpen={isTaxModalOpen}
  bind:isDeleteModalOpen={isAlertDialogOpen} />

<!-- Delete modal -->
<AlertDialog bind:open={isAlertDialogOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel disabled={isLoading} on:click={() => (selectedTaxIndex = null)}>Cancel</AlertDialogCancel>
      <Button variant="destructive" on:click={handleDelete} disabled={isLoading}>
        {#if isLoading}
          <Loader2 class="h-4 w-4 mr-1 text-white animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
