<script>
  import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { createPostRequest } from "$lib/helpers/request";
  import { reminders, reminderGroups } from "$lib/stores/settings-store";
  import { Label } from "$lib/components/ui/label";
  import { Input } from "$lib/components/ui/input";
  import { Button } from "$lib/components/ui/button";
  import { Loader2 } from "lucide-svelte";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { pluralHelper } from "$lib/helpers/pluralHelper";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import toast from "svelte-french-toast";

  export let isOpen = false;
  export let isLoading = false;
  export let defaultButtonState = [];
  export let setDefaultReminderGroup = (reminder, i) => {};

  let reminderGroupName;
  let reminderGroup = {
    before: [],
    after: [],
  };

  const resetAll = () => {
    isOpen = false;
    isLoading = false;
    reminderGroup = {
      before: [],
      after: [],
    };
    reminderGroupName = null;
  };

  const updateSelectedReminders = (reminder, isChecked, isBefore) => {
    if (isChecked) {
      if (isBefore) {
        reminderGroup.before = [...reminderGroup.before, reminder];
      } else {
        reminderGroup.after = [...reminderGroup.after, reminder];
      }
    } else {
      if (isBefore) {
        reminderGroup.before = reminderGroup.before.filter((item) => item !== reminder);
      } else {
        reminderGroup.after = reminderGroup.after.filter((item) => item !== reminder);
      }
    }
  };

  const checkIsExist = () => {
    const isExist = $reminderGroups.find((item) => item.name === reminderGroupName.trim());
    if (isExist) {
      return true;
    }
    return false;
  };

  const checkIsValid = () => {
    if (!reminderGroup.before.length && !reminderGroup.after.length) {
      toast.error("Please select at least one reminder.");
      return false;
    }
    if (checkIsExist()) {
      toast.error("Please use a different name.");
      return false;
    }
    return true;
  };

  const saveReminderGroup = async () => {
    if (!checkIsValid()) {
      return;
    }

    let isNew = false;
    isLoading = true;
    const data = {
      name: reminderGroupName.trim(),
      value: reminderGroup,
    };
    if (isEmptyCheck($reminderGroups)) {
      $reminderGroups = [];
      isNew = true;
    }
    const payload = [...$reminderGroups, data];

    toast.promise(createPostRequest("settings/update?tab=invoice", { reminderGroups: payload }), {
      loading: "Saving reminder group...",
      success: () => {
        if (isNew) {
          defaultButtonState = [0];
          setDefaultReminderGroup(data, 0);
        }
        $reminderGroups = payload;
        resetAll();
        return "Reminder group saved successfully.";
      },
      error: (err) => {
        resetAll();
        return handleError(err, "Failed to save reminder group.");
      },
    });
  };
</script>

<Dialog open={isOpen} onOpenChange={() => resetAll()}>
  <DialogTrigger id="tax-modal"></DialogTrigger>
  <DialogContent class="sm:max-w-[425px] space-y-2">
    <DialogHeader>
      <DialogTitle>Add New Reminder Group</DialogTitle>
    </DialogHeader>
    <form on:submit|preventDefault={saveReminderGroup} class="space-y-6">
      <!-- Reminder name -->
      <div class="col-span-2 space-y-2">
        <Label for="reminder-group-name">Name <MiniStar /></Label>
        <Input
          id="reminder-group-name"
          type="text"
          required
          placeholder="Reminder group name"
          bind:value={reminderGroupName} />
      </div>

      <!-- Reminder list -->
      <div class="grid grid-cols-2 gap-8">
        <!-- Reminder Before -->
        <div class="space-y-2 p-3 rounded-lg">
          <Label class="col-span-2 cursor-default text-center">Reminders Before</Label>
          <div class="grid grid-cols-1 gap-2">
            {#each $reminders as reminder}
              <div class="flex flex-nowrap items-center gap-x-4 bg-accent px-6 py-3 rounded-lg">
                <Checkbox
                  id={reminder + " before"}
                  onCheckedChange={(isChecked) => {
                    updateSelectedReminders(reminder, isChecked, true);
                  }} />
                <Label for={reminder + " before"}>{pluralHelper(reminder)}</Label>
              </div>
            {/each}
          </div>
        </div>
        <!-- Reminder After -->
        <div class="space-y-2 p-3 rounded-lg">
          <Label class="col-span-2 cursor-default text-center">Reminders After</Label>
          <div class="grid grid-cols-1 gap-2">
            {#each $reminders as reminder}
              <div class="flex flex-nowrap items-center gap-x-4 bg-accent px-6 py-3 rounded-lg">
                <Checkbox
                  id={reminder + " after"}
                  onCheckedChange={(isChecked) => {
                    updateSelectedReminders(reminder, isChecked, false);
                  }} />
                <Label for={reminder + " after"}>{pluralHelper(reminder)}</Label>
              </div>
            {/each}
          </div>
        </div>
      </div>

      <!-- Save button -->
      <DialogFooter>
        <Button disabled={isLoading} type="submit">
          {#if isLoading}
            <Loader2 class="h-4 w-4 animate-spin" />
            Saving
          {:else}
            Save
          {/if}
        </Button>
      </DialogFooter>
    </form>
  </DialogContent>
</Dialog>
