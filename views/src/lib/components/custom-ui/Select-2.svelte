<script>
  import {
    Select,
    SelectItem,
    SelectValue,
    SelectContent,
    SelectTrigger,
    SelectSeparator,
  } from "$lib/components/ui/select";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { createEventDispatcher } from "svelte";
  import { slide } from "svelte/transition";
  import Label from "$lib/components/ui/label/label.svelte";

  const dispatch = createEventDispatcher();

  export let id;
  export let label;
  export let selected;
  export let placeholder;
  export let selectionListArr = null;
  export let selectionListObj = null;
  export let checked = false;
  export let multiple = false;
  export let canCreateSelection = false;
  export let createSelectionValue = "";
  export let noCheckboxLabel = false;
  /** for custom value */
  export let isDiscount = false;
  /** for custom label */
  export let isCurrency = false;

  let isSelectOpen = false;

  // if checked is true, then user click checkbox,
  // then checked still be true because this run before
  // value change to false
  const resetSelected = () => {
    selected = checked ? null : selected;
  };
</script>

<!-- This component has Checkbox. So after checkbox is checked, then the Select dropdown is shown. -->
<!-- If you don't need the checkbox, then use the other (Select) component -->
<div class="flex flex-col gap-y-2">
  {#if noCheckboxLabel}
    <Label for={id}>{label}</Label>
  {:else}
    <div class="flex flex-row gap-x-2">
      <Checkbox {id} bind:checked on:click={resetSelected} />
      <Label for={id}>{label}</Label>
    </div>
  {/if}
  {#if checked}
    <div transition:slide>
      <Select bind:selected {multiple} bind:open={isSelectOpen}>
        <SelectTrigger {id} class="h-fit">
          <SelectValue {placeholder} />
        </SelectTrigger>
        <SelectContent class="max-h-60 overflow-y-auto">
          {#if canCreateSelection}
            <SelectItem
              value={createSelectionValue}
              on:click={() => {
                dispatch(`open${label}Modal`);
                isSelectOpen = false;
              }}>
              + Create new
            </SelectItem>
            <SelectSeparator class="mx-auto" />
          {/if}
          {#if selectionListObj}
            {#each selectionListObj as selection}
              <SelectItem
                value={!isDiscount ? selection.value : selection.value + "_" + selection.type}
                label={!isCurrency ? selection.name : selection.name + " (" + selection.value + ")"}
                class="flex flex-row justify-between"
                on:click={() => (selected = selected)}>
                <div>{selection.name}</div>
                {#if selection.value && selection.type}
                  <div class="font-bold">
                    {selection.value}{selection.type === "%" ? "%" : ""}
                  </div>
                {/if}
              </SelectItem>
            {/each}
          {:else if selectionListArr}
            {#each selectionListArr as selection}
              <SelectItem value={selection} label={selection}>
                {selection}
              </SelectItem>
            {/each}
          {/if}
        </SelectContent>
      </Select>
    </div>
  {/if}
</div>
