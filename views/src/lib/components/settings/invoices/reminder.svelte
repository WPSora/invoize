<script>
  import {
    AlertDialog,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { Separator } from "$lib/components/ui/separator";
  import { Card, CardHeader, CardDescription, CardTitle, CardContent } from "$lib/components/ui/card";
  import { Table, Row, Body, Cell, Head, Header, Caption } from "$lib/components/ui/table";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import { Plus } from "radix-icons-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import { emptyData, isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { isProPopupOpen, isPro, reminders, reminderGroups, defaultReminderGroup } from "$lib/stores/settings-store";
  import { Check, Loader2 } from "lucide-svelte";
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import DeleteButton from "$lib/components/custom-ui/DeleteButton.svelte";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import ReminderModal from "$lib/components/invoice/add-reminder-modal.svelte";
  import ReminderGroupModal from "$lib/components/invoice/add-reminder-group-modal.svelte";
  import toast from "svelte-french-toast";
  import { onMount } from "svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import UpgradeToProButton from "$lib/components/upgrade-to-pro/UpgradeToProButton.svelte";

  let isLoadingReminder = false;
  let isReminderModalOpen = false;
  let isAlertDialogOpen = false;
  let selectedReminderIndex;
  let editInterval;
  let isEditing = false;

  let isLoadingReminderGroup = false;
  let isReminderGroupModalOpen = false;
  let isAlertDialogGroupOpen = false;
  let selectedReminderGroupIndex;
  let isLoadingDefaultButton = false;
  let defaultButtonState = [];

  $: isEmptyReminder = isEmptyCheck($reminders);

  const addReminderHandler = () => {
    isReminderModalOpen = true;
    isEditing = false;
  };

  const addReminderGroupHandler = () => {
    isReminderGroupModalOpen = true;
  };

  const editReminderHandler = (reminder, i) => {
    editInterval = reminder.split(" ")[0];
    isReminderModalOpen = true;
    isEditing = true;
    selectedReminderIndex = i;
  };

  const deleteReminderHandler = (i) => {
    isAlertDialogOpen = true;
    selectedReminderIndex = i;
  };

  const deleteReminderGroupHanlder = (i) => {
    isAlertDialogGroupOpen = true;
    selectedReminderGroupIndex = i;
  };

  const cancelDeleteReminderHandler = () => {
    selectedReminderIndex = null;
  };

  const cancelDeleteReminderGroupHanlder = () => {
    selectedReminderGroupIndex = null;
  };

  const setDefaultButtonState = () => {
    $reminderGroups.map((r) => defaultButtonState.push(false));
  };

  const deleteReminder = () => {
    if (isLoadingReminder) {
      return;
    }
    isLoadingReminder = true;
    let payload = $reminders.filter((_, i) => i !== selectedReminderIndex);

    if (payload.length === 0) {
      payload = [...emptyData];
    }

    toast.promise(createPostRequest("settings/update?tab=invoice", { reminders: payload }), {
      loading: "Saving...",
      success: () => {
        $reminders = payload;
        isLoadingReminder = false;
        isAlertDialogOpen = false;
        return "Reminder deleted";
      },
      error: (err) => {
        isLoadingReminder = false;
        isAlertDialogOpen = false;
        return handleError(err, "Failed to delete reminder.");
      },
    });
  };

  const deleteReminderGroup = () => {
    if (isLoadingReminderGroup) {
      return;
    }
    isLoadingReminderGroup = true;

    // remove the selected reminder group from the list
    let filteredReminderGroup = $reminderGroups.filter((_, i) => i !== selectedReminderGroupIndex);

    // if empty, set it to default empty data
    if (filteredReminderGroup.length === 0) {
      filteredReminderGroup = [...emptyData];
    }

    let payload = {
      reminderGroups: filteredReminderGroup,
    };

    // check if the deleted reminder group is the default reminder group.
    // If so, then update the defaultReminderGroup to empty data
    const deletedReminderGroup = $reminderGroups.find((_, i) => i === selectedReminderGroupIndex);
    if (deletedReminderGroup.name === $defaultReminderGroup.name) {
      payload = { ...payload, defaultReminderGroup: emptyData };
    }

    toast.promise(createPostRequest("settings/update?tab=invoice", payload), {
      loading: "Saving...",
      success: () => {
        $reminderGroups = payload.reminderGroups;
        $defaultReminderGroup = payload?.defaultReminderGroup ?? $defaultReminderGroup;
        isLoadingReminderGroup = false;
        isAlertDialogGroupOpen = false;
        return "Reminder deleted";
      },
      error: (err) => {
        isLoadingReminderGroup = false;
        isAlertDialogGroupOpen = false;
        return handleError(err, "Failed to delete reminder group");
      },
    });
  };

  const setDefaultReminderGroup = (reminderGroup, i) => {
    defaultButtonState[i] = true;
    isLoadingDefaultButton = true;
    toast.promise(createPostRequest("settings/update?tab=invoice", { defaultReminderGroup: reminderGroup }), {
      loading: "Saving...",
      success: () => {
        defaultButtonState[i] = false;
        isLoadingDefaultButton = false;
        $defaultReminderGroup = reminderGroup;
        return "Default reminder group updated";
      },
      error: (err) => {
        defaultButtonState[i] = false;
        isLoadingDefaultButton = false;
        return handleError(err, "Failed to update default reminder group.");
      },
    });
  };

  onMount(() => {
    setDefaultButtonState();
  });
</script>

<Card class="w-ful flex xl:flex-row flex-col p-4 gap-y-10">
  <!-- Reminder -->
  <div class="flex-1">
    <CardHeader class="space-y-0.5 md:p-6 px-0 flex flex-row justify-between">
      <div>
        <CardTitle class="tmd:text-lg text-base border-l-2 border-primary pl-2">Reminder</CardTitle>
        <CardDescription class="ml-3 md:text-sm text-xs">Manage reminder options for your invoices.</CardDescription>
      </div>
      <div class="flex flex-nowrap mt-4 ml-2">
        <Button
          variant="default"
          class="hover:shadow-lg hover:shadow-primary/80 transition-all w-full sm:w-fit"
          on:click={addReminderHandler}>
          <Plus />
          <div class="ml-1">New Reminder</div>
        </Button>
      </div>
    </CardHeader>
    <CardContent class="md:px-6 md:pb-6 p-0">
      {#if isLoadingReminder}
        {#each $reminders as _}
          <Skeleton class="w-full h-8 mb-2 my-8" />
        {/each}
        <Skeleton class="w-32 h-8 mb-2 mt-12" />
      {:else}
        <!-- if first value name is none, it means it has no saved tax -->
        {#if !isEmptyCheck($reminders)}
          <div class="bg-secondary p-4 rounded-lg">
            <Table class="bg-white rounded-lg md:text-sm text-xs">
              <Caption class="md:text-sm text-xs">List of reminders used on your invoice.</Caption>
              <Header class="border-b-secondary border-b-4">
                <Row>
                  <Head>#</Head>
                  <Head class="text-center">Reminder</Head>
                  <Head class="text-center">Actions</Head>
                </Row>
              </Header>
              <Body>
                {#each $reminders as reminder, i}
                  <Row class="border-b-0 md:space-y-0 space-y-1">
                    <Cell>
                      <div>{i + 1}</div>
                    </Cell>
                    <Cell class="text-center">
                      <div>{pluralHelper(reminder)}</div>
                    </Cell>
                    <Cell class="text-center">
                      <EditButton on:click={() => editReminderHandler(reminder, i)} />
                      <DeleteButton on:click={() => deleteReminderHandler(i)} />
                    </Cell>
                  </Row>
                {/each}
              </Body>
            </Table>
          </div>
        {:else}
          <Separator />
          <div class="italic text-sm text-muted-foreground mt-4 mb-8">
            You have no default reminder configuration saved.
          </div>
        {/if}
      {/if}
    </CardContent>
  </div>

  <!-- Reminder Group -->
  <div class="flex-1">
    <CardHeader class="space-y-0.5 md:p-6 px-0 flex flex-row justify-between">
      <div>
        <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Reminder Group</CardTitle>
        <CardDescription class="ml-3 md:text-sm text-xs">Manage reminder group for your invoices.</CardDescription>
      </div>
      <div class="flex flex-nowrap mt-4 ml-2">
        {#if $isPro}
          <Button
            variant="default"
            disabled={isEmptyReminder}
            class="hover:shadow-lg hover:shadow-primary/80 transition-all w-full sm:w-fit"
            on:click={addReminderGroupHandler}>
            {#if isEmptyReminder}
              <div class="ml-1">No Reminder</div>
            {:else}
              <Plus />
              <div class="ml-1">New Reminder Group</div>
            {/if}
          </Button>
        {:else}
          <UpgradeToProButton bind:isProPopupOpen={$isProPopupOpen} customText="Pro Only" /> 
        {/if}
      </div>
    </CardHeader>
    <CardContent class="md:p-6 p-0">
      {#if isLoadingReminderGroup}
        {#each $reminderGroups as _}
          <Skeleton class="w-full h-8 mb-2 my-8" />
        {/each}
        <Skeleton class="w-32 h-8 mb-2 mt-12" />
      {:else}
        <!-- if first value name is none, it means it has no saved tax -->
        {#if !isEmptyCheck($reminderGroups) && $isPro}
          <div class="bg-secondary p-4 rounded-lg">
            <Table class="bg-white rounded-lg md:text-sm text-xs">
              <Caption class="md:text-sm text-xs">List of reminder groups used on your invoice.</Caption>
              <Header class="border-b-secondary border-b-4">
                <Row>
                  <Head>#</Head>
                  <Head class="text-center">Reminder Group</Head>
                  <Head class="text-center">Actions</Head>
                </Row>
              </Header>
              <Body>
                {#each $reminderGroups as reminder, i}
                  <Row class="border-b-0 h-16">
                    <Cell>
                      <div>{i + 1}</div>
                    </Cell>
                    <Cell>
                      <div>{reminder.name}</div>
                      <!-- Reminder Before -->
                      {@const reminderListBefore = reminder.value?.before?.sort().map((r) => {
                        return " " + pluralHelper(r);
                      })}
                      <div class="text-muted-foreground text-xs">Before: {reminderListBefore ?? ""}</div>
                      <!-- Reminder After -->
                      {@const reminderListAfter = reminder.value?.after?.sort().map((r) => {
                        return " " + pluralHelper(r);
                      })}
                      <div class="text-muted-foreground text-xs">After: {reminderListAfter ?? ""}</div>
                    </Cell>
                    <Cell class="text-center flex md:flex-row flex-col items-center gap-y-1 justify-center gap-x-1">
                      {#if $defaultReminderGroup.name === reminder.name}
                        <Button variant="outline" disabled class="text-xs text-green-500">
                          <Check class="mr-1 h-4 w-4" />
                          Default
                        </Button>
                      {:else}
                        <Button
                          variant="outline"
                          disabled={isLoadingDefaultButton}
                          class="text-xs"
                          on:click={() => setDefaultReminderGroup(reminder, i - 1)}>
                          {#if defaultButtonState[i - 1]}
                            <Loader2 class="mr-2 w-4 h-4 animate-spin" />
                            Setting to Default
                          {:else}
                            Set as Default
                          {/if}
                        </Button>
                      {/if}
                      <DeleteButton on:click={() => deleteReminderGroupHanlder(i)} />
                    </Cell>
                  </Row>
                {/each}
              </Body>
            </Table>
          </div>
        {:else}
          <Separator />
          <div class="italic text-sm text-muted-foreground mt-4 mb-8">You have no default reminder group saved.</div>
        {/if}
      {/if}
    </CardContent>
  </div>
</Card>

<!-- Create and edit reminder modal -->
<ReminderModal
  bind:open={isReminderModalOpen}
  bind:isLoading={isLoadingReminder}
  bind:selectedReminderIndex
  bind:isEditing
  bind:editInterval />

<ReminderGroupModal
  bind:isOpen={isReminderGroupModalOpen}
  bind:isLoading={isLoadingReminderGroup}
  bind:defaultButtonState
  {setDefaultReminderGroup} />

<!-- Delete reminder modal -->
<AlertDialog bind:open={isAlertDialogOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel on:click={cancelDeleteReminderHandler}>Cancel</AlertDialogCancel>
      <Button disabled={isLoadingReminder} variant="destructive" on:click={deleteReminder}>
        {#if isLoadingReminder}
          <Loader2 class="w-4 h-4 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>

<!-- Delete reminder group modal -->
<AlertDialog bind:open={isAlertDialogGroupOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel on:click={cancelDeleteReminderGroupHanlder}>Cancel</AlertDialogCancel>
      <Button disabled={isLoadingReminderGroup} variant="destructive" on:click={deleteReminderGroup}>
        {#if isLoadingReminderGroup}
          <Loader2 class="w-4 h-4 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
