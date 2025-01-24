<script>
  import { Separator } from "$lib/components/ui/separator";

  export let title = "Title";
  export let description = "Description";
  export let hasDivider = true;
  let className = "";
  export { className as class };
</script>

<div class="space-y-1 mt-4 ml-2 {!hasDivider && 'mb-4'} {className}">
  <h2 class="md:text-[30px] text-xl font-bold tracking-tight">{title}</h2>
  <p class="text-muted-foreground md:text-base text-sm">{description}</p>
</div>

{#if hasDivider}
  <Separator class="my-5" />
{/if}
