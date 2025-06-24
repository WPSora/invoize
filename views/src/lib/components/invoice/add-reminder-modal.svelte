<script>
  import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { Select, SelectItem, SelectValue, SelectContent, SelectTrigger } from "$lib/components/ui/select";
  import { createPostRequest } from "$lib/helpers/request";
  import { Button } from "$lib/components/ui/button";
  import { reminders } from "$lib/stores/settings-store";
  import { isEmptyCheck } from "$lib/helpers/emptyDataHelper";
  import { Loader2 } from "lucide-svelte";
  import { Label } from "$lib/components/ui/label";
  import { Input } from "$lib/components/ui/input";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import toast from "svelte-french-toast";

  export let open = false;
  export let isLoading = false;
  export let selectedReminderIndex = 0;
  export let isEditing = false;
  // edit mode
  export let editInterval = null;
  // add mode
  let interval = null;

  let selectedType = { value: "day", label: "Day(s)" };

  const resetAll = () => {
    interval = null;
    editInterval = null;
    selectedReminderIndex = null;
    isLoading = false;
    open = false;
  };

  const checkIsExist = () => {
    const isExist = $reminders.find((item) => item.includes(interval.toString()));
    if (isExist) {
      return true;
    }
    return false;
  };

  // add or update
  const saveReminder = async () => {
    if (isLoading) {
      return;
    }
    if (checkIsExist()) {
      toast.error("Value already exist. Please input a new value.");
      return;
    }
    isLoading = true;
    // either add or edit, the payload is all of the reminders, not just the one being edited or added
    let payload;

    if (isEditing) {
      $reminders[selectedReminderIndex] = `${parseInt(editInterval)} ${selectedType.value}`;
      payload = $reminders;
    } else {
      if (isEmptyCheck($reminders)) {
        $reminders = [];
      }
      const newReminder = `${parseInt(interval)} ${selectedType.value}`;
      payload = [...$reminders, newReminder];
    }

    toast.promise(createPostRequest("settings/update?tab=invoice", { reminders: payload }), {
      loading: "Saving...",
      success: () => {
        $reminders = payload;
        resetAll();
        return "Reminder Saved";
      },
      error: (err) => {
        resetAll();
        return handleError(err, "Failed to save reminder.");
      },
    });
  };
</script>

<Dialog {open} onOpenChange={() => resetAll()}>
  <DialogTrigger id="tax-modal"></DialogTrigger>
  <DialogContent class="sm:max-w-[425px] ">
    <DialogHeader>
      <DialogTitle>Add New Reminder</DialogTitle>
    </DialogHeader>
    <form on:submit|preventDefault={saveReminder}>
      <div class="grid gap-4 py-4">
        <div class="grid grid-cols items-center gap-4">
          <Label for="reminder-interval">Interval <MiniStar /></Label>
          <div class="flex flex-row flex-nowrap items-center justify-between gap-x-2">
            {#if isEditing}
              <Input
                id="reminder-interval"
                type="number"
                placeholder="Reminder interval"
                min={1}
                required
                bind:value={editInterval} />
            {:else}
              <Input
                id="reminder-interval"
                type="number"
                placeholder="Reminder interval"
                min={1}
                required
                bind:value={interval} />
            {/if}
            <Select bind:selected={selectedType} disabled>
              <SelectTrigger id="type">
                <SelectValue placeholder="Select type" />
              </SelectTrigger>
              <SelectContent class="max-h-60 overflow-y-scroll">
                <SelectItem value="day">Day(s)</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

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
