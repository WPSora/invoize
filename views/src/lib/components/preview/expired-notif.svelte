<script>
  import { Alert, AlertTitle, AlertDescription } from "$lib/components/ui/alert";
  import { AlertCircle } from "lucide-svelte";

  export let tab;
</script>

<div class="w-full h-60 flex justify-center items-center">
  <Alert class="md:w-1/2 w-full md:mx-0 mx-5 flex">
    <div>
      <AlertCircle class="h-8 w-8 text-destructive mr-2" />
    </div>
    <div>
      <AlertTitle>
        This invoice has {tab}
      </AlertTitle>
      <AlertDescription>Please contact your merchant for more information.</AlertDescription>
    </div>
  </Alert>
</div>
