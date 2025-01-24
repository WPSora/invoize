<script>
  import { CardHeader, CardDescription, CardTitle } from "$lib/components/ui/card";
  import { slide } from "svelte/transition";
</script>

<div class="flex-1">
  <CardHeader class="space-y-0.5 md:px-0 md:pt-0 md:pb-2 px-0">
    <CardTitle class="md:text-base">Payment Methods</CardTitle>
    <CardDescription class="text-xs">Manage your Xendit available payment methods</CardDescription>
  </CardHeader>

  <div class="flex flex-col gap-4">
    <div transition:slide class="bg-muted rounded-lg p-4 text-muted-foreground">
      <div class="text-sm">
        To manage your Xendit account payment methods, go to Checkout Settings in your Xendit account dashboard.
      </div>
      <div class="text-xs">For more information, go to this link</div>
    </div>
  </div>
</div>
