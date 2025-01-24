<script>
  import {
    Select,
    SelectItem,
    SelectValue,
    SelectContent,
    SelectTrigger,
    SelectSeparator,
  } from "$lib/components/ui/select";
  import Label from "$lib/components/ui/label/label.svelte";

  export let id;
  export let label;
  export let placeholder;
  export let selectionList;
  export let onSelectedChange;
  export let canCreateSelection = false;
  export let createSelectionValue = "";
  export let selected = null;
  export let triggerClass = "";
  export let open = false;
  export let modalOpen = false;
  export let disabled = false;

  const openCreateModal = () => {
    open = false;
    modalOpen = true;
  };
</script>

<!-- Basic Select. If you need checkbox before showing the Select, then use Select-2 component -->
<Label for={id} class="md:text-sm text-xs">{label}</Label>
<Select required {onSelectedChange} {selected} {open} {disabled}>
  <SelectTrigger {id} class="{triggerClass} md:text-sm text-xs">
    <SelectValue {placeholder} />
  </SelectTrigger>
  <SelectContent class="max-h-60 overflow-y-auto">
    {#if canCreateSelection}
      <SelectItem value={createSelectionValue} class="md:text-sm text-xs" on:click={openCreateModal}
        >+ Create new</SelectItem>
      <SelectSeparator class="mx-auto w-11/12" />
    {/if}
    {#each selectionList as selection}
      <SelectItem value={selection.id} label={selection.name} class="md:text-sm text-xs">
        {selection.name}
      </SelectItem>
    {/each}
  </SelectContent>
</Select>
