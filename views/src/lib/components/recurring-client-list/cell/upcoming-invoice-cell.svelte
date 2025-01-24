<script>
  import moment from "moment";

  export let upcomingInvoiceDate;

  $: formattedDate = moment(upcomingInvoiceDate).format(invoize.date_format);
</script>

<div class="text-center text-xs">{upcomingInvoiceDate ? formattedDate : "-"}</div>
