<script>
  import { Dialog, DialogContent, DialogDescription } from "$lib/components/ui/dialog";
  import { CheckCircle, ArrowUpCircleIcon } from "lucide-svelte";
  import { proFeatures } from "$lib/common/options";
  import Button from "$lib/components/ui/button/button.svelte";
  import ProBadge from "$lib/components/upgrade-to-pro/ProBadge.svelte";

  export let open = false;

  $: isSmallScreen = window.innerWidth < 768;
</script>

<Dialog bind:open>
  <DialogContent class="max-w-screen-sm">
    <div class="flex w-full items-center">
      <img src="{invoize.plugin_url}assets/icon.svg" class="w-[40px] h-auto" alt="invoize-icon" />
      <span class="text-2xl font-semibold"> Invoize </span>
      <ProBadge />
    </div>
    <span class="font-semibold"> Upgrade to Invoize Pro now to start enjoying these benefits: </span>
    <div class="flex">
      <ul class="font-light grid sm:grid-cols-2 grid-cols-1">
        {#each proFeatures as feature}
          <li class="flex items-center">
            <CheckCircle class="h-4 w-4 mr-3 text-green-500" />
            {feature}
          </li>
        {/each}
      </ul>
    </div>
    <div>
      <Button class="w-full hover:text-white focus:text-white" href="{invoize.pricing_url}" target="_blank">
        <CheckCircle class="w-5 h-5 mr-1" />
        Upgrade to Pro
      </Button>
    </div>
  </DialogContent>
</Dialog>
