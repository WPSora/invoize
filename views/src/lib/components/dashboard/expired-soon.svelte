<script>
  import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "$lib/components/ui/card";
  import { Separator } from "$lib/components/ui/separator";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { Button } from "$lib/components/ui/button";
  import { ChevronDown } from "lucide-svelte";
  import moment from "moment";

  export let isLoading;
  export let date;
  export let isRecurring = false;
</script>

<Card>
  <CardHeader class="pb-2">
    <CardTitle class="text-primary sm:text-xl text-lg">
      {#if isRecurring}
        Upcoming Invoice
      {:else}
        Expired Soon
      {/if}
    </CardTitle>
    <Separator />
  </CardHeader>
  <CardContent class="space-y-4 sm:text-sm pb-4 text-xs">
    {#if isLoading}
      <div class="flex flex-col gap-y-3 justify-center">
        <Skeleton class="h-6 w-full" />
        <Skeleton class="h-6 w-full" />
        <Skeleton class="h-6 w-full" />
      </div>
    {:else}
      {#each date as invoice}
        {@const dueInDays = moment(invoice.dueDate).from(moment().subtract(1, "days"))}
        {@const dueInDate = moment(invoice.dueDate).format(invoize.date_format)}
        <div class="flex h-fit sm:flex-row flex-col sm:justify-between sm:items-center items-start gap-x-4">
          <Button
            variant="link"
            class="flex justify-start h-full md:w-8/12 w-full text-black hover:text-primary active:text-primary decoration-primary p-0 font-normal sm:text-sm text-xs"
            on:click={() =>
              (location.href = isRecurring
                ? `#/recurring/${invoice.customerId}/${invoice.token}`
                : `#/invoice/${invoice.token}`)}>
            <div class="flex text-wrap text-start w-full">
              {invoice.name}
            </div>
          </Button>
          <div class="text-end text-nowrap">
            <span class="text-red-600">{dueInDays}</span> ({dueInDate})
          </div>
        </div>
      {:else}
        <div class="text-muted-foreground text-center italic flex justify-center items-center h-12">
          {#if isRecurring}
            No upcoming invoice
          {:else}
            No invoice expired soon
          {/if}
        </div>
      {/each}
      {#if !isRecurring && date.length > 0}
        <div class="w-full flex justify-center mt-2">
          <Button
            variant="link"
            class="text-primary sm:text-sm text-xs"
            on:click={() => (location.href = "#/invoices")}>
            View All
            <ChevronDown class="ml-1 w-4 h-4" />
          </Button>
        </div>
      {/if}
    {/if}
  </CardContent>
</Card>
