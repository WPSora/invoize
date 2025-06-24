<script>
  import { Button } from "$lib/components/ui/button";
  import { CheckCircle } from "lucide-svelte";

  export let isProPopupOpen;
  export let customText = "Upgrade to Pro";
  let className = "";
  export { className as class };
</script>

<Button
  on:click={() => (isProPopupOpen = true)}
  class="md:text-sm sm:w-fit w-full text-xs border-primary sm:whitespace-nowrap whitespace-normal hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary active:bg-primary active:text-white active:shadow-lg active:shadow-primary {className}">
  <CheckCircle class="h-4 w-4 mr-2" />
  {customText}
</Button>
