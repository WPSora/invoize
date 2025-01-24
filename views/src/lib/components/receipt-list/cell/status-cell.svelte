<script>
  export let status;

  $: uppercaseStatus = status?.toUpperCase();
  const badgeStyle = "flex justify-center items-center w-24 rounded-md px-4 py-0.5 text-muted-foreground";
</script>

<div class="flex flex-col items-center text-xs">
  {#if status === "paid"}
    <div class="{badgeStyle} bg-teal-100">
      {uppercaseStatus}
    </div>
  {:else if status === "archived"}
    <div class="{badgeStyle} bg-yellow-100">
      {uppercaseStatus}
    </div>
  {:else if status === "cancelled"}
    <div class="{badgeStyle} bg-gray-200">
      {uppercaseStatus}
    </div>
  {/if}
</div>
