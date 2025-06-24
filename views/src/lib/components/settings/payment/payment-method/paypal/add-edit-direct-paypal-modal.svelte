<script>
  import { Dialog, DialogHeader, DialogTitle, DialogContent, DialogDescription } from "$lib/components/ui/dialog";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { Button } from "$lib/components/ui/button";
  import { Loader2 } from "lucide-svelte";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";

  export let isOpen;
  export let isLoading;
  export let resetInputValue;
  export let submit;
  export let inputValue;
  export let placeholder;
  export let modalTitle;
  export let modalDescription;
  export let label;
  // export let extraForm = [{
  //   inputValue: "",
  //   placeholder: "",
  //   label: "",
  // }];
</script>

<Dialog bind:open="{isOpen}" onOpenChange="{resetInputValue}">
  <DialogContent class="sm:max-w-lg">
    <DialogHeader>
      <DialogTitle>{modalTitle}</DialogTitle>
      <DialogDescription>{modalDescription}</DialogDescription>
    </DialogHeader>
    <form class="space-y-4" on:submit|preventDefault="{submit}">
      <div class="space-y-2">
        <Label for="name">{label} <MiniStar /></Label>
        <Input type="text" id="name" required bind:value="{inputValue}" {placeholder} />
      </div>
      <slot />
      <div class="pt-4">
        <Button disabled="{isLoading}" type="submit">
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </div>
    </form>
  </DialogContent>
</Dialog>
