<script>
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import {
    Dialog,
    DialogHeader,
    DialogTitle,
    DialogContent,
    DialogDescription,
  } from "$lib/components/ui/dialog";
  import {
    Card,
    CardHeader,
    CardDescription,
    CardTitle,
    CardContent,
  } from "$lib/components/ui/card";
  import {
    Table,
    Row,
    Body,
    Cell,
    Head,
    Header,
    Caption,
  } from "$lib/components/ui/table";
  import { File, List, Loader2, Mail, MailIcon } from "lucide-svelte";
  import { onMount } from "svelte";
  import { hasEmailTemplateTabSettings } from "$lib/stores/settings-store";
  import { emailTemplates } from "$lib/stores/settings-store";
  import Button from "$lib/components/ui/button/button.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Input from "$lib/components/ui/input/input.svelte";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import toast from "svelte-french-toast";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { isValidEmail } from "$lib/helpers/validationHelper";
  import Tags from "$lib/components/custom-ui/Tags.svelte";
  import {
    Tabs,
    TabsList,
    TabsTrigger,
    TabsContent,
  } from "$lib/components/ui/tabs";

  let selectedTemplate = {};
  let isLoading = false;
  let mailTemplateEditModalOpen = false;

  const toggleModal = (template) => {
    selectedTemplate = template;
    mailTemplateEditModalOpen = true;
  };

  const saveTemplate = () => {
    for (let i = 0; i < selectedTemplate.cc.length; i++) {
      if (!isValidEmail(selectedTemplate.cc[i])) {
        toast.error(
          "Invalid email address, Please enter a valid email address for CC",
        );
        return;
      }
    }

    for (let i = 0; i < selectedTemplate.bcc.length; i++) {
      if (!isValidEmail(selectedTemplate.bcc[i])) {
        toast.error(
          "Invalid email address, Please enter a valid email address for BCC",
        );
        return;
      }
    }

    if (isLoading) {
      return;
    }
    isLoading = true;
    const payload = {
      [selectedTemplate.key]: JSON.stringify({
        name: selectedTemplate.name,
        subject: selectedTemplate.subject,
        body: selectedTemplate.content,
        cc: selectedTemplate?.cc.join(","),
        bcc: selectedTemplate?.bcc.join(","),
      }),
    };

    toast.promise(createPostRequest("settings/update?tab=templates", payload), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        mailTemplateEditModalOpen = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        mailTemplateEditModalOpen = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  const getTemplatesSettings = async () => {
    isLoading = true;
    try {
      const response = await createGetRequest(
        "settings/retrieve?tab=templates",
      );

      $emailTemplates = response.data.data.map((item) => {
        let rawTemplate = JSON.parse(item.value);
        let template = {
          key: item.name,
          name: rawTemplate.name,
          subject: rawTemplate.subject,
          content: rawTemplate.body.trim(),
          cc: [],
          bcc: [],
        };

        if (rawTemplate.cc?.length) {
          template.cc = rawTemplate.cc.includes(",")
            ? rawTemplate.cc.split(",")
            : [rawTemplate.cc];
        }

        if (rawTemplate.bcc?.length) {
          template.bcc = rawTemplate.bcc.includes(",")
            ? rawTemplate.bcc.split(",")
            : [rawTemplate.bcc];
        }

        return template;
      });
      isLoading = false;
      $hasEmailTemplateTabSettings = true;
    } catch (err) {
      isLoading = false;
      handleError(err, "Failed to retrieve settings data.");
    }
  };

  onMount(() => {
    !$hasEmailTemplateTabSettings && getTemplatesSettings();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2"
      >E-Mail Notification</CardTitle
    >
    <CardDescription class="ml-3 md:text-sm text-xs">
      This is where you can configure your e-mail template settings.
    </CardDescription>
  </CardHeader>
  <CardContent class="md:px-6 md:pb-6 p-0">
    <Tabs class="mb-2">
      <TabsList>
        <TabsTrigger value="invoice">
          <div class="flex flex-row items-center">
            <List class="w-4 h-4 mr-2" />
            <div class="text-start">Invoice</div>
          </div>
        </TabsTrigger>
        <TabsTrigger value="quotation">
          <List class="w-4 h-4 mr-2" />
          <div class="text-start">Quotation</div>
        </TabsTrigger>
      </TabsList>
      <TabsContent value="invoice">
        <div>
          {#if isLoading}
            <div class="space-y-3 rounded-md border p-3">
              {#each [1, 2, 3, 4, 5] as _}
                <Skeleton class="h-4 w-full" />
              {/each}
            </div>
          {:else}
            <div class="bg-secondary p-4 rounded-lg">
              <Table class="bg-white rounded-lg md:text-sm text-xs">
                <Caption class="md:text-sm text-xs"
                  >List of email templates used to send emails to users.</Caption
                >
                <Header class="border-b-secondary border-b-4">
                  <Row>
                    <Head class="w-[1px]">No</Head>
                    <Head class="w-[150px]">Name</Head>
                    <Head class="w-[150px] text-center">Actions</Head>
                  </Row>
                </Header>
                <Body>
                  {#each $emailTemplates.filter((item) => !item.key.includes('quotation')) as emailTemplate, i (i)}
                    <Row class="border-b-0">
                      <Cell>{i + 1}</Cell>
                      <Cell>{emailTemplate.name}</Cell>
                      <Cell class="text-center">
                        <EditButton
                          on:click={() => toggleModal(emailTemplate)}
                        />
                      </Cell>
                    </Row>
                  {/each}
                </Body>
              </Table>
            </div>
          {/if}
        </div>
      </TabsContent>
      <TabsContent value="quotation">
        <div>
          {#if isLoading}
            <div class="space-y-3 rounded-md border p-3">
              {#each [1, 2, 3, 4, 5] as _}
                <Skeleton class="h-4 w-full" />
              {/each}
            </div>
          {:else}
            <div class="bg-secondary p-4 rounded-lg">
              <Table class="bg-white rounded-lg md:text-sm text-xs">
                <Caption class="md:text-sm text-xs"
                  >List of email templates used to send emails to users.</Caption
                >
                <Header class="border-b-secondary border-b-4">
                  <Row>
                    <Head class="w-[1px]">No</Head>
                    <Head class="w-[150px]">Name</Head>
                    <Head class="w-[150px] text-center">Actions</Head>
                  </Row>
                </Header>
                <Body>
                  {#each $emailTemplates.filter((item) => item.key.includes('quotation')) as emailTemplate, i (i)}
                    <Row class="border-b-0">
                      <Cell>{i + 1}</Cell>
                      <Cell>{emailTemplate.name}</Cell>
                      <Cell class="text-center">
                        <EditButton
                          on:click={() => toggleModal(emailTemplate)}
                        />
                      </Cell>
                    </Row>
                  {/each}
                </Body>
              </Table>
            </div>
          {/if}
        </div>
      </TabsContent>
    </Tabs>
  </CardContent>
</Card>

<!-- Edit modal -->
<Dialog
  open={mailTemplateEditModalOpen}
  onOpenChange={() => (mailTemplateEditModalOpen = false)}
>
  <DialogContent class="sm:max-w-2xl">
    <DialogHeader>
      <DialogTitle>Mail Template</DialogTitle>
      <DialogDescription>
        Edit the mail template for {selectedTemplate.name}
      </DialogDescription>
    </DialogHeader>
    <div class="grid gap-4 py-4">
      <form on:submit|preventDefault={saveTemplate}>
        <div class="grid mb-3 w-full items-center gap-1.5">
          <Label for="name">Name</Label>
          <Input
            type="text"
            id="name"
            disabled
            bind:value={selectedTemplate.name}
          />
        </div>
        <div class="grid mb-3 w-full items-center gap-1.5">
          <Label for="Subject">Subject</Label>
          <Input
            type="text"
            id="Subject"
            class="select-auto"
            required
            bind:value={selectedTemplate.subject}
          />
        </div>
        <div class="grid mb-3 w-full items-center gap-1.5">
          <Label for="Subject">CC</Label>
          <Tags
            name="{selectedTemplate.name}_cc"
            bind:value={selectedTemplate.cc}
          />
        </div>
        <div class="grid mb-3 w-full items-center gap-1.5">
          <Label for="Subject">BCC</Label>
          <Tags
            name="{selectedTemplate.name}_bcc"
            bind:value={selectedTemplate.bcc}
          />
        </div>
        <div class="grid mb-3 w-full items-center gap-1.5">
          <Label for="content">Content</Label>
          <Textarea
            id="content"
            rows={10}
            required
            bind:value={selectedTemplate.content}
          />
        </div>

        <Button class="w-full" type="submit" disabled={isLoading}>
          {#if isLoading}
            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
            Saving
          {:else}
            Save changes
          {/if}
        </Button>
      </form>
      <div class="grid text-sm border-2 p-4 rounded-lg bg-slate-50">
        <div class="grid grid-cols-3 gap-1">
          <span class="font-medium">Variable</span>
          <span class="font-medium">Description</span>
          <span></span>

          <span class="text-slate-500"
            >&#123;&#123; business_name &#125;&#125;</span
          >
          <span class="text-slate-700">Business name</span>
          <span></span>

          <span class="text-slate-500"
            >&#123;&#123; client_name &#125;&#125;</span
          >
          <span class="text-slate-700">Client name</span>
          <span></span>

          <span class="text-slate-500"
            >&#123;&#123; invoice_number &#125;&#125;</span
          >
          <span class="text-slate-700">Invoice Number</span>
          <span></span>

          <span class="text-slate-500"
            >&#123;&#123; invoice_date &#125;&#125;</span
          >
          <span class="text-slate-700">Date Invoice Created</span>
          <span></span>

          <span class="text-slate-500">&#123;&#123; due_date &#125;&#125;</span>
          <span class="text-slate-700">Invoice Due Date</span>
          <span></span>
        </div>
      </div>
    </div>
  </DialogContent>
</Dialog>
