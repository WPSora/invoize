<script> 
  const CurrencySetting = import("$lib/components/settings/invoices/currency.svelte");
  const DiscountSetting = import("$lib/components/settings/invoices/discount.svelte");
  const GeneralSetting = import("$lib/components/settings/invoices/general.svelte");
  const ReminderSetting = import("$lib/components/settings/invoices/reminder.svelte");
  const TaxSetting = import("$lib/components/settings/invoices/tax.svelte");
  
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { invoiceTabSettingStyle } from "$lib/common/styles";
  import { isPro, activeTab2 } from "$lib/stores/settings-store";
  import { Tabs, TabsContent, TabsList, TabsTrigger } from "$lib/components/ui/tabs";
  import ProBadge from "$lib/components/upgrade-to-pro/ProBadge.svelte";

</script>

<Tabs bind:value={$activeTab2}>
  <TabsList class="bg-background h-12 w-full hidden md:flex justify-around p-6 rounded-lg">
    <TabsTrigger value="general" class={invoiceTabSettingStyle}>General</TabsTrigger>
    <TabsTrigger value="currency" class={invoiceTabSettingStyle}>Currency</TabsTrigger>
    <TabsTrigger value="tax" class={invoiceTabSettingStyle}>Tax</TabsTrigger>
    <TabsTrigger value="discount" class={invoiceTabSettingStyle}>Discount</TabsTrigger>
    <TabsTrigger value="reminder" class={invoiceTabSettingStyle} >
      Reminder
    </TabsTrigger>
  </TabsList>

  <!-- General -->
  <TabsContent value="general">
    <Card class="p-4">
      <CardHeader class="space-y-0.5 md:p-6 px-0">
        <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">General</CardTitle>
        <CardDescription class="ml-3 md:text-sm text-xs">Manage general settings.</CardDescription>
      </CardHeader>
      <CardContent class="md:px-6 md:pb-6 p-0">
        {#if GeneralSetting}
          {#await GeneralSetting then { default: GeneralSetting }}
            <GeneralSetting />
          {/await}
        {/if}    
      </CardContent>
    </Card>
  </TabsContent>

  <!-- Currency -->
  <TabsContent value="currency">
    {#if CurrencySetting}
      {#await CurrencySetting then { default: CurrencySetting }}
        <CurrencySetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Tax -->
  <TabsContent value="tax">
    {#if TaxSetting}
      {#await TaxSetting then { default: TaxSetting }}
        <TaxSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Discount -->
  <TabsContent value="discount">
    {#if DiscountSetting}
      {#await DiscountSetting then { default: DiscountSetting }}
        <DiscountSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Reminder -->
  <TabsContent value="reminder">
    {#if ReminderSetting}
      {#await ReminderSetting then { default: ReminderSetting }}
        <ReminderSetting />
      {/await}
    {/if}
  </TabsContent>
</Tabs>
