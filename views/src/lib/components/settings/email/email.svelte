<script>
  import { createPostRequest } from "$lib/helpers/request";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Tabs, TabsList, TabsTrigger, TabsContent } from "$lib/components/ui/tabs";
  import { onMount } from "svelte";
  import { WordpressBrand } from "svelte-awesome-icons";
  import { Loader2 } from "lucide-svelte";
  import { emailTabSettingStyle } from "$lib/common/styles";
  import { hasAutomationTabSettings } from "$lib/stores/settings-store";
  import { sendEmail, reminderOnDueDate, remindersBefore } from "$lib/stores/settings-store";
  import { activeTab2 } from "$lib/stores/settings-store";
  import EmailTemplateList from "$lib/components/tables/email-templates/email-template-list.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import Checkbox from "$lib/components/ui/checkbox/checkbox.svelte";
  import toast from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";
  import * as Alert from "$lib/components/ui/alert";

  const emailSmtp = {
    default: "default",
    pro: "pro",
  };

  let isLoading = false;
  let selectedEmailSmtp = emailSmtp.default;
  let payload = {
    automation: {
      sendEmail: {
        paid: false,
        cancel: false,
        expired: false,
      },
      reminderOnDueDate: false,
      /**
       * @type {number[]}
       */
      remindersBefore: [],
    },
    smtp: {
      smtp_host: "",
      smtp_protocol: "",
      smtp_port: "",
      smtp_username: "",
      smtp_password: "",
    },
    templates: {},
  };

  const saveFromApiToStore = (res) => {
    const settings = {};
    res.map((item) => {
      settings[item.name] = item.value;
    });

    if (settings.sendEmail) {
      $sendEmail = settings.sendEmail;
    }
    $reminderOnDueDate = settings?.reminderOnDueDate === "true";
    $remindersBefore = settings?.remindersBefore?.map(Number) ?? [];
  };

  const saveFromInputToStore = (data) => {
    // we make if like this so that if sendEmail is null, then it won't overwrite default value on store
    if (data.sendEmail) {
      $sendEmail = data.sendEmail;
    }
    // here if null, we set it to false and empty array because its the default value on store
    $reminderOnDueDate = data?.reminderOnDueDate ?? false;
    $remindersBefore = data?.remindersBefore ?? [];
  };

  const submit = async () => {
    if (isLoading) {
      return;
    }
    isLoading = true;
    toast.promise(createPostRequest(`settings/update?tab=automation`, payload.automation), {
        loading: "Saving...",
        success: () => {
          saveFromInputToStore(payload.automation);
          isLoading = false;
          return "Settings saved!";
        },
        error: (err) => {
          isLoading = false;
          return handleError(err, "Failed to save settings");
        },
      });
  };



  onMount(() => {
    !$hasAutomationTabSettings;
  });
</script>

<Tabs bind:value={$activeTab2}>
  <TabsList class="bg-background h-12 w-full hidden md:flex justify-around p-6 rounded-xl">
    <!-- <TabsTrigger value="automation" class={emailTabSettingStyle}>Automation</TabsTrigger> -->
    <TabsTrigger value="smtp" class={emailTabSettingStyle}>SMTP Account</TabsTrigger>
    <TabsTrigger value="templates" class={emailTabSettingStyle}>Templates</TabsTrigger>
  </TabsList>

  <!-- Automation -->
  <TabsContent value="automation">
    <Card class="p-4">
      <CardHeader class="space-y-0.5 md:p-6 px-0">
        <CardTitle class="md:text-sm text-base border-l-2 border-primary pl-2">E-Mail</CardTitle>
        <CardDescription class="ml-3 md:text-base text-xs">
          This is where you can configure your e-mail settings.
        </CardDescription>
      </CardHeader>
      <CardContent class="md:px-6 md:pb-6 p-0">
        <form on:submit|preventDefault={() => submit()} class="space-y-4">
          <div class="rounded-lg border-border border bg-secondary p-6">
            <div>
              <Label class="md:text-base text-sm cursor-default">Auto send e-mail</Label>
              <span class="block text-gray-500 text-xs">
                Send notification automatically when invoice status changed.
              </span>
            </div>
            <div class="flex sm:flex-row flex-col gap-y-6 justify-between mt-3 md:w-[400px]">
              <div class="col-span-2 flex space-x-2">
                <Checkbox id="status-paid" value="true" bind:checked={payload.automation.sendEmail.paid} />
                <Label
                  for="status-paid"
                  class="md:text-sm text-xs font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                  Paid
                </Label>
              </div>
              <div class="col-span-2 flex space-x-2">
                <Checkbox id="status-cancel" value="true" bind:checked={payload.automation.sendEmail.cancel} />
                <Label
                  for="status-cancel"
                  class="md:text-sm text-xs font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                  Cancel
                </Label>
              </div>
              <div class="col-span-1 flex space-x-2">
                <Checkbox id="status-expired" value="true" bind:checked={payload.automation.sendEmail.expired} />
                <Label
                  for="status-expired"
                  class="md:text-sm text-xs font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                  Expired
                </Label>
              </div>
            </div>
          </div>

          <div class="rounded-lg border-border border bg-secondary p-6">
            <div>
              <Label class="md:text-base text-sm cursor-default">Reminder on Due Date</Label>
              <span class="block text-xs text-gray-500"> Send reminder on due date. </span>
            </div>
            <div class="flex justify-between mt-3 md:w-[400px]">
              <div class="col-span-3 flex space-x-2">
                <Checkbox id="on-due-date" value="true" bind:checked={payload.automation.reminderOnDueDate} />
                <Label
                  for="on-due-date"
                  class="md:text-sm text-xs font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                  On Due Date
                </Label>
              </div>
            </div>
          </div>

          <div class="mt-5">
            <Button disabled={isLoading}>
              {#if isLoading}
                <Loader2 class="mr-2 w-4 h-4 animate-spin" />
                Saving
              {:else}
                Save changes
              {/if}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </TabsContent>

  <!-- SMTP -->
  <TabsContent value="smtp">
    <Card class="p-4">
      <CardHeader class="space-y-0.5 md:p-6 px-0">
        <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">SMTP Account</CardTitle>
        <CardDescription class="ml-3 md:text-sm text-xs">
          This is where you can configure your SMTP settings.
        </CardDescription>
      </CardHeader>
      <CardContent class="gap-x-8 md:gap-y-0 gap-y-6 md:px-6 md:pb-6 p-0">
        <div class="mb-4">
          <Alert.Root class="text-gray-500">
            <Alert.Title>
              <span class="font-bold">
                Tips for setting up SMTP
              </span>
            </Alert.Title>
            <Alert.Description>
              To ensure notifications are sent successfully, Install an SMTP plugin such as <a class="text-primary" href="https://wordpress.org/plugins/fluent-smtp/">Fluent SMTP</a> or <a class="text-primary" href="https://wordpress.org/plugins/smtp-mailer/">SMTP Mailer</a> to ensure your email system works properly in WordPress. If you're experiencing problems with emails not sending properly, consider installing the <a href="https://wordpress.org/plugins/wp-mail-logging/#description" target="_blank" class="text-primary">WP Mail Logging Plugin.</a>
            </Alert.Description>
          </Alert.Root>
        </div>
        <Card
          class="w-1/3 relative hover:bg-secondary cursor-pointer border border-border transition-all hover:shadow-xl {selectedEmailSmtp !==
          emailSmtp.default
            ? ' bg-background'
            : ' bg-gradient-to-r from-primary to-primary-500 text-white'}">
          <CardContent class="p-0 h-20 flex justify-center items-center">
            <div class="flex items-center space-x-2">
              <WordpressBrand />
              <span class="md:text-xl text-base"> Default </span>
            </div>
          </CardContent>
        </Card>
        <!-- <Card
          class="w-1/3 relative border border-border text-slate-600 bg-slate-100 transition-all cursor-default"
          on:click={() => (isProPopupOpen = true)}>
          <CardContent
            class="p-0 h-20 flex justify-center items-center gap-x-2 text-inherit">
            <PhpBrand />
            <span class="md:text-xl text-base"> Custom SMTP Account </span>
            <ProBadge class="text-inherit border-inherit" />
          </CardContent>
        </Card> -->
      </CardContent>
    </Card>
  </TabsContent>

  <!-- Template -->
  <TabsContent value="templates">
    <EmailTemplateList />
  </TabsContent>
</Tabs>
