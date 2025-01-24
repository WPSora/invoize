<script>
  export let totalInactive;
</script>

<div class="text-center font-semibold text-xs {totalInactive > 0 ? 'text-destructive' : 'text-gray-300'} ">
  {totalInactive}
</div>
