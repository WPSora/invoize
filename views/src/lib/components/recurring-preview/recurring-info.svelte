<script>
  import { Button } from "$lib/components/ui/button";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import moment from "moment";

  export let recurring;
</script>

<div class="text-xs border bg-muted rounded-md p-3 space-y-2">
  <div class="font-semibold text-sm">{recurring.name}</div>
  <div class="flex items-center justify-between">
    <div>Start</div>
    <div>{moment(recurring.start).format(invoize.date_format)}</div>
  </div>
  <div class="flex items-center justify-between">
    <div>End</div>
    <div>{recurring.end === "never" ? capitalizeFirstLetter(recurring.end) : recurring.end}</div>
  </div>
  <div class="flex items-center justify-between">
    <div>Schedule</div>
    <div class="text-wrap w-8/12 text-right">
      {capitalizeFirstLetter(recurring.interval)}
    </div>
  </div>
  <div class="flex items-center justify-between">
    <div>Next Invoice</div>
    <div>{moment(recurring.nextInvoiceDate).format(invoize.date_format)}</div>
  </div>
  <div class="flex items-center justify-between">
    <div>Last Invoice</div>
    {#if recurring.lastInvoice}
      <Button
        variant="link"
        on:click={() => (location.href = `#/invoice/${recurring.lastInvoice.token}`)}
        class="p-0 h-fit">
        {recurring.lastInvoice.name}
      </Button>
    {:else}
      <div>-</div>
    {/if}
  </div>
</div>
