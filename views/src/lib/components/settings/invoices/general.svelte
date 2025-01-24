<script>
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { formatDueDate, setDueDate } from "$lib/helpers/dueDateHelper.js";
  import { handleError } from "$lib/helpers/errorHelper";
  import { removeArrowOnInputStyle } from "$lib/common/styles";
  import CustomSelect from "$lib/components/custom-ui/Select.svelte";
  import { settingDueDateList, paperSize } from "$lib/common/options";
  import {
    hasInvoiceTabSettings,
    prefix,
    dueDateInterval,
    startFromNumber,
    note,
    termsAndConditions,
    downloadPaperSize,
  } from "$lib/stores/settings-store";

  import toast from "svelte-french-toast";
  import { onMount } from "svelte";
  import { saveInvoiceTabToSettingsStore } from "$lib/helpers/saveToStoreHelper";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import { Loader2 } from "lucide-svelte";
  import { loadInvoiceSetting } from "$lib/helpers/settings";

  let isLoading = false;
  let selectedDueDate;
  let selectedDownloadPaperSize;

  let payload = {
    prefix: "#",
    dueDateInterval: null,
    note: "",
    termsAndConditions: "",
    startFromNumber: 1,
    downloadPaperSize: "",
  };

  const saveFromStoreToInput = () => {
    payload.startFromNumber = $startFromNumber;
    payload.prefix = $prefix;
    payload.note = $note;
    payload.termsAndConditions = $termsAndConditions;
    if ($dueDateInterval) {
      selectedDueDate = setDueDate($dueDateInterval);
    }
    payload.downloadPaperSize = $downloadPaperSize;
    selectedDownloadPaperSize = setDownloadPaperSizeInput($downloadPaperSize);
  };

  const saveFromInputToStore = (res) => {
    $prefix = res?.prefix ?? "";
    $dueDateInterval = res?.dueDateInterval ?? "";
    $note = res?.note ?? "";
    $termsAndConditions = res?.termsAndConditions ?? "";
    $downloadPaperSize = res?.downloadPaperSize ?? "";
  };

  const submit = () => {
    if (isLoading) {
      return;
    }
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=invoice", payload), {
      loading: "Saving...",
      success: () => {
        saveFromInputToStore(payload);
        isLoading = false;
        return "Setting Saved";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };

  const handleSelectedDueDate = (e) => {
    payload.dueDateInterval = formatDueDate(e);
  };

  const handleSelectedPaperSize = (e) => {
    payload.downloadPaperSize = e.value;
  };

  const setDownloadPaperSizeInput = (store) => {
    const data = paperSize.find((item) => item.id === store);
    const defaultData = paperSize.find((item) => item.id === "a4");
    return data ? { label: data.name, value: data.id } : { label: defaultData.name, value: defaultData.id };
  };

  const getInvoiceTabSettings = async () => {
    try {
      await loadInvoiceSetting();
      saveFromStoreToInput();
      $hasInvoiceTabSettings = true;
    } catch (err) {
      handleError(err, "Failed to retrieve settings data");
    }
  };

  onMount(() => {
    if(!$hasInvoiceTabSettings) {
      getInvoiceTabSettings();
    } else {
      saveFromStoreToInput();
    }
    // when user first fetch API, this won't save the value to store. We call this again inside saveFromApiToStore.
    // So this only works for whenever user navigate away and back to this page.
  });
</script>

<form on:submit|preventDefault={() => submit()} class="md:space-y-10 space-y-8">
  <div class="flex flex-wrap-reverse md:flex-nowrap gap-8 md:gap-16">
    <!-- Invoice Prefix -->
    <div class="w-full md:w-1/2">
      <Label for="prefix" class="md:text-sm text-xs">Prefix</Label>
      <Input type="text" id="prefix" class="md:text-sm text-xs" disabled={isLoading} bind:value={payload.prefix} />
    </div>

    <!-- Start From Number -->
    <div class="w-full md:w-1/2">
      <Label for="startFromNumber" class="md:text-sm text-xs">Start From Number</Label>
      <Input
        type="number"
        id="startFromNumber"
        class="{removeArrowOnInputStyle} md:text-sm text-xs"
        disabled={isLoading}
        bind:value={payload.startFromNumber} />
    </div>
  </div>

  <div class="flex flex-wrap-reverse md:flex-nowrap gap-8 md:gap-16">
    <!-- Due Date -->
    <div class="w-full md:w-1/2">
      <CustomSelect
        id="due-date"
        label="Default Due Date"
        placeholder="Choose paper size for download"
        selectionList={settingDueDateList}
        selected={selectedDueDate}
        onSelectedChange={handleSelectedDueDate} />
    </div>
    <!-- Download size -->
    <div class="w-full md:w-1/2">
      <CustomSelect
        id="download-paper-size"
        label="Download Paper Size"
        placeholder="Choose download paper size"
        selectionList={paperSize}
        selected={selectedDownloadPaperSize}
        onSelectedChange={handleSelectedPaperSize} />
    </div>
  </div>

  <div class="flex flex-wrap md:flex-nowrap md:gap-16 gap-8">
    <!-- Note -->
    <div class="w-full md:w-1/2 space-y-1">
      <Label class="cursor-default md:text-sm text-xs">Note</Label>
      <Textarea
        id="note"
        disabled={isLoading}
        placeholder="Your invoice note here"
        bind:value={payload.note}
        class="md:h-32 h-60 w-full p-4 md:text-sm text-xs" />
    </div>

    <!-- Terms & Conditions -->
    <div class="w-full md:w-1/2 space-y-1">
      <Label class="cursor-default md:text-sm text-xs">Terms & Conditions</Label>
      <Textarea
        id="note"
        disabled={isLoading}
        placeholder="Your terms & conditions here"
        bind:value={payload.termsAndConditions}
        class="w-full md:h-32 h-60 p-4 md:text-sm text-xs" />
    </div>
  </div>

  <!-- Save button -->
  <div class="mt-5">
    <Button disabled={isLoading} class="md:w-fit w-full">
      {#if isLoading}
        <Loader2 class="mr-2 w-4 h-4 animate-spin" />
        Saving
      {:else}
        Save Changes
      {/if}
    </Button>
  </div>
</form>
