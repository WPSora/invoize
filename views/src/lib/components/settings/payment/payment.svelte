<script>
  import { Tabs, TabsList, TabsTrigger, TabsContent } from "$lib/components/ui/tabs";
  import { activeTab2 } from "$lib/stores/settings-store";
  import { paymentTabSettingStyle } from "$lib/common/styles";
  import DefaultPayment from "$lib/components/settings/payment/default-payment.svelte";
  import PaymentMethod from "$lib/components/settings/payment/payment-method/payment-method.svelte";
</script>

<Tabs bind:value={$activeTab2} class="w-full">
  <TabsList class="bg-background h-12 w-full hidden md:flex justify-around p-6 rounded-xl">
    <TabsTrigger value="default" class={paymentTabSettingStyle}>Default</TabsTrigger>
    <TabsTrigger value="payment-method" class={paymentTabSettingStyle}>Payment Method</TabsTrigger>
  </TabsList>

  <!-- Default -->
  <TabsContent value="default">
    <DefaultPayment />
  </TabsContent>

  <!-- Payment Method -->
  <TabsContent value="payment-method" class="w-full">
    <PaymentMethod />
  </TabsContent>
</Tabs>
