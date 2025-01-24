<script>
  import { RadioGroup, RadioGroupInput, RadioGroupItem } from "$lib/components/ui/radio-group";
  import { CheckIcon, Loader, SaveIcon } from "lucide-svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";

  let submittingForm;
  let testingConnection;
  let loading = submittingForm || testingConnection;

  let payload = {
    smtp_host: "",
    smtp_protocol: "",
    smtp_port: "",
    smtp_username: "",
    smtp_password: "",
  };

  function sendTestEmail() {
    testingConnection = true;
  }

  function submitForm() {}
  async function testConnection() {}
</script>

<form class="w-1/2 space-y-4" on:submit|preventDefault={() => submitForm()}>
  <div class="space-y-2">
    <Label for="smtp-host">SMTP Host</Label>
    <Input required type="text" id="smtp-host" bind:value={payload.smtp_port} />
  </div>
  <div class="space-y-2">
    <Label for="smtp-protocol">SMTP Protocol</Label>
    <RadioGroup id="smtp-protocol" bind:value={payload.smtp_protocol}>
      <div class="flex items-center space-x-2">
        <RadioGroupItem value="ssl" id="ssl-option" />
        <Label for="ssl-option">SSL</Label>
      </div>
      <div class="flex items-center space-x-2">
        <RadioGroupItem value="tls" id="tls-option" />
        <Label for="tls-option">TLS</Label>
      </div>
      <RadioGroupInput required name="smtp_account" bind:value={payload.smtp_protocol} />
    </RadioGroup>
  </div>
  <div class="space-y-2">
    <Label for="smtp-port">SMTP Port</Label>
    <Input required type="text" id="smtp-port" bind:value={payload.smtp_port} />
  </div>
  <div class="space-y-2">
    <Label for="smtp-username">SMTP Username</Label>
    <Input required type="email" id="smtp-username" bind:value={payload.smtp_username} />
  </div>
  <div class="space-y-2">
    <Label for="smtp-password">SMTP Password</Label>
    <Input required type="password" id="smtp-password" bind:value={payload.smtp_password} />
  </div>
  <div class="flex space-x-2">
    <Button disabled={loading}>
      {#if submittingForm}
        <Loader class="mr-2 h-4 w-4 animate-spin" />
      {:else}
        <SaveIcon class="mr-2 h-4 w-4" />
      {/if}
      Save
    </Button>
    <Button disabled={loading} variant="secondary">
      {#if testingConnection}
        <Loader class="mr-2 h-4 w-4 animate-spin" />
      {:else}
        <CheckIcon class="mr-2 h-4 w-4" />
      {/if}
      Test Connection
    </Button>
  </div>
</form>
