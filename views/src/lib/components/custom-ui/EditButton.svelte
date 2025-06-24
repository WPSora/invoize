<script>
  import { Button } from "$lib/components/ui/button";
  import { Pencil } from "lucide-svelte";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";

  /**
   * @type {"default"| "sm"| "icon" | "lg"}
   */
  export let size = "icon";

  /**
   * @type {"default"| "link"| "outline" | "ghost" | "secondary" | "destructive"}
   */
  export let variant = "outline";

  let className = "";
  export { className as class };
</script>

<Tooltip>
  <TooltipTrigger>
    <Button
      {size}
      {variant}
      on:click
      on:keydown
      class="hover:bg-primary hover:text-white text-primary hover:shadow-primary-600 hover:shadow-md transition-all hover:border-primary {className}">
      <slot>
        <Pencil class="w-4 h-4" />
      </slot>
    </Button>
  </TooltipTrigger>
  <TooltipContent class="bg-white shadow-lg border">
    <p class="text-black">Edit</p>
  </TooltipContent>
</Tooltip>
