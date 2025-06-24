<script>
  import {
    Card,
    CardHeader,
    CardDescription,
    CardTitle,
    CardContent,
  } from "$lib/components/ui/card";
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import { Loader2 } from "lucide-svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import toast from "svelte-french-toast";

  let isLoading = false;

  let payload = {
    prefix: "#",
    startFromNumber: 1,
  };

  const submit = () => {
    if (isLoading) return;
    toast.promise(createPostRequest("settings/update?tab=quotation", payload), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        return "Quotation settings updated successfully.";
      },
      error: (err) => {
        isLoading = false;
        return err.message;
      },
    });
  };
</script>

<div>
  <Card class="p-4">
    <CardHeader class="space-y-0.5 md:p-6 px-0">
      <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2"
        >Quotation</CardTitle>
      <CardDescription class="ml-3 md:text-sm text-xs"
        >Manage quotation settings.</CardDescription>
    </CardHeader>
    <CardContent class="md:px-6 md:pb-6 p-0">
      <form on:submit|preventDefault={() => submit()}>
        <div class="grid gap-4">
          <div class="w-full md:w-1/2">
            <Label for="prefix" class="md:text-sm text-xs">Prefix</Label>
            <Input
              type="text"
              id="prefix"
              class="md:text-sm text-xs"
              disabled={isLoading}
              bind:value={payload.prefix} />
          </div>
          <div class="w-full md:w-1/2">
            <Label for="startFromNumber" class="md:text-sm text-xs"
              >Start From Number</Label>
            <Input
              type="number"
              id="startFromNumber"
              class="md:text-sm text-xs"
              disabled={isLoading}
              bind:value={payload.startFromNumber} />
          </div>
        </div>
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
    </CardContent>
  </Card>
</div>
