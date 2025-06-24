<script>
  import { Label } from "$lib/components/ui/label";
  import { Input } from "$lib/components/ui/input";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { endTime } from "$lib/common/options";
  import { slide } from "svelte/transition";
  import { dueDateList } from "$lib/common/options";
  import { onMount } from "svelte";
  import { getDefaultRecurring } from "$lib/helpers/setDefaultRecurring";

  export let dueDateInterval = 7;
  export let payload = {
    name: "",
    interval: "",
    start: "",
    end: "",
  };

  let selectedEnd = { value: "never", label: "Never" };
  let selectedInterval = { value: null, label: null };

  let isCustomInterval = false;

  const updateName = (e) => {
    payload.name = e.target.value;
  };

  const updateInterval = (e) => {
    payload.interval = e.value;
  };

  // const updateStartDate = (e) => {
  //   payload.start = e.target.value;
  // };

  const updateEndDate = (e) => {
    payload.end = e.value;
  };

  const updateDueDateInterval = (e) => {
    dueDateInterval = e.value;
    if (e.value === 0) {
      isCustomInterval = true;
    } else {
      isCustomInterval = false;
    }
  };

  const updateCustomDueDateInterval = (e) => {
    dueDateInterval = e.target.value;
  };

  onMount(() => {
    const recurringDefault = getDefaultRecurring();
    payload.start = recurringDefault.start_date;
    payload.end = recurringDefault.end.value;

    selectedEnd = recurringDefault.end;
  });
</script>

<div class="space-y-5">
  <!-- recurring name -->
  <div class="space-y-1">
    <Label for="interval-name">Recurring Name</Label>
    <Input required id="interval-name" type="text" on:input="{updateName}" placeholder="Recurring name" />
  </div>

  <!-- recurring interval -->
  <div class="space-y-1">
    <Label for="interval">Repeat this invoice</Label>
    <Select required onSelectedChange="{updateInterval}">
      <SelectTrigger id="interval">
        <SelectValue placeholder="Choose interval" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="daily" label="Daily">Daily</SelectItem>
        <SelectItem value="weekly" label="Weekly">Weekly</SelectItem>
        <SelectItem value="monthly" label="Monthly">Monthly</SelectItem>
        <SelectItem value="yearly" label="Yearly">Yearly</SelectItem>
      </SelectContent>
    </Select>
  </div>

  <!-- recurring start date -->
  <!-- <div class="space-y-1">
    <Label for="start-date">Start invoice on</Label>
    <Input required type="date" class="relative" id="start-date" on:input="{updateStartDate}" value="{payload.start}" />
  </div> -->

  <!-- recurring end date -->
  <div class="space-y-1">
    <Label for="end">End invoice after</Label>
    <Select required onSelectedChange="{updateEndDate}" selected="{selectedEnd}">
      <SelectTrigger id="at">
        <SelectValue placeholder="Choose end period" />
      </SelectTrigger>
      <SelectContent class="max-h-60 overflow-y-auto">
        {#each endTime as end}
          <SelectItem value="{end.value}" label="{end.label}">{end.label}</SelectItem>
        {/each}
      </SelectContent>
    </Select>
  </div>

  <!-- Due date -->
  <div class="space-y-1">
    <Label for="due-date">Due Date</Label>
    <Select required onSelectedChange="{updateDueDateInterval}" selected="{selectedInterval}">
      <SelectTrigger>
        <SelectValue placeholder="Choose due date interval" />
      </SelectTrigger>
      <SelectContent class="max-h-60 overflow-y-auto">
        {#each dueDateList as selection}
          <SelectItem value="{selection.id}" label="{selection.name}">
            {selection.name}
          </SelectItem>
        {/each}
      </SelectContent>
    </Select>
    {#if isCustomInterval}
      <div class="flex flex-row justify-start items-center w-full gap-2" transition:slide>
        <Input type="number" id="custom-due-date" min="1" on:input="{updateCustomDueDateInterval}" />
        <div class="text-sm pr-2">day(s)</div>
      </div>
    {/if}
  </div>
</div>
