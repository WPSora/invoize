<script>
  import moment from "moment";

  export let nextInvoiceDate;
</script>

<div class="flex flex-col justify-center items-center">
  <div class="font-medium">Next Invoice:</div>
  <div class="text-primary font-bold">{moment(nextInvoiceDate).format(invoize.date_format)}</div>
</div>
