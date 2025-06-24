<script>
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  export let content;
</script>

<!-- For note & terms -->
<MultilineText text={content} class="text-[0.65rem] leading-4" />
