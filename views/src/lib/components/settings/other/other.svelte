<script>
  import { Button } from "$lib/components/ui/button";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Label } from "$lib/components/ui/label";
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { Loader2 } from "lucide-svelte";
  import { onMount } from "svelte";
  import { otherSettings } from "$lib/stores/settings-store";
  import { saveOtherTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import { slide } from "svelte/transition";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import toast from "svelte-french-toast";

  let checkedEnableLog = false;
  let selectedKeepLogsFor = { label: null, value: null };

  let isLoading = false;
  let isFetching = false;
  let payload = {
    log: {
      enableLog: false,
      keepLogsFor: "forever",
    },
  };

  const keepLogsForOptions = [
    { label: "Forever", value: "forever" },
    { label: "1 Month", value: "1-month" },
    { label: "3 Months", value: "3-month" },
    { label: "6 Months", value: "6-month" },
  ];

  const updateKeepLogsFor = (e) => {
    selectedKeepLogsFor = e;
    payload.log.keepLogsFor = e.value;
  };

  const updateEnableLog = (e) => {
    checkedEnableLog = e;
    payload.log.enableLog = e;
  };

  const saveFromStoreToInput = () => {
    console.log($otherSettings);
    // @ts-ignore
    checkedEnableLog = $otherSettings.log.enableLog === "true" || $otherSettings.log.enableLog === true ? true : false;
    const keepLogsFor = keepLogsForOptions.find((item) => item.value === $otherSettings.log.keepLogsFor);
    if (keepLogsFor) {
      selectedKeepLogsFor = keepLogsFor;
    }
  };

  const getSetting = async () => {
    try {
      isFetching = true;
      const response = await createGetRequest("settings/retrieve?tab=other");
      console.log({ response: response.data.data });
      saveOtherTabToSettingsStore(response.data.data);
      saveFromStoreToInput();
      isFetching = false;
    } catch (err) {
      isFetching = false;
      isLoading = false;
      handleError(err, "Failed to retrieve settings data");
    }
  };

  const submit = () => {
    console.log(payload);
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=other", payload), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        return "Setting saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };
  onMount(() => {
    getSetting();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0 mb-2">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Log Setting</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">
      Logs can be found in logs folder in this plugin's folder
    </CardDescription>
  </CardHeader>
  <CardContent>
    {#if isFetching}
      <Loader2 class="h-5 w-5 text-primary animate-spin" />
    {:else}
      <form on:submit|preventDefault="{submit}" class="flex flex-col gap-6">
        <div class="flex flex-col gap-4">
          <div class="flex flex-nowrap gap-2 items-center w-80">
            <Checkbox id="enable-log-setting" checked="{checkedEnableLog}" onCheckedChange="{updateEnableLog}" />
            <Label for="enable-log-setting" class="md:text-sm text-xs">Enable Logs</Label>
          </div>
          {#if checkedEnableLog}
            <div transition:slide class="flex flex-col flex-nowrap gap-2 items-start w-80">
              <Label for="keep-log-for" class="md:text-sm text-xs">Keep Logs For</Label>
              <Select selected="{selectedKeepLogsFor}" onSelectedChange="{updateKeepLogsFor}">
                <SelectTrigger id="keep-log-for">
                  <SelectValue placeholder="Choose options" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="forever">Forever</SelectItem>
                  <SelectItem value="1-month">1 month</SelectItem>
                  <SelectItem value="3-month">3 months</SelectItem>
                  <SelectItem value="6-month">6 months</SelectItem>
                </SelectContent>
              </Select>
            </div>
          {/if}
        </div>

        <Button type="submit" class="w-fit" disabled="{isLoading}">
          {#if isLoading}
            <Loader2 class="h-4 w-4 text-white animate-spin mr-1" />
            Saving
          {:else}
            Save Changes
          {/if}
        </Button>
      </form>
    {/if}
  </CardContent>
</Card>
