<script>
  import { createTable, Subscribe, Render, createRender } from "svelte-headless-table";
  import { onMount } from "svelte";
  import { Label } from "$lib/components/ui/label";
  import { Button } from "$lib/components/ui/button";
  import { Frown as FrownIcon, Plus, ShoppingCart } from "lucide-svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { Alert, AlertTitle, AlertDescription } from "$lib/components/ui/alert";
  import { BookX } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { writable } from "svelte/store";
  import { isDebug } from "$lib/stores/settings-store";
  import * as Table from "$lib/components/ui/table";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import ProductModal from "$lib/components/invoice/product-modal.svelte";
  import TableActions from "$lib/components/tables/products/action-buttons.svelte";
  import Pagination from "$lib/components/custom-ui/Pagination.svelte";
  import IdCell from "$lib/components/invoice-list/cell/id-cell.svelte";
  import NameCell from "$lib/components/product/cell/name-cell.svelte";
  import DateCell from "$lib/components/product/cell/date-cell.svelte";
  import PriceCell from "$lib/components/product/cell/price-cell.svelte";
  import SearchBar from "$lib/components/custom-ui/SearchBar.svelte";
  import moment from "moment";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import { loadInvoiceSetting } from "$lib/helpers/settings";
  import { productListPage } from "$lib/stores/navigation";

  const nav = [{ name: "Product", link: "products" }];
  const api = "product/list?";
  let pagination = {
    page: 1,
    perPage: 10,
    totalItems: 0,
    totalPages: 1,
  };
  let products = writable([]);
  let product = {};
  let isModalOpen = false;
  let isLoading = false;
  // this is used to check if we already fetch init data. if so, then header won't be loading anymore
  let isFetched = false;
  let isSearching = false;
  let searchValue;
  let selectedCurrency = { label: null, value: null };
  let selectedPagination = { value: pagination?.perPage ?? 10, label: pagination?.perPage ?? 10 };

  $: hasNoProduct = $products.length < 1;

  const table = createTable(products);
  const columns = table.createColumns([
    table.column({
      header: "#",
      accessor: "id",
      cell: (item) => {
        const id = $products.findIndex((data) => data.id === item.value);
        const position = (pagination.page - 1) * pagination.perPage + id + 1;
        return createRender(IdCell, { id: position });
        // return createRender(IdCell, { id: item.value });
      },
      plugins: {
        resize: {
          initialWidth: 40,
          minWidth: 30,
          maxWidth: 50,
        },
      },
    }),
    table.column({
      header: "Name",
      accessor: "name",
      cell: (item) => {
        return createRender(NameCell, {
          name: item.value,
          description: item.row.original.description,
          isWc: item.row.original.is_wc_product,
        });
      },
    }),
    table.column({
      header: "Unit Price",
      accessor: "price",
      cell: (item) => {
        return createRender(PriceCell, { price: item.value, currencyName: item.row.original?.currency?.name });
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
        return createRender(TableActions, { id: item.value, isWc: item.row.original.is_wc_product })
          .on("open-modal", (event) => {
            openModal(event);
          })
          .on("update", () => {
            getProducts(pagination.page);
          });
      },
    }),
  ]);

  // Enable Plugin
  const { headerRows, pageRows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);

  const openModal = (event) => {
    isModalOpen = true;
    const arg = event?.detail;
    if (arg) {
      const [selectedProduct] = $products.filter((product) => product.id == arg.id);
      product = selectedProduct;
      selectedCurrency = { label: product?.currency?.name, value: product?.currency };
    } else {
      product = {};
    }
  };

  const handleSearchProduct = () => {
    if (!searchValue) {
      isSearching = false;
    } else {
      isSearching = true;
    }
    getProducts();
  };

  const getProducts = async (pageIndex) => {
    isLoading = true;
    const fullApi = isSearching
      ? `${api}&page=${pageIndex ?? 1}&per_page=${pagination.perPage}&search=${searchValue}`
      : `${api}&page=${pageIndex ?? 1}&per_page=${pagination.perPage}`;
    try {
      const response = await createGetRequest(fullApi);
      const { page, per_page, total_items, total_pages, items } = response.data;
      $isDebug && console.log(response.data);
      $products = items ?? $products;
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
      handleError(err, "Failed to fetch products");
    }
  };

  onMount(() => {
    getProducts($productListPage);
    
    try {
      loadInvoiceSetting();
    } catch (e) {
      handleError(e, "Failed to retrieve settings data");
    }
  });
</script>

<Breadcrumb to={nav} />
<PageTitle title="Products" description="Manage Your Products" hasDivider={false} />

<ProductModal
  bind:isOpen={isModalOpen}
  bind:product
  on:update={() => getProducts(pagination.page)}
  {selectedCurrency} />

<Alert class="flex sm:flex-row flex-col items-center justify-between mb-2 gap-y-2">
  {#if isLoading && !isFetched}
    <div class="flex flex-nowrap justify-between w-full">
      <Skeleton class="h-10 w-60" />
      <div class="flex flex-nowrap gap-x-6">
        <Skeleton class="h-10 w-52" />
        <Skeleton class="h-10 w-32" />
      </div>
    </div>
  {:else}
    {#if hasNoProduct && !isSearching && !isLoading}
      <div class="flex items-center">
        <FrownIcon class="mr-2 h-6 w-6 text-primary" />
        <div>
          <AlertTitle>There is no product yet</AlertTitle>
          <AlertDescription>You can create one or sync from Woocommerce</AlertDescription>
        </div>
      </div>
    {:else}
      <SearchBar
        isSearching={isLoading}
        handleSearch={handleSearchProduct}
        bind:searchQuery={searchValue}
        placeholder="Search products by name" />
    {/if}
    <div class="flex flex-row flex-nowrap justify-between gap-x-6 sm:w-fit w-full">
      <Button variant="default" class="sm:w-fit w-full" on:click={() => openModal()}>
        <Plus class="mr-1 h-4 w-4" />
        <span>Add product</span>
      </Button>
    </div>
  {/if}
</Alert>

<!-- Table content -->
{#if isLoading}
  <div class="space-y-3 rounded-md border p-3">
    {#each $products as _}
      <Skeleton class="h-12 w-full" />
    {:else}
      {#each [1, 2, 3, 4, 5] as _}
        <Skeleton class="h-12 w-full" />
      {/each}
    {/each}
  </div>
{:else if hasNoProduct && !isSearching}
  <div class="w-full flex flex-col flex-nowrap justify-center items-center mt-36 text-center gap-y-4">
    <ShoppingCart size={120} strokeWidth={1} class="mx-auto mb-0 text-primary" />
    <Label class="mx-auto font-normal text-2xl text-center">"You haven't added any product yet."</Label>
  </div>
{:else if hasNoProduct && isSearching}
  <div class="w-full flex flex-col flex-nowrap justify-center items-center mt-36 text-center gap-y-4">
    <BookX size={120} strokeWidth={1} class="mx-auto mb-0 text-gray-400" />
    <Label class="mx-auto font-normal text-2xl text-center text-gray-400">"Product not found"</Label>
  </div>
{:else if !hasNoProduct}
  <div class="rounded-md border bg-white">
    <Table.Root {...$tableAttrs}>
      <!-- Header -->
      <Table.Header>
        {#each $headerRows as headerRow}
          <Subscribe rowAttrs={headerRow.attrs()}>
            <Table.Row>
              {#each headerRow.cells as cell (cell.id)}
                <Subscribe attrs={cell.attrs()} let:attrs props={cell.props()} let:props>
                  <Table.Head {...attrs} class={cell.id !== "name" ? "text-center" : "text-start"}>
                    <Render of={cell.render()} />
                  </Table.Head>
                </Subscribe>
              {/each}
            </Table.Row>
          </Subscribe>
        {/each}
      </Table.Header>
      <!-- Body -->
      <Table.Body {...$tableBodyAttrs}>
        {#each $pageRows as row (row.id)}
          <Subscribe rowAttrs={row.attrs()} let:rowAttrs>
            <Table.Row {...rowAttrs} class="border-b-0">
              <!-- this is Table.Row attribute (look top) -->
              <!-- data-state={$selectedDataIds[row.id] && "selected"} -->
              {#each row.cells as cell (cell.id)}
                <Subscribe attrs={cell.attrs()} let:attrs>
                  <Table.Cell {...attrs} class={cell.id !== "name" ? "text-center" : "text-start font-medium"}>
                    <Render of={cell.render()} />
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

{#if !isLoading && !hasNoProduct}
  <Pagination
    name="product"
    bind:dataList={$products}
    listApi={api}
    bind:isLoading
    bind:pagination
    bind:selectedPagination
    {isSearching}
    {searchValue} />
{/if}
