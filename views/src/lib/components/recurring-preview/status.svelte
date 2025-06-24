<script>
  import { RecurringStatus } from "$lib/common/enum";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let status;
</script>

<div class="flex flex-col justify-center items-center">
  <div class="font-medium">Status:</div>
  <div
    class="text-white font-bold px-2 rounded-md {status === RecurringStatus.ACTIVE
      ? 'bg-green-600'
      : 'bg-destructive'}">
    {capitalizeFirstLetter(status)}
  </div>
</div>
