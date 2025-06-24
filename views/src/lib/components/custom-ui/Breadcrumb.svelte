<script>
  import { ChevronRight, ArrowLeft } from "radix-icons-svelte";
  import { Button } from "$lib/components/ui/button";
  import { isPro } from "$lib/stores/settings-store";
  import ProBadge from "$lib/components/upgrade-to-pro/ProBadge.svelte";
  import { recurringTab } from "$lib/stores/active-tab-store";
  import { RecurringListTab } from "$lib/common/enum";

  /** @type {Array<{ name: string, link: string }>} */
  export let to = [];

  /** @type {string}*/
  export let from = "Dashboard";
</script>

<div
  class="flex sm:flex-row flex-col-reverse items-center justify-between space-x-1 text-sm text-muted-foreground bg-gradient-to-r from-slate-200 to-primary/0 rounded-lg px-4 py-4">
  <div class="flex items-center flex-nowrap">
    <!-- Back button -->
    <Button
      class="p-0 sm:mr-6 mr-2 text-muted-foreground hover:text-black"
      variant="link"
      on:click="{() => window.history.back()}">
      <ArrowLeft size="{30}" />
    </Button>

    <!-- Dashboard -->
    <div
      class="overflow-hidden whitespace-nowrap cursor-pointer hover:font-bold {to.length < 1 &&
        'text-foreground font-bold'}"
      role="button"
      tabindex="0"
      on:click="{() => (location.href = '#/')}"
      on:keydown="{() => {}}">
      {from}
    </div>

    {#each to as nav, i}
      <ChevronRight size="{15}" />
      <!-- if on current page, currenct nav link is unclickable -->
      {#if i === to.length - 1}
        <div class="font-bold text-foreground">
          {nav.name}
        </div>
        <!-- Only prev nav link is clickable -->
      {:else}
        <div
          on:click="{() => (location.href = `#/${nav.link}`)}"
          on:keydown="{() => {}}"
          role="button"
          tabindex="0"
          class="cursor-pointer hover:font-bold">
          {nav.name}
        </div>
      {/if}
    {/each}
  </div>

  <!-- Pro button on and off -->
  <!-- <div class="flex items-center gap-2 bg-white p-3 rounded-lg">
    <Switch id="pro" bind:checked={$isPro} />
    <Label for="pro">Pro</Label>
  </div> -->

  <div class="font-bold text-3xl text-right text-primary flex sm:mb-0 mb-2">
    <a href="https://wpsora.com" target="_blank" class="flex justify-center items-center hover:text-primary-700">
      <img src="{invoize.plugin_url}assets/icon.svg" alt="invoize-icon" />
      INVOIZE
      {#if $isPro}
        <ProBadge />
      {/if}
    </a>
  </div>
</div>
