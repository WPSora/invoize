<script>
  import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "$lib/components/ui/card";
  import { Separator } from "$lib/components/ui/separator";
  import { CheckCircle } from "lucide-svelte";
  import { proFeatures } from "$lib/common/options";
  import { isProPopupOpen } from "$lib/stores/settings-store";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";
  import ProBadge from "$lib/components/upgrade-to-pro/ProBadge.svelte";
  import Button from "../ui/button/button.svelte";
</script>

<Card>
  <CardHeader>
    <CardTitle class="text-primary sm:text-xl text-lg flex justify-center">
      Upgrade to Invoize
      <ProBadge />
    </CardTitle>
    <Separator />
  </CardHeader>
  <CardContent class="space-y-1 text-sm">
    <div class="sm:text-lg text-sm mb-2">Upgrade to <span class="text-primary">Invoize Pro</span> now to start enjoying these benefits:</div>
    <ul class="font-light grid sm:grid-cols-2 grid-cols-1 sm:text-sm text-xs">
      {#each proFeatures as feature}
        <li class="flex items-center">
          <CheckCircle class="h-4 w-4 mr-3 text-primary" />
          {feature}
        </li>
      {/each}
    </ul>
  </CardContent>
  <CardFooter class="flex justify-center">
    <Button href="{invoize.pricing_url}" class="w-full hover:text-white" target="_blank">Upgrade to Pro</Button>
    <!-- <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} class="w-full" /> -->
  </CardFooter>
</Card>
