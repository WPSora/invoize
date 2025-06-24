<script>
  import moment from "moment";

  export let dueDate;

  $: dueFromNow = moment(dueDate).from(moment().subtract(1, "days"));
</script>

<div class="flex flex-col justify-center items-center">
  <div class="font-medium">Due:</div>
  <div class="text-destructive font-bold">{dueFromNow}</div>
</div>
