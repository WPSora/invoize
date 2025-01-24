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
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Table, Row, Body, Cell, Head, Header, Caption } from "$lib/components/ui/table";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { Plus } from "radix-icons-svelte";
  import { Separator } from "$lib/components/ui/separator";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { isPro, isProPopupOpen, discounts, defaultCurrency } from "$lib/stores/settings-store";
  import { Loader2 } from "lucide-svelte";
  import { Button } from "$lib/components/ui/button";
  import { numberFormatter, currencyFormatter } from "$lib/helpers/decimalFormatter";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import DiscountModal from "$lib/components/invoice/add-discount-modal.svelte";

  let isEditState = false;
  let isDiscountModalOpen = false;
  let isAlertDialogOpen = false;
  let isLoading = false;
  let fromSetting = true;
  let selectedDiscountIndex;
  let payload;
  let submit;

  const handleNewDiscountButton = () => {
    isDiscountModalOpen = true;
    isEditState = false;
  };

  const handleEditButton = (discount, i) => {
    isDiscountModalOpen = true;
    isEditState = true;
    addEditValues(discount, i);
  };

  const handleDeleteButton = (i) => {
    isAlertDialogOpen = true;
    selectedDiscountIndex = i;
  };

  const handleDelete = () => {
    $discounts = $discounts.filter((_, i) => i !== selectedDiscountIndex);
    // add default value if no discount is saved, because it can't save null value
    if ($discounts.length === 0) {
      $discounts = [...emptyData];
    }
    selectedDiscountIndex = null;
    submit();
  };

  const addEditValues = (discount, i) => {
    payload = {
      id: i,
      name: discount.name,
      description: discount.description,
      value: discount.value,
      currency:
        discount.type === "fixed"
          ? { label: discount.currency.name, value: discount.currency }
          : { label: null, value: null },
      type:
        discount.type === "percent" ? { label: "Percent (%)", value: "percent" } : { label: "Fixed", value: "fixed" },
    };
  };
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0 flex flex-row justify-between">
    <div>
      <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Discount</CardTitle>
      <CardDescription class="ml-3 md:text-sm text-xs">Manage discount options for your invoices.</CardDescription>
    </div>
    <div class="flex flex-nowrap mt-4">
      {#if !$isPro && $discounts.length >= 3}
        <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} customText="Upgrade to Pro to Add More Discounts" />
      {:else}
        <Button
          variant="default"
          class="hover:shadow-lg hover:shadow-primary/80 transition-all w-full sm:w-fit"
          on:click={handleNewDiscountButton}>
          <Plus />
          <div class="ml-1">New Discount</div>
        </Button>
      {/if}
    </div>
  </CardHeader>
  {#if isLoading}
    <div class="mt-5 mb-10 mx-8">
      {#each $discounts as _}
        <Skeleton class="h-[20px] my-8 rounded-full" />
      {/each}
    </div>
  {:else}
    <CardContent class="md:px-6 md:pb-6 p-0">
      <!-- if first value name is none, it means it has no saved discount -->
      {#if !isEmptyCheck($discounts)}
        <div class="bg-secondary p-4 rounded-lg">
          <Table class="bg-white rounded-lg md:text-sm text-xs">
            <Caption class="md:text-sm text-xs">List of discounts used on your invoice.</Caption>
            <Header class="border-b-secondary border-b-4">
              <Row>
                <Head class="min-w-[100px]">Name</Head>
                <Head class="text-center">Description</Head>
                <Head class="text-center min-w-[100px]">Value</Head>
                <Head class="text-center">Actions</Head>
              </Row>
            </Header>
            <Body>
              {#each $discounts as discount, i}
                <Row class="border-b-0">
                  <Cell>
                    <div>{discount.name}</div>
                  </Cell>
                  <Cell class="text-center">
                    <div>{discount.description ?? ""}</div>
                  </Cell>
                  <Cell class="text-center">
                    {#if discount.type === "fixed"}
                      {currencyFormatter(discount.currency.name, discount.value)}
                    {:else}
                      {numberFormatter($defaultCurrency.name, discount.value)} %
                    {/if}
                  </Cell>
                  <Cell class="text-center md:space-y-0 space-y-1">
                    <EditButton on:click={() => handleEditButton(discount, i)} />
                    <DeleteButton on:click={() => handleDeleteButton(i)} />
                  </Cell>
                </Row>
              {/each}
            </Body>
          </Table>
        </div>
      {:else}
        <Separator />
        <div class="italic text-sm text-muted-foreground mt-4 mb-8">
          You have no default discount configuration saved.
        </div>
      {/if}
    </CardContent>
  {/if}
</Card>

<!-- Create and edit modal -->
<DiscountModal
  {isEditState}
  bind:isLoading
  bind:payload
  bind:submit
  bind:fromSetting
  bind:isOpen={isDiscountModalOpen}
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
      <AlertDialogCancel disabled={isLoading} on:click={() => (selectedDiscountIndex = null)}>Cancel</AlertDialogCancel>
      <Button variant="destructive" on:click={handleDelete} disabled={isLoading}>
        {#if isLoading}
          <Loader2 class="h-4 w-4 animate-spin mr-1 text-white" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
