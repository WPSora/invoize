<script>
  import { Button } from "$lib/components/ui/button";
  import { Trash } from "radix-icons-svelte";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";

  /**
   * @type {"default"| "sm"| "icon" | "lg"}
   */
  export let size = "icon";

  /**
   * @type {"default"| "link"| "outline" | "ghost" | "secondary" | "destructive"}
   */
  export let variant = "outline";

  /**
   * @type {boolean}
   */
  export let disabled = false;

  let className = "";
  export { className as class };
</script>

<Tooltip>
  <TooltipTrigger>
    <Button
      {size}
      {variant}
      {disabled}
      on:click
      on:keydown
      class="text-destructive hover:bg-red-500 hover:border-red-500 hover:text-white hover:shadow-red-400 {className}">
      <slot>
        <Trash class="w-4 h-4" />
      </slot>
    </Button>
  </TooltipTrigger>
  <TooltipContent class="bg-white shadow-lg">
    <p class="text-red-600">Delete</p>
  </TooltipContent>
</Tooltip>
