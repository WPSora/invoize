<script>
  import {
    CircleDollarSign,
    Mails,
    ScrollText,
    Receipt,
    Building2,
    Menu,
    Wrench,
  } from "lucide-svelte";
  import {
    Sheet,
    SheetTitle,
    SheetTrigger,
    SheetContent,
    SheetDescription,
    SheetClose,
    SheetHeader,
  } from "$lib/components/ui/sheet";
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "$lib/components/ui/accordion";
  import { Tabs, TabsContent, TabsList, TabsTrigger } from "$lib/components/ui/tabs";
  import { settingsTabStyle } from "$lib/common/styles";
  import { Button } from "$lib/components/ui/button";
  import { activeTab1, activeTab2 } from "$lib/stores/settings-store";
  import CircleLoading from "../misc/CircleLoading.svelte";
  import PageTitle from "$lib/components/custom-ui/PageTitle.svelte";
  import Breadcrumb from "$lib/components/custom-ui/Breadcrumb.svelte";
  import BusinessSetting from "$lib/components/settings/business/business.svelte";
  const QuotationSetting = import("$lib/components/settings/quotation/quotation.svelte"); 
  const InvoicesSetting = import("$lib/components/settings/invoices/invoices.svelte");
  const PaymentSetting = import("$lib/components/settings/payment/payment.svelte");
  const EmailSetting = import("$lib/components/settings/email/email.svelte");
  const ReceiptSetting = import("$lib/components/settings/receipt/receipt.svelte");
  const IntegrationSetting = import("$lib/components/settings/integration/integration.svelte");
  const OtherSetting = import("$lib/components/settings/other/other.svelte");

  const nav = [{ name: "Settings", link: "setting/" }];

  let openedAccordion;
  let selectedTab = "business";

  // for small screen nav only
  // large screen nav use the bind to update the tab value
  const updateTab = (tab1, tab2 = null) => {
    $activeTab1 = tab1;
    selectedTab = tab1;
    if (tab2) {
      $activeTab2 = tab2;
      selectedTab = tab1 + "-" + tab2;
    }
  };

  const setDefaultTab2 = (tab1) => {
    if (tab1 === "invoice") {
      $activeTab2 = "general";
    } else if (tab1 === "email") {
      $activeTab2 = "smtp";
    } else if (tab1 === "payment") {
      $activeTab2 = "default";
    }
  };
</script>

<Breadcrumb from="Dashboard" to={nav} />

<div class="flex md:justify-between justify-end items-center">
  <!-- title for large screen only -->
  <PageTitle title="Settings" description="Manage your invoice settings." hasDivider={false} class="md:block hidden" />
  <!-- Navbar for small screen -->
  <Sheet>
    <SheetTrigger asChild let:builder>
      <Button builders={[builder]} size="icon" class="md:hidden my-2">
        <Menu class="h-6 w-6" />
      </Button>
    </SheetTrigger>

    <SheetContent class="overflow-y-auto">
      <SheetHeader class="mt-10 mb-4">
        <SheetTitle class="text-right">Settings</SheetTitle>
        <SheetDescription class="mt-0 text-right">Manage WP Invoice settings.</SheetDescription>
      </SheetHeader>

      <Accordion multiple bind:value={openedAccordion}>
        <SheetClose class="w-full">
          <Button
            variant="link"
            class="flex justify-start text-black pl-2 py-6 border-b w-full rounded-none {selectedTab === 'business' &&
              'bg-primary-800 text-white rounded-lg'}"
            on:click={() => updateTab("business")}>
            Business
          </Button>
        </SheetClose>

        <AccordionItem value="invoice">
          <AccordionTrigger class="pl-2">Invoice</AccordionTrigger>
          <AccordionContent>
            <SheetClose class="w-full">
              <div class="flex flex-col">
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-general' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "general")}>General</Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-currency' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "currency")}>
                  Currency
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-tax' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "tax")}>Tax</Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-discount' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "discount")}>
                  Discount
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-reminder' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "reminder")}>
                  Reminder
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'invoice-recurring' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("invoice", "recurring")}>
                  Recurring
                </Button>
              </div>
            </SheetClose>
          </AccordionContent>
        </AccordionItem>

        <SheetClose class="w-full">
          <Button
            variant="link"
            class="flex justify-start text-black pl-2 py-6 border-b w-full rounded-none {selectedTab === 'receipt' &&
              'bg-primary-800 text-white rounded-lg'}"
            on:click={() => updateTab("receipt")}>
            Receipt
          </Button>
        </SheetClose>

        <AccordionItem value="email">
          <AccordionTrigger class="pl-2">E-Mail</AccordionTrigger>
          <AccordionContent>
            <SheetClose class="w-full">
              <div class="flex flex-col">
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'email-automation' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("email", "automation")}>Automation</Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'email-smtp' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("email", "smtp")}>
                  SMTP Account
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'email-templates' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("email", "templates")}>Templates</Button>
              </div>
            </SheetClose>
          </AccordionContent>
        </AccordionItem>

        <AccordionItem value="payment">
          <AccordionTrigger class="pl-2 ">Payment</AccordionTrigger>
          <AccordionContent>
            <SheetClose class="w-full">
              <div class="flex flex-col">
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'payment-default' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("payment", "default")}>
                  Default
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'payment-banks' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("payment", "banks")}>
                  Banks
                </Button>
                <Button
                  variant="ghost"
                  class="flex justify-start {selectedTab === 'payment-paypal' && 'bg-primary-800 text-white'}"
                  on:click={() => updateTab("payment", "paypal")}>
                  Paypal
                </Button>
              </div>
            </SheetClose>
          </AccordionContent>
        </AccordionItem>

        <SheetClose class="w-full">
          <Button
            variant="link"
            class="flex justify-start text-black pl-2 py-6 border-b w-full rounded-none {selectedTab === 'client' &&
              'bg-primary-800 text-white rounded-lg'}"
            on:click={() => updateTab("client")}>
            Customer
          </Button>
        </SheetClose>

        <SheetClose class="w-full">
          <Button
            variant="link"
            class="flex justify-start text-black pl-2 py-6 border-b w-full rounded-none {selectedTab === 'product' &&
              'bg-primary-800 text-white rounded-lg'}"
            on:click={() => updateTab("product")}>
            Product
          </Button>
        </SheetClose>
      </Accordion>
    </SheetContent>
  </Sheet>
</div>

<Tabs
  bind:value={$activeTab1}
  onValueChange={(tab) => setDefaultTab2(tab)}
  class="w-full flex md:flex-row flex-col md:items-start items-end gap-3">
  <TabsList class="hidden md:flex flex-col h-auto w-48 gap-1 items-start bg-background rounded-md">
    <TabsTrigger value="business" class="{settingsTabStyle} gap-2">
      <Building2 class="h-5" />
      Business
    </TabsTrigger>

    <TabsTrigger value="quotation" class="{settingsTabStyle} gap-2">
      <ScrollText class="h-5" />
      Quotation
    </TabsTrigger>

    <TabsTrigger value="invoice" class="{settingsTabStyle} gap-2">
      <ScrollText class="h-5" />
      Invoice
    </TabsTrigger>

    <TabsTrigger value="receipt" class="{settingsTabStyle} gap-2">
      <Receipt class="h-5" />
      Receipt
    </TabsTrigger>

    <TabsTrigger value="email" class="{settingsTabStyle} gap-2">
      <Mails class="h-5" />
      E-Mail
    </TabsTrigger>

    <TabsTrigger value="payment" class="{settingsTabStyle} gap-2">
      <CircleDollarSign class="h-5" />
      Payment
    </TabsTrigger>

    <TabsTrigger value="integration" class="{settingsTabStyle} gap-2">
      <div class="w-5 h-5 pt-1">
        <svg preserveAspectRatio="xMidYMid" version="1.1" viewBox="0 0 256 153" xmlns="http://www.w3.org/2000/svg">
          <path
            d="m23.759 0h208.38c13.187 0 23.863 10.675 23.863 23.863v79.542c0 13.187-10.675 23.863-23.863 23.863h-74.727l10.257 25.118-45.109-25.118h-98.695c-13.187 0-23.863-10.675-23.863-23.863v-79.542c-0.10466-13.083 10.571-23.863 23.758-23.863z"
            fill="#7f54b3" />
          <path
            d="m14.578 21.75c1.4569-1.9772 3.6423-3.0179 6.5561-3.226 5.3073-0.41626 8.3252 2.0813 9.0537 7.4927 3.226 21.75 6.7642 40.169 10.511 55.259l22.79-43.395c2.0813-3.9545 4.6829-6.0358 7.8049-6.2439 4.5789-0.3122 7.3886 2.6016 8.5333 8.7415 2.6016 13.841 5.9317 25.6 9.8862 35.59 2.7057-26.433 7.2846-45.476 13.737-57.236 1.561-2.9138 3.8504-4.3707 6.8683-4.5789 2.3935-0.20813 4.5789 0.52033 6.5561 2.0813 1.9772 1.561 3.0179 3.5382 3.226 5.9317 0.10406 1.8732-0.20813 3.4341-1.0407 4.9951-4.0585 7.4927-7.3886 20.085-10.094 37.567-2.6016 16.963-3.5382 30.179-2.9138 39.649 0.20813 2.6016-0.20813 4.8911-1.2488 6.8683-1.2488 2.2894-3.122 3.5382-5.5154 3.7463-2.7057 0.20813-5.5154-1.0406-8.2211-3.8504-9.678-9.8862-17.379-24.663-22.998-44.332-6.7642 13.32-11.759 23.311-14.985 29.971-6.1398 11.759-11.343 17.795-15.714 18.107-2.8098 0.20813-5.2033-2.1854-7.2846-7.1805-5.3073-13.633-11.031-39.961-17.171-78.985-0.41626-2.7057 0.20813-5.0992 1.665-6.9724zm223.64 16.338c-3.7463-6.5561-9.2618-10.511-16.65-12.072-1.9772-0.41626-3.8504-0.62439-5.6195-0.62439-9.9902 0-18.107 5.2033-24.455 15.61-5.4114 8.8455-8.1171 18.628-8.1171 29.346 0 8.013 1.665 14.881 4.9951 20.605 3.7463 6.5561 9.2618 10.511 16.65 12.072 1.9772 0.41626 3.8504 0.62439 5.6195 0.62439 10.094 0 18.211-5.2033 24.455-15.61 5.4114-8.9496 8.1171-18.732 8.1171-29.45 0.10406-8.1171-1.665-14.881-4.9951-20.501zm-13.112 28.826c-1.4569 6.8683-4.0585 11.967-7.9089 15.402-3.0179 2.7057-5.8276 3.8504-8.4293 3.3301-2.4976-0.52033-4.5789-2.7057-6.1398-6.7642-1.2488-3.226-1.8732-6.452-1.8732-9.4699 0-2.6016 0.20813-5.2033 0.72846-7.5967 0.93659-4.2667 2.7057-8.4293 5.5154-12.384 3.4341-5.0992 7.0764-7.1805 10.823-6.452 2.4976 0.52033 4.5789 2.7057 6.1398 6.7642 1.2488 3.226 1.8732 6.452 1.8732 9.4699 0 2.7057-0.20813 5.3073-0.72846 7.7008zm-52.033-28.826c-3.7463-6.5561-9.3659-10.511-16.65-12.072-1.9772-0.41626-3.8504-0.62439-5.6195-0.62439-9.9902 0-18.107 5.2033-24.455 15.61-5.4114 8.8455-8.1171 18.628-8.1171 29.346 0 8.013 1.665 14.881 4.9951 20.605 3.7463 6.5561 9.2618 10.511 16.65 12.072 1.9772 0.41626 3.8504 0.62439 5.6195 0.62439 10.094 0 18.211-5.2033 24.455-15.61 5.4114-8.9496 8.1171-18.732 8.1171-29.45 0-8.1171-1.665-14.881-4.9951-20.501zm-13.216 28.826c-1.4569 6.8683-4.0585 11.967-7.9089 15.402-3.0179 2.7057-5.8276 3.8504-8.4293 3.3301-2.4976-0.52033-4.5789-2.7057-6.1398-6.7642-1.2488-3.226-1.8732-6.452-1.8732-9.4699 0-2.6016 0.20813-5.2033 0.72846-7.5967 0.93658-4.2667 2.7057-8.4293 5.5154-12.384 3.4341-5.0992 7.0764-7.1805 10.823-6.452 2.4976 0.52033 4.5789 2.7057 6.1398 6.7642 1.2488 3.226 1.8732 6.452 1.8732 9.4699 0.10406 2.7057-0.20813 5.3073-0.72846 7.7008z"
            fill="#fff" />
        </svg>
      </div>

      <!-- <Blocks class="h-5" /> -->
      WooCommerce
    </TabsTrigger>

    <TabsTrigger value="other" class="{settingsTabStyle} gap-2">
      <Wrench class="h-5"/>
      Other
    </TabsTrigger>
  </TabsList>

  <!-- Business -->
  <TabsContent value="business" class="w-full mt-0">
    <BusinessSetting />
  </TabsContent>

  <!-- Quotation -->
  <TabsContent value="quotation" class="w-full mt-0">
    {#if QuotationSetting}
      {#await QuotationSetting}
        <CircleLoading />
      {:then { default: QuotationSetting }}
        <QuotationSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Invoice -->
  <TabsContent value="invoice" class="w-full mt-0">
    {#if InvoicesSetting}
      {#await InvoicesSetting}
        <CircleLoading />
      {:then { default: InvoicesSetting }}
        <InvoicesSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Receipt -->
  <TabsContent value="receipt" class="w-full mt-0">
    {#if ReceiptSetting}
      {#await ReceiptSetting}
        <CircleLoading />
      {:then { default: ReceiptSetting }}
        <ReceiptSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Email -->
  <TabsContent value="email" class="w-full mt-0">
    {#if EmailSetting}
      {#await EmailSetting}
        <CircleLoading />
      {:then { default: EmailSetting }}
        <EmailSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Payment -->
  <TabsContent value="payment" class="w-full mt-0">
    {#if PaymentSetting}
      {#await PaymentSetting}
        <CircleLoading />
      {:then { default: PaymentSetting }}
        <PaymentSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Integration -->
  <TabsContent value="integration" class="w-full mt-0">
    {#if IntegrationSetting}
      {#await IntegrationSetting}
        <CircleLoading />
      {:then { default: IntegrationSetting }}
        <IntegrationSetting />
      {/await}
    {/if}
  </TabsContent>

  <!-- Integration -->
  <TabsContent value="other" class="w-full mt-0">
    {#if OtherSetting}
      {#await OtherSetting}
        <CircleLoading />
      {:then { default: OtherSetting }}
        <OtherSetting />
      {/await}
    {/if}
  </TabsContent>
</Tabs>
