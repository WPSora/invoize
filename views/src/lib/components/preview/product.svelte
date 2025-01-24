<script>
  import { Table, Row, Body, Cell, Head, Header } from "$lib/components/ui/table";
  import { currencyFormatter, numberFormatter } from "$lib/helpers/decimalFormatter";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  export let currency = { name: "USD", symbol: "$" };
  export let products = [];
</script>

<Table class="my-5">
  <Header>
    <Row class="grid grid-cols-12 bg-slate-100 rounded-t-lg">
      <Head class="flex items-center col-span-1 font-semibold">#</Head>
      <Head class="flex items-center col-span-6 font-semibold">Products</Head>
      <Head class="flex items-center justify-center col-span-2 font-semibold">Unit Price</Head>
      <Head class="flex items-center justify-center col-span-1 font-semibold">Qty</Head>
      <Head class="flex items-center justify-end col-span-2 font-semibold">Amount</Head>
    </Row>
  </Header>
  <Body class="border-b-2">
    {#each products as product, i}
      <Row class="border-0 grid grid-cols-12 items-center">
        <Cell class="col-span-1 font-medium">{i + 1}</Cell>
        <Cell class="col-span-6">
          <div>{product.name ?? ""}</div>
          <MultilineText text={product.note ?? product.description} class="text-muted-foreground text-xs" isShowEmpty={true} />
        </Cell>
        <Cell class="col-span-2 text-center">{currencyFormatter(currency.name, product.unitPrice) ?? ""}</Cell>
        <Cell class="col-span-1 text-center">{numberFormatter(currency.name, product.quantity) ?? ""}</Cell>
        <Cell class="col-span-2 text-right">{currencyFormatter(currency.name, product.amount) ?? ""}</Cell>
      </Row>
    {/each}
  </Body>
</Table>
