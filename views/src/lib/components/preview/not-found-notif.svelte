<script>
  import { Alert, AlertTitle } from "$lib/components/ui/alert";
  import { HelpCircle } from "lucide-svelte";
</script>

<div class="w-full h-60 flex justify-center items-center">
  <Alert class="md:w-1/2 w-full md:mx-0 mx-5 flex items-center text-lg">
    <div>
      <HelpCircle class="h-8 w-8 text-amber-500 mr-3" />
    </div>
    <AlertTitle class="text-muted-foreground">Invoice not found</AlertTitle>
  </Alert>
</div>
