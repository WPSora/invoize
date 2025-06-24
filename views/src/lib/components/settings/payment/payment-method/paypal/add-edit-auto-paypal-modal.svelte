<script>
  import { Dialog, DialogHeader, DialogTitle, DialogContent, DialogDescription } from "$lib/components/ui/dialog";
  import { Tabs, TabsContent, TabsTrigger, TabsList } from "$lib/components/ui/tabs";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { Switch } from "$lib/components/ui/switch";
  import { Button } from "$lib/components/ui/button";
  import { Loader2 } from "lucide-svelte";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";

  export let isModalAutomaticOpen;
  export let resetAutomaticPaypal;
  export let submitAutomaticPaypal;
  export let isLoading;
  export let sandboxAutomaticPaypal;
  export let liveAutomaticPaypal;
</script>

<Dialog bind:open={isModalAutomaticOpen} onOpenChange={resetAutomaticPaypal}>
  <DialogContent class="sm:max-w-lg">
    <DialogHeader>
      <DialogTitle>Payment Account</DialogTitle>
      <DialogDescription>Manage your paypal account for receiving payment.</DialogDescription>
    </DialogHeader>
    <Tabs>
      <TabsList class="w-full">
        <TabsTrigger class="w-full data-[state=active]:bg-yellow-500" value="sandbox">Sandbox</TabsTrigger>
        <TabsTrigger class="w-full data-[state=active]:bg-green-600" value="live">Live</TabsTrigger>
      </TabsList>
      <TabsContent value="sandbox">
        <form class="space-y-4" on:submit|preventDefault={submitAutomaticPaypal}>
          <div class="space-y-2">
            <Label for="name">Name <MiniStar /></Label>
            <Input
              type="text"
              id="name"
              required
              bind:value={sandboxAutomaticPaypal.name}
              placeholder="Paypal account name" />
          </div>
          <div class="space-y-2">
            <Label for="type">Client ID <MiniStar /></Label>
            <Input
              type="text"
              id="type"
              required
              bind:value={sandboxAutomaticPaypal.clientId}
              placeholder="Paypal Client ID" />
          </div>
          <div class="space-y-2">
            <Label for="type">Secret ID <MiniStar /></Label>
            <Input
              type="text"
              id="type"
              required
              bind:value={sandboxAutomaticPaypal.secretId}
              placeholder="Paypal Secret ID" />
          </div>
          <!-- <div class="flex gap-4 items-center">
            <Label for="sandbox">Sandbox Account</Label>
            <Switch id="sandbox" required bind:checked={automaticPaypal.sandbox.isSandbox} />
          </div> -->
          <div class="pt-4">
            <Button disabled={isLoading} type="submit">
              {#if isLoading}
                <Loader2 class="mr-2 w-4 h-4 animate-spin" />
                Saving
              {:else}
                Save
              {/if}
            </Button>
          </div>
        </form>
      </TabsContent>
      <TabsContent value="live">
        <form class="space-y-4" on:submit|preventDefault={submitAutomaticPaypal}>
          <div class="space-y-2">
            <Label for="name">Name <MiniStar /></Label>
            <Input
              type="text"
              id="name"
              required
              bind:value={liveAutomaticPaypal.name}
              placeholder="Paypal account name" />
          </div>
          <div class="space-y-2">
            <Label for="type">Client ID <MiniStar /></Label>
            <Input
              type="text"
              id="type"
              required
              bind:value={liveAutomaticPaypal.clientId}
              placeholder="Paypal Client ID" />
          </div>
          <div class="space-y-2">
            <Label for="type">Secret ID <MiniStar /></Label>
            <Input
              type="text"
              id="type"
              required
              bind:value={liveAutomaticPaypal.secretId}
              placeholder="Paypal Secret ID" />
          </div>
          <!-- <div class="flex gap-4 items-center">
            <Label for="sandbox">Sandbox Account</Label>
            <Switch id="sandbox" required bind:checked={automaticPaypal.isSandbox} />
          </div> -->
          <div class="pt-4">
            <Button disabled={isLoading} type="submit">
              {#if isLoading}
                <Loader2 class="mr-2 w-4 h-4 animate-spin" />
                Saving
              {:else}
                Save
              {/if}
            </Button>
          </div>
        </form>
      </TabsContent>
    </Tabs>
  </DialogContent>
</Dialog>
