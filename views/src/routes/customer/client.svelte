<script>
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import * as Table from "$lib/components/ui/table";
  import { BookX, Loader2, Plus, Users2 } from "lucide-svelte";
  import { createTable, Subscribe, Render, createRender } from "svelte-headless-table";
  import { Alert, AlertDescription, AlertTitle } from "$lib/components/ui/alert";
  import { Button } from "$lib/components/ui/button";
  import { Frown } from "lucide-svelte";
  import { onMount } from "svelte";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { clients, client, defaultClient } from "$lib/stores/invoice-store";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import CustomerModal from "$lib/components/invoice/customer-modal.svelte";
  import Pagination from "$lib/components/custom-ui/Pagination.svelte";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import TableActions from "$lib/components/tables/client/table-actions.svelte";
  import IdCell from "$lib/components/invoice-list/cell/id-cell.svelte";
  import NameCell from "$lib/components/client/cell/name-cell.svelte";
  import EmailCell from "$lib/components/client/cell/email-cell.svelte";
  import DateCell from "$lib/components/client/cell/date-cell.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import toast from "svelte-french-toast";
  import moment from "moment";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import { customerListPage } from "$lib/stores/navigation";

  $: hasNoClient = $clients.length < 1;

  const nav = [{ name: "Customer", link: "customers" }];

  let pagination = {
    page: 1,
    perPage: 10,
    totalItems: 0,
    totalPages: 1,
  };
  let selectedPagination = { value: pagination?.perPage ?? 10, label: pagination?.perPage ?? 10 };
  let isModalOpen = false;
  let isDeleteModalOpen = false;
  let isFetched = false;
  let isLoading = true;
  let isEditing = false;
  let isSearching = false;
  let isDeleteWpUser = false;
  let selectedId;
  let searchValue;

  const table = createTable(clients);
  const columns = table.createColumns([
    table.column({
      header: "#",
      accessor: "id",
      cell: (item) => {
        const id = $clients.findIndex((data) => data.id === item.value);
        const position = (pagination.page - 1) * pagination.perPage + id + 1;
        return createRender(IdCell, { id: position });
        // return createRender(IdCell, { id: item.value });
      },
    }),
    table.column({
      header: "Name",
      accessor: "name",
      cell: (item) => {
        return createRender(NameCell, { name: item.value });
      },
    }),
    table.column({
      header: "Email",
      accessor: "email",
      cell: (item) => {
        return createRender(EmailCell, { email: item.value });
      },
    }),
    table.column({
      header: "Date Created",
      accessor: "created_at",
      cell: (item) => {
        return createRender(DateCell, { date: moment(item.value).format(invoize.date_format) });
      },
    }),

    table.column({
      header: "Actions",
      accessor: ({ id }) => id,
      cell: (item) => {
        return createRender(TableActions, { id: item.value })
          .on("delete", (event) => {
            selectedId = event.detail.selectedId;
            isDeleteModalOpen = event.detail.isDeleteModalOpen;
          })
          .on("edit", (event) => {
            isModalOpen = true;
            isEditing = true;
          });
      },
    }),
  ]);

  // Enable Plugin
  const { headerRows, pageRows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);

  const handleAdd = () => {
    isModalOpen = true;
    isEditing = false;
    $client = { ...$defaultClient };
  };

  const handleSearchClient = () => {
    if (!searchValue) {
      isSearching = false;
    } else {
      isSearching = true;
    }
    getClient();
  };

  const getClient = async () => {
    try {
      isLoading = true;
      const api = isSearching 
        ? `client/list?search=${searchValue}&per_page=${pagination.perPage}`
        : `client/list?page=${$customerListPage}`;
      const response = await createGetRequest(api);
      const { page, per_page, total_items, total_pages, items } = response.data;
      $clients = items;
      pagination = {
        page,
        perPage: per_page,
        totalItems: total_items,
        totalPages: total_pages,
      };
      isLoading = false;
      isFetched = true;
    } catch (err) {
      isLoading = false;
      handleError(err, "Failed to fetch client data");
    }
  };

  const deleteClient = async (id) => {
    isLoading = true;
    toast.promise(createPostRequest("client/delete", { id, isDeleteWpUser }), {
      loading: "Deleting...",
      success: () => {
        // update client store
        $clients = $clients.filter((c) => c.id !== id);
        isLoading = false;
        isDeleteModalOpen = false;
        selectedId = null;
        return "Client data deleted";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to delete client data.");
      },
    });
  };

  onMount(() => {
    getClient();
  });
</script>

<Breadcrumb to={nav} />
<PageTitle title="Customers" description="Manage Your Customers" hasDivider={false} />

<Alert class="flex sm:flex-row flex-col justify-between items-center mb-2 gap-y-2">
  {#if isLoading && !isFetched}
    <div class="flex flex-nowrap justify-between w-full">
      <Skeleton class="h-10 w-60" />
      <div class="flex flex-nowrap gap-x-6">
        <Skeleton class="h-10 w-52" />
        <Skeleton class="h-10 w-32" />
      </div>
    </div>
  {:else}
    {#if hasNoClient && !isSearching && !isLoading}
      <div class="flex flex-nowrap items-center gap-x-2">
        <Frown class="h-6 w-6" />
        <div>
          <AlertTitle>There is no customer yet</AlertTitle>
          <AlertDescription>Let's create your first customer</AlertDescription>
        </div>
      </div>
    {:else}
      <!-- Search input -->
      <SearchBar
        isSearching={isLoading}
        handleSearch={handleSearchClient}
        bind:searchQuery={searchValue}
        placeholder="Search customers by name" />
    {/if}
    <div class="flex flex-row flex-nowrap justify-between gap-x-6 sm:w-fit w-full">
      <Button variant="default" class="sm:w-fit w-full" on:click={() => handleAdd()}>
        <Plus class="mr-1 h-4 w-4" />
        <span>Add customer</span>
      </Button>
    </div>
  {/if}
</Alert>

{#if isLoading}
  <div class="space-y-3 rounded-md border p-3">
    {#each $clients as _}
      <Skeleton class="h-12 w-full" />
    {:else}
      {#each [1, 2, 3, 4, 5] as _}
        <Skeleton class="h-12 w-full" />
      {/each}
    {/each}
  </div>
{:else if hasNoClient && !isSearching}
  <div class="w-full flex flex-col flex-nowrap justify-center items-center mt-36 text-center">
    <Users2 size={120} strokeWidth={1} class="mx-auto mb-0 text-primary" />
    <Label class="text-center font-normal text-2xl">"You haven't added any customer yet."</Label>
  </div>
{:else if hasNoClient && isSearching}
  <div class="w-full flex flex-col flex-nowrap justify-center items-center mt-36 text-center gap-y-4">
    <BookX size={120} strokeWidth={1} class="mx-auto mb-0 text-gray-400" />
    <Label class="mx-auto font-normal text-2xl text-center text-gray-400">"Customer not found"</Label>
  </div>
{:else if !hasNoClient}
  <div class="rounded-md border bg-white">
    <Table.Root {...$tableAttrs}>
      <Table.Header>
        {#each $headerRows as headerRow}
          <Subscribe rowAttrs={headerRow.attrs()}>
            <Table.Row>
              {#each headerRow.cells as cell (cell.id)}
                <Subscribe attrs={cell.attrs()} let:attrs props={cell.props()} let:props>
                  <Table.Head {...attrs} class="[&:has([role=checkbox])]:pl-3">
                    {#if cell.id === "name"}
                      <div class="font-medium">
                        <Render of={cell.render()} />
                      </div>
                    {:else}
                      <div class="text-center">
                        <Render of={cell.render()} />
                      </div>
                    {/if}
                  </Table.Head>
                </Subscribe>
              {/each}
            </Table.Row>
          </Subscribe>
        {/each}
      </Table.Header>
      <Table.Body {...$tableBodyAttrs}>
        {#each $pageRows as row (row.id)}
          <Subscribe rowAttrs={row.attrs()} let:rowAttrs>
            <Table.Row {...rowAttrs} class="border-b-0">
              {#each row.cells as cell (cell.id)}
                <Subscribe attrs={cell.attrs()} let:attrs>
                  <Table.Cell {...attrs}>
                    {#if cell.id === "name"}
                      <div class="font-medium">
                        <Render of={cell.render()} />
                      </div>
                    {:else}
                      <div class="text-center">
                        <Render of={cell.render()} />
                      </div>
                    {/if}
                  </Table.Cell>
                </Subscribe>
              {/each}
            </Table.Row>
          </Subscribe>
        {/each}
      </Table.Body>
    </Table.Root>
  </div>
{/if}

{#if !isLoading && !hasNoClient}
  <Pagination
    name="customer"
    bind:dataList={$clients}
    listApi="client/list?"
    bind:isLoading
    bind:pagination
    bind:selectedPagination
    {isSearching}
    {searchValue} />
{/if}

<!-- Add or Edit client modal -->
<CustomerModal bind:isOpen={isModalOpen} {isEditing} />

<!-- Delete modal -->
<AlertDialog
  closeOnOutsideClick={true}
  bind:open={isDeleteModalOpen}
  onOpenChange={() => {
    selectedId = null;
    isDeleteWpUser = false;
  }}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialogDescription>
      <div class="flex flex-row flex-nowrap items-center gap-x-2 py-2">
        <Checkbox id="send-email" bind:checked={isDeleteWpUser} />
        <Label for="send-email">Also delete wordpress User account</Label>
      </div>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel on:click={() => {}}>Cancel</AlertDialogCancel>
      <Button variant="destructive" disabled={isLoading} on:click={() => deleteClient(selectedId)}>
        {#if isLoading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
