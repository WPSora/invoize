<script>
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Tabs, TabsList, TabsTrigger, TabsContent } from "$lib/components/ui/tabs";
  import { activeTab3, isPro } from "$lib/stores/settings-store";
  import Bank from "$lib/components/settings/payment/payment-method/bank/bank.svelte";
  import Paypal from "$lib/components/settings/payment/payment-method/paypal/paypal.svelte";
  import Xendit from "$lib/components/settings/payment/payment-method/xendit/xendit.svelte";
  import UpgradeToPro from "$lib/components/dashboard/upgrade-to-pro.svelte";

  const tabsTriggerClass =
    "w-full py-2 flex justify-start data-[state=active]:bg-background data-[state=active]:text-primary data-[state=active]:shadow-none data-[state=active]:text-base rounded-sm";
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Payment Method</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">Manage your payment methods.</CardDescription>
  </CardHeader>
  <CardContent>
    <Tabs bind:value={$activeTab3} class="w-full flex gap-3">
      <TabsList
        class="bg-background shadow-sm max-h-60 h-fit min-w-32 flex flex-col gap-1 justify-start rounded-md overflow-y-auto">
        <TabsTrigger value="bank" class={tabsTriggerClass}>Bank</TabsTrigger>
        <TabsTrigger value="paypal" class={tabsTriggerClass}>Paypal</TabsTrigger>
        <TabsTrigger value="xendit" class={tabsTriggerClass}>
          Xendit
        </TabsTrigger>
      </TabsList>

      <TabsContent value="bank" class="m-0 w-full">
        <Bank />
      </TabsContent>

      <TabsContent value="paypal" class="m-0 w-full">
        <Paypal />
      </TabsContent>

      <TabsContent value="xendit" class="m-0 w-full">
        {#if $isPro}
          <Xendit />
        {:else}
          <UpgradeToPro />
        {/if}
      </TabsContent>
    </Tabs>
  </CardContent>
</Card>
