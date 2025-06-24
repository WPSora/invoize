<script>
  import moment from "moment";

  export let date;

  $: date = moment(date).format(invoize.date_format);
</script>

<div class="text-center text-xs">
  <div>{date}</div>
</div>
