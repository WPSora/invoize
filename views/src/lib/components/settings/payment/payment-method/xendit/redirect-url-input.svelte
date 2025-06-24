<script>
  import { Input } from "$lib/components/ui/input";
  import { Button } from "$lib/components/ui/button";
  import { slide } from "svelte/transition";
  import { Loader2, Check } from "lucide-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import toast from "svelte-french-toast";
  import { handleError } from "$lib/helpers/errorHelper";

  export let name;
  export let redirectUrlStore;
  export let settingName;
  export let value;
  let isFocused = false;
  let isEditing = false;
  let isLoading;

  const updateState = () => {
    isEditing = true;
  };

  const saveInputToStore = () => {
    redirectUrlStore = value;
  };

  const submit = () => {
    if (isLoading) return;
    isLoading = true;
    toast.promise(createPostRequest("settings/update?tab=payment", { [settingName]: value }), {
      loading: "Saving...",
      success: () => {
        saveInputToStore();
        isLoading = false;
        isEditing = false;
        return "Settings saved successfully";
      },
      error: (err) => {
        isEditing = false;
        isLoading = false;
        return handleError(err, "Failed to save settings");
      },
    });
  };
</script>

<form class="flex flex-col gap-4" on:submit|preventDefault="{submit}">
  <div class="flex gap-2 items-center">
    <div class="text-nowrap text-sm w-24">On {name}</div>
    :
    {#if isFocused}
      <Input
        {name}
        type="url"
        id="{name}-redirect-url"
        bind:value
        on:blur="{() => (isFocused = false)}"
        on:input="{updateState}" />
    {:else}
      <Input
        type="url"
        {value}
        on:focus="{() => {
          isFocused = true;
          setTimeout(() => {
            document.getElementById(`${name}-redirect-url`)?.focus();
          }, 100);
        }}" />
    {/if}
    {#if isEditing}
      <div transition:slide="{{ axis: 'x' }}">
        <Button disabled="{isLoading}" size="icon" type="submit">
          {#if isLoading}
            <Loader2 class="h-4 w-4 text-white animate-spin" />
          {:else}
            <Check class="h-4 w-4 text-white" />
          {/if}
        </Button>
      </div>
    {/if}
  </div>
</form>
