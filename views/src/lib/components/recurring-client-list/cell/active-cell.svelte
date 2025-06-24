<script>
  export let totalActive;
</script>

<div class="text-center font-semibold text-xs {totalActive > 0 ? 'text-green-600' : 'text-gray-300'}">
  {totalActive}
</div>
