<script>
  export let isShowEmpty = false;
  export let text;
  let className = "";
  export { className as class };

  const addLineBreak = (note) => {
    if (note) {
      return note.split(/\n|\r\n/g).map((t) => ({ text: t, br: "<br>" }));
    }
    return [];
  };

  $: formattedText = addLineBreak(text ?? "");
</script>

<div class={className}>
  {#if formattedText?.length <= 1 && formattedText[0]?.text === ""}
    {#if isShowEmpty}
      <span></span>
    {:else}
      -
    {/if}
  {:else}
    {#each formattedText as { text, br }}
      {text}
      {@html br}
    {:else}
      {#if isShowEmpty}
        <span></span>
      {:else}
        -
      {/if}
    {/each}
  {/if}
</div>
