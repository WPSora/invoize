<script>
  import { pluralHelper } from "$lib/helpers/pluralHelper";

  export let dueDateInterval;
</script>

<div class="flex flex-row flex-nowrap">
  <div class="w-32 font-medium space-y-1">
    <div>Due date</div>
  </div>
  <div class="w-10 flex flex-col flex-nowrap items-center space-y-1">
    <div>:</div>
  </div>
  <div class="flex flex-col flex-nowrap items-end w-32 space-y-1">
    <div>{pluralHelper(`${dueDateInterval} day`)}</div>
  </div>
</div>
