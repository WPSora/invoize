<script>
  import moment from "moment";

  export let date;

  $: formattedDate = moment(date).format(invoize.date_format);
</script>

<div class="text-center text-xs">{formattedDate}</div>
