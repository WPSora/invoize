<script>
  import moment from "moment";

  export let invoiceDate;

  $: date = moment(invoiceDate).format(invoize.date_format);
</script>

<div class="text-center text-xs">
  <div>{date}</div>
</div>
