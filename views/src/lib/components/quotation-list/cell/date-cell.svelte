<script>
  import moment from "moment";

  export let date;

  $: formatedDate = moment(date).format(invoize.date_format);
</script>

<div class="text-center text-xs">
  <div>{formatedDate}</div>
</div>
