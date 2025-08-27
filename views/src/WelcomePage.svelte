<script>
  import * as Card from "$lib/components/ui/card";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { Steps } from "svelte-steps";
  import { onMount } from "svelte";
  import { FileDropzone } from "@skeletonlabs/skeleton";
  import { MinusCircleIcon, Loader2 } from "lucide-svelte";
  import { slide } from "svelte/transition";
  import toast, { Toaster } from "svelte-french-toast";
  import Button from "$lib/components/ui/button/button.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Input from "$lib/components/ui/input/input.svelte";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import lottieWeb from "lottie-web";
  import { tick } from "svelte";
  import CurrencySetting from "$lib/components/settings/invoices/currency.svelte";
  import GeneralSetting from "$lib/components/settings/invoices/general.svelte";
  import IntegrationSetting from "$lib/components/settings/integration/integration.svelte";
  import Bank from "$lib/components/settings/payment/payment-method/bank/bank.svelte";
  import * as Tabs from "$lib/components/ui/tabs";


  let  confettiElement,
    welcomePayload = {},
    businessPayload = {
      web: window.location.origin,// default is current website
    },
    currentStep = 0,
    buttonText = "Continue",
    logoPreview,
    isLoading = false;
  

  $: businessPayload.logo = businessPayload.logo ? businessPayload.logo[0] : null;

  const steps = [{ text: "Business Information" }, { text: "Additional Configuration" }, { text: "Finish" }],
    saveBusiness = async () => {
      isLoading = true;
      createPostRequest("business/add", businessPayload)
        .then((res) => {
          currentStep++;
          isLoading = false;
        })
        .catch((err) => {
          isLoading = false;
          handleError(err, "Failed to save data.");
        });
    },
    onChangeHandler = (event) => {
      const file = event.target.files[0];
      const allowedTypes = ["image/jpeg", "image/png"];
      const allowedSize = 2 * 1024 * 1024; // 2MB

      if (file && file.size > allowedSize) {
        toast.error("File size is too large. Maximum size is 2MB.");
        return;
      }

      if (file && allowedTypes.includes(file.type)) {
        logoPreview = URL.createObjectURL(file);
      } else {
        toast.error("Invalid file type. Only images are allowed.");
      }
    },
    fillbusinessPayload = async () => {
      createGetRequest("business/get")
        .then((res) => {
          if (res.data?.business_name) {
            businessPayload = res.data;
          }
        })
        .catch((err) => {
          handleError(err, "Failed to retrieve data.");
        });
    },
    toNextStep = () => {
      switch (steps[currentStep].text) {
        case "Business Information":
          saveBusiness();
          break;
       case "Additional Configuration":
          toStep(2);
          break;
        case "Finish":
          window.location.href = `${invoize.base_url}/wp-admin/admin.php?page=invoize`;
          break;
      }
    },
    toPrevStep = () => {
      currentStep--;
      toggleButtonText(currentStep);
    },
    toStep = (stepValue) => {
      currentStep = stepValue;
      if(currentStep == 2) {
        loadConfetti();
      }
      toggleButtonText(currentStep);
    },
    skipStep = () => {
      window.location.href = `${invoize.base_url}/wp-admin/admin.php?page=invoize`;
    },
    toggleButtonText = (stepValue) => {
      if(stepValue == 0) {
        buttonText = "Continue to Setup";
      } else if(stepValue == 1) {
        buttonText = "Finish";
      } else {
        buttonText = "Go to Dashboard";
      }
      // buttonText = stepValue === steps.length - 1 ? "Finish" : "Continue";
    };


  const loadConfetti = async () => {
    await tick();
    lottieWeb.loadAnimation({
      container: confettiElement,
      renderer: "svg",
      loop: false,
      autoplay: true,
      path: `${invoize.plugin_url}/public/confetti.json`,
    });
  }

  onMount(() => {
    fillbusinessPayload();
  });
</script>


<div class="mx-auto text-base w-3/4">
  <Steps
    size="1.8rem"
    primary="#4100ae"
    secondary="#e1e1e1"
    current={currentStep}
    {steps}
    on:click={(e) => toStep(e.detail.current)} />
</div>

<!-- Content -->
<Card.Root class="xl:w-[1000px] md:w-[700px] mx-auto mt-4 bg-white">
  {#if currentStep === 0}
  <Card.Header>
    <span class="text-primary-400 text-xs font">Step 1 of 3</span>
    <Card.Title class="text-xl">Business Information</Card.Title>
    <Card.Description>Please fill out the following information to get started.</Card.Description>
  </Card.Header>
  <Card.Content class="flex items-center">
    <div class="w-1/2 mr-auto border bg-gray-50 border-gray-200 rounded-lg p-4">
      <form class="space-y-4">
        <div>
          <Label for="company-name">Company Name</Label>
          <Input
            id="company-name"
            type="text"
            class="my-2"
            placeholder="Your company name"
            bind:value={businessPayload.business_name} />
        </div>
        <div>
          <Label for="company-phone-number">Company Phone Number</Label>
          <Input
            id="company-phone-number"
            type="tel"
            class="my-2"
            placeholder="Your company phone number"
            bind:value={businessPayload.phone_number} />
        </div>
        <div>
          <Label for="company-email">Company Email</Label>
          <Input
            id="company-email"
            type="email"
            class="my-2"
            placeholder="Your company email"
            bind:value={businessPayload.email} />
        </div>
        <div>
          <Label for="company-website">Website</Label>
          <Input
            id="company-website"
            type="url"
            class="my-2"
            placeholder="Your company website"
            bind:value={businessPayload.web} />
        </div>
        <div>
          <Label for="general_zip">ZIP Code</Label>
          <Input
            id="general_zip"
            type="text"
            class="my-2"
            placeholder="Your company ZIP code"
            bind:value={businessPayload.zip} />
        </div>

        <!-- Company Address -->
        <div>
          <Label for="general_address">Address</Label>
          <Textarea
            id="general_address"
            class="my-2 bg-white"
            placeholder="Your company address"
            bind:value={businessPayload.address} />
        </div>

        <div class="flex justify-between md:flex-row flex-col">
          <!-- Company Logo -->
          <div class="w-full">
            <Label>Company Logo</Label>
            <!-- Dropzone and Logo preview -->
            <div class="flex flex-row flex-nowrap mt-2">
              <!-- Dropzone -->
              {#if !logoPreview}
                <div class="w-full mb-5">
                  <FileDropzone
                    name="logoFile"
                    bind:files={businessPayload.logo}
                    padding="p-4"
                    on:change={onChangeHandler}>
                    <svelte:fragment slot="message">
                      <span class="xl:text-sm text-xs"><b>Click</b> here or <b>Drag</b> here</span>
                    </svelte:fragment>
                    <svelte:fragment slot="meta">
                      <span class="text-xs">JPG or PNG format. Max size 2MB</span>
                    </svelte:fragment>
                  </FileDropzone>
                </div>
              {/if}
            </div>

            <!-- Logo preview -->
            {#if logoPreview}
              <div class="object-scale-down w-40" transition:slide>
                <img class="shadow-lg rounded-lg" src={logoPreview} alt="company logo" />
                <Button
                  variant="destructive"
                  class="text-xs mt-2 w-full"
                  type="button"
                  on:click={() => {
                    businessPayload.logo = null;
                    logoPreview = null;
                  }}>
                  <MinusCircleIcon class="mr-2" />
                  Remove
                </Button>
              </div>
            {/if}
          </div>
        </div>
      </form>
    </div>
    <div>
      <img
        width="400px"
        height="400px"
        alt="fill form"
        src="{invoize.plugin_url}public/undraw_fill_form_re_cwyf.svg" />
    </div>
    <!-- <Setup bind:payload /> -->
  </Card.Content>
  {/if}

  {#if currentStep === 1}
  <Card.Header>
    <span class="text-primary-400 text-xs font">Step 2 of 3</span>
    <Card.Title class="text-xl">Additional Configuration</Card.Title>
    <Card.Description>Customize your preferences such as Invoice, currency, and other optional configurations.</Card.Description>
  </Card.Header>
  <Card.Content>
    <div class="w-full">
      <Tabs.Root value="invoice" >
        <Tabs.List>
          <Tabs.Trigger class="w-[150px]" value="invoice">Invoice</Tabs.Trigger>
          <Tabs.Trigger class="w-[150px]" value="currency">Currency</Tabs.Trigger>
          <Tabs.Trigger class="w-[150px]" value="bank">Bank</Tabs.Trigger>
          <Tabs.Trigger class="w-[150px]" value="wc">WooCommerce</Tabs.Trigger>
        </Tabs.List>
        <Tabs.Content value="invoice">
          <div class="border border-gray-200 p-4 rounded-xl">
            <h3 class="font-semibold tracking-tight md:text-lg text-base border-l-2 border-primary pl-2">
              Invoice Setting
            </h3>
            <p class="text-muted-foreground ml-3 md:text-sm text-xs">
              Customize your preferences such as Prefix, Start from number, Due date, Note, and Terms & Conditions.
            </p>
            <div class="mt-6">
              <GeneralSetting/>
            </div>
          </div>
        </Tabs.Content>
        <Tabs.Content value="currency">
          <CurrencySetting />
        </Tabs.Content>
        <Tabs.Content value="bank">
          <div class="border border-gray-200 p-4 rounded-xl">
            <h3 class="font-semibold tracking-tight md:text-lg text-base border-l-2 border-primary pl-2">Bank</h3>
            <p class="text-muted-foreground ml-3 md:text-sm text-xs">
              Add your bank account details to receive payments.
            </p>
            <div class="mt-6">
              <Bank />
            </div>
          </div>
        </Tabs.Content>
        <Tabs.Content value="wc">
          <div class="mt-6">
            <IntegrationSetting />
          </div>
        </Tabs.Content>
      </Tabs.Root>
    </div>
  </Card.Content>
  {/if}
  
  
  {#if currentStep === 2}
  <Card.Content class="p-10 relative">
      <div id="confetti" class=" w-full top-4 left-0 h-[400px] absolute" bind:this={confettiElement}></div>
      <div class="flex justify-center">
        <img
          width="400px"
          height="400px"
          alt="Finish"
          src="{invoize.plugin_url}public/undraw_partying_re_at7f.svg" />
      </div>

      <div class="flex justify-center">
        <h1 class="text-2xl font-bold mb-2">
          You are all set to use Invoize
        </h1>
      </div>
      <div class="flex justify-center">
        <span class="text-sm text-muted-foreground">You are all set to use Invoize</span>
      </div>
      <!-- <div class="flex justify-evenly mt-10">
        <a
          href="https://google.com"
          class="cursor-pointer w-1/4 text-center bg-white border border-gray-300 p-4 rounded-lg hover:bg-slate-100 hover:text-black flex transition-colors ease-in-out duration-300 shadow-inner">
          <FileTextIcon color="#000000" class="mr-2" />
          Read Documentation
        </a>
        <a
          href="https://google.com"
          class="cursor-pointer w-1/4 text-center bg-white border border-gray-300 p-4 rounded-lg hover:bg-slate-100 hover:text-black flex transition-colors ease-in-out duration-300 shadow-inner">
          <Globe color="#000000" class="mr-2" />
          Visit Our Website
        </a>
        <a
          href="https://google.com"
          class="cursor-pointer w-1/4 text-center bg-white border border-gray-300 p-4 rounded-lg hover:bg-slate-100 hover:text-black flex transition-colors ease-in-out duration-300 shadow-inner">
          <BarChart color="#000000" class="mr-2" />
          Go to Dashboard</a>
      </div> -->
      <!-- <Setup bind:payload /> -->
    </Card.Content>
    <!-- <Finished /> -->
  {/if}
</Card.Root>

<div class="xl:w-[1000px] md:w-[700px] mx-auto flex justify-between mt-5">
  {#if currentStep !== 0}
    <!-- Back Button -->
    <!-- only show on NOT first page-->
    <Button class="" variant="link" on:click={toPrevStep}>Back</Button>
  {:else}
    <!-- For balancing the button -->
    <div class="invisible"></div>
  {/if}
  <div>
    <!-- Continue/Finish Button -->
    <Button class="w-40 h-10" on:click={toNextStep} disabled={isLoading} variant="outline">
      {#if isLoading}
        <Loader2 class="h-4 w-4 mr-2 animate-spin" />
        Saving
      {:else}
        {buttonText}
      {/if}
    </Button> 
  </div>
</div>
<Toaster position="bottom-right" />

<style>
  :global(#wpcontent, #wpbody, #wpwrap) {
    background-color: white !important;
  }
</style>