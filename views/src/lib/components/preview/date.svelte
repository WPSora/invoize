<script>
  import moment from "moment";

  export let isPaid;
  export let orderDate;
  export let invoiceDate;
  export let dueDate;
  export let paidDate;

  const formatDate = (date) => {
    return moment(date).format(invoize.date_format);
  };
</script>

<div class="flex flex-row flex-nowrap">
  <div class="w-32 font-medium space-y-1">
    <div>Order date</div>
    <div>Invoice date</div>
    <div>Due date</div>
    {#if isPaid}
      <div>Paid on</div>
    {/if}
  </div>
  <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
    <div>:</div>
    <div>:</div>
    <div>:</div>
    {#if isPaid}
      <div>:</div>
    {/if}
  </div>
  <div class="flex flex-col flex-nowrap items-end w-32 space-y-1">
    <div>{orderDate ? formatDate(orderDate) : "-"}</div>
    <div>{invoiceDate ? formatDate(invoiceDate) : "-"}</div>
    <div>{dueDate ? formatDate(dueDate) : "-"}</div>
    {#if isPaid}
      <div>{paidDate ? formatDate(paidDate) : "-"}</div>
    {/if}
  </div>
</div>
