<script>
  import moment from "moment";

  export let date;

  $: formattedDate = moment(date).format(invoize.date_format);
</script>

<div class="text-xs">{date ? formattedDate : "-"}</div>
