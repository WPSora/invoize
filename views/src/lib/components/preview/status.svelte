<script>
  export let isPaid = false;
  export let paymentStatus = "UNPAID";
  export let isRecurring;
</script>

<div class="flex flex-row flex-nowrap">
  <div class="w-32 font-medium space-y-1">
    <div>Status</div>
    <div>Type</div>
  </div>
  <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
    <div>:</div>
    <div>:</div>
  </div>
  <div class="flex flex-col items-end w-32 space-y-1 font-semibold">
    <div id="invoice-status" class="w-24 rounded-md text-center text-white {isPaid ? 'bg-green-600' : 'bg-destructive'}">
      {paymentStatus?.toUpperCase()}
    </div>
    <div
      class="rounded-md w-24 font-medium text-center print:mt-0 print:pt-0 {isRecurring
        ? 'text-blue-500'
        : 'text-amber-500'} ">
      {isRecurring ? "Recurring" : "One-time"}
    </div>
  </div>
</div>
