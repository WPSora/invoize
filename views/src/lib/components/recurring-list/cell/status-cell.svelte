<script>
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";

  export let status;

  const badgeStyle = "flex justify-center items-center text-[10px] w-24 rounded-md text-[13px] px-4 my-0.5";
</script>

<div class="flex flex-col items-center text-xs">
  <div class="{badgeStyle} {status === 'active' ? 'bg-teal-100' : 'bg-red-200'} rounded-md text-center text-black">
    {status.toUpperCase()}
  </div>
</div>
