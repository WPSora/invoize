<script>
  import { Card, CardContent, CardFooter, CardHeader } from "$lib/components/ui/card";
  import { Table, Row, Body, Cell, Head, Header } from "$lib/components/ui/table";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { Separator } from "$lib/components/ui/separator";
</script>

<Card class="md:w-[800px]">
  <CardHeader class="flex flex-row justify-between sm:mx-10 sm:mt-10">
    <!-- Left side Header -->
    <!-- Logo -->
    <div class="flex text-muted-foreground rounded-lg">
      <Skeleton class="w-40 h-20 rounded-md" />
    </div>
    <!-- Right side Header -->
    <div class="flex flex-col items-end space-y-2">
      <Skeleton class="w-44 h-10 rounded-md" />
      <Skeleton class="w-20 h-6 rounded-md" />
    </div>
  </CardHeader>

  <Separator class="w-10/12 mx-auto h-[1px] bg-slate-200 my-2" />

  <CardContent class="md:mx-10 px-0 sm:p-6 space-y-16">
    <!-- Content Top -->
    <div class="flex flex-nowrap justify-between">
      <!-- Content Top-Left -->
      <div class="w-1/2 space-y-6">
        <!-- Issued by -->
        <div class="space-y-2">
          <div class="font-medium">Issued by</div>
          <Skeleton class="h-6 w-28" />
          <Skeleton class="h-4 w-40" />
          <Skeleton class="h-4 w-36" />
          <Skeleton class="h-4 w-32" />
        </div>
        <!-- Customer -->
        <div class="space-y-2">
          <div class="font-medium">Customer</div>
          <Skeleton class="h-6 w-28" />
          <Skeleton class="h-4 w-40" />
          <Skeleton class="h-4 w-36" />
          <Skeleton class="h-4 w-32" />
        </div>
      </div>

      <!-- Content Top-Right -->
      <div class="w-1/2 flex items-center justify-end text-sm">
        <div class="space-y-8">
          <!-- Status & Type -->
          <div class="flex flex-row flex-nowrap">
            <div class="w-32 font-medium space-y-1">
              <div>Status</div>
              <div>Type</div>
            </div>
            <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
              <div>:</div>
              <div>:</div>
            </div>
            <div class="flex flex-col items-end w-32 space-y-1">
              <Skeleton class="h-5 w-32" />
              <Skeleton class="h-5 w-32" />
            </div>
          </div>

          <!-- Date -->
          <div class="flex flex-row flex-nowrap">
            <div class="w-32 font-medium space-y-1">
              <div>Order date</div>
              <div>Invoice date</div>
              <div>Due date</div>
            </div>
            <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
              <div>:</div>
              <div>:</div>
              <div>:</div>
            </div>
            <div class="flex flex-col flex-nowrap items-end w-32 space-y-1">
              <Skeleton class="h-5 w-32" />
              <Skeleton class="h-5 w-32" />
              <Skeleton class="h-5 w-32" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Middle -->
    <Table>
      <Header>
        <Row class="grid grid-cols-12">
          <Head class="col-span-1">#</Head>
          <Head class="col-span-6 text-center">Products</Head>
          <Head class="col-span-2 text-center">Unit Price</Head>
          <Head class="col-span-1 text-center">Quantity</Head>
          <Head class="col-span-2 text-center">Amount</Head>
        </Row>
      </Header>
      <Body>
        <Row class="grid grid-cols-12">
          <Cell class="col-span-1">
            <Skeleton class="h-6" />
          </Cell>
          <Cell class="col-span-6 text-center">
            <Skeleton class="h-6" />
          </Cell>
          <Cell class="col-span-2 text-center">
            <Skeleton class="h-6" />
          </Cell>
          <Cell class="col-span-1 text-center">
            <Skeleton class="h-6" />
          </Cell>
          <Cell class="col-span-2 text-center">
            <Skeleton class="h-6" />
          </Cell>
        </Row>
      </Body>
    </Table>

    <!-- Content Bottom -->
    <div class="flex flex-row flex-nowrap justify-between items-center">
      <!-- Content Bottom-Left -->
      <div class="w-1/2 flex flex-col gap-y-2">
        <div class="font-medium mb-1">Payment Method</div>
        <Skeleton class="h-5 w-24" />
        <Skeleton class="h-5 w-32" />
        <Skeleton class="h-5 w-28" />
      </div>
      <!-- Content Bottom-Right -->
      <div class="w-1/2 flex flex-col items-end gap-y-4">
        <Skeleton class="h-6 w-60" />
        <Skeleton class="h-6 w-60" />
      </div>
    </div>
  </CardContent>

  <!-- Note & TC -->
  <CardFooter class="sm:mx-10 my-10 flex flex-col items-start font-medium gap-y-8">
    <!-- Note -->
    <div class="w-full space-y-2">
      <div>Note</div>
      <Skeleton class="h-10 w-full" />
    </div>
    <!-- TC -->
    <div class="flex flex-nowrap justify-between items-end w-full">
      <div class="w-8/12 flex flex-col gap-y-2">
        <div>Terms & Conditions</div>
        <Skeleton class="h-20 w-full" />
      </div>
      <Skeleton class="h-24 w-24" />
    </div>
  </CardFooter>
</Card>
