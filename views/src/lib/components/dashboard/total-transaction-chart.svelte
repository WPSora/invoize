<script>
  import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "$lib/components/ui/card";
  import { Separator } from "$lib/components/ui/separator";
  import { Loader2 } from "lucide-svelte";
  import { isPro, isProPopupOpen } from "$lib/stores/settings-store";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";

  export let isLoadingSummary;
  export let data;
  export let chart;
  export let chartTotalOptions;
  export let selectedCurrencyName;
</script>

<Card>
  <CardHeader>
    <CardTitle class="text-primary sm:text-xl text-lg">Total Transactions ({selectedCurrencyName})</CardTitle>
    <Separator />
  </CardHeader>
  <CardContent class="px-4 relative">
    {#if !$isPro && !isLoadingSummary}
      <div
        class="ml-[-16px] rounded-xl backdrop-blur-md bg-white/10 w-full h-full absolute top-0 z-20 flex items-center justify-center">
        <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} />
      </div>
    {/if}
    {#if isLoadingSummary}
      <div class="flex justify-center w-full">
        <Loader2 class="h-10 w-10 animate-spin text-primary" />
      </div>
    {:else if chart && (data.summary.length || Object.keys(data.summary).length)}
      <div use:chart={chartTotalOptions} />
    {:else}
      <div class="text-sm text-muted-foreground text-center flex items-center justify-center italic pb-2 h-60">
        No invoice found
      </div>
    {/if}
  </CardContent>
</Card>
