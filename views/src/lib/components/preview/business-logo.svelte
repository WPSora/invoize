<script>
  export let logo;
</script>

<div>
  {#if logo && logo !== "false"}
    <img class="h-20 object-cover" src={logo} alt="business logo" />
  {/if}
</div>
