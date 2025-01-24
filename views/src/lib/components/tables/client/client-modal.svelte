<script>
  import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
  } from "$lib/components/ui/dialog";
  import { toast } from "svelte-french-toast";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
  import { Button } from "$lib/components/ui/button";
  import { createEventDispatcher, onMount } from "svelte";
  import { createPostRequest } from "$lib/helpers/request";
  import Dropzone from "svelte-file-dropzone/Dropzone.svelte";
  import axios from "axios";
  import { handleError } from "$lib/helpers/errorHelper";

  export let open = false;
  export let client;

  const dispatch = createEventDispatcher();

  let loading = false;

  const saveClient = () => {
    if (loading) {
      return;
    }
    loading = true;
    let route = client.id ? "client/update" : "client/create";
    toast.promise(
      createPostRequest(route, client, () => {
        loading = false;
        open = false;
        dispatch("update");
      }),
      {
        loading: "Saving Client...",
        success: "Client saved successfully",
        error: (err) => {
          loading = false;
          open = false;
          return handleError(err, "Failed to save client");
        },
      },
    );
  };

  // COUNTRY SELECT
  let countries = [];
  onMount(async () => {
    try {
      const response = await axios.get("https://restcountries.com/v3.1/all");
      countries = response.data
        .map((country) => ({
          label: country.name.common,
          id: country.cca3,
        }))
        .sort((a, b) => a.label.localeCompare(b.label));
    } catch (err) {
      handleError(err, "Failed to retrieve data");
    }
  });

  let files = {
    accepted: [],
    rejected: [],
  };
  function handleFilesSelect(e) {
    const { acceptedFiles, fileRejections } = e.detail;
    if (acceptedFiles.length > 0) {
      const firstFile = acceptedFiles[0];
      if (firstFile.type === "image/jpeg" || firstFile.type === "image/png" || firstFile.type === "image/gif") {
        files.accepted = [firstFile];
      } else {
        files.rejected = [firstFile];
      }
    }
    files.rejected = [...files.rejected, ...fileRejections];
  }
</script>

<Dialog {open} onOpenChange={() => (open = open ? false : open)}>
  <DialogContent class="max-w-3xl">
    <DialogHeader>
      <DialogTitle>Add New Client</DialogTitle>
      <DialogDescription>Add New Client here</DialogDescription>
    </DialogHeader>
    <form on:submit|preventDefault={saveClient} class="space-y-4">
      <div class="grid grid-cols-2 w-full items-center gap-8">
        <div class="grid-cols-1">
          <Label for="name">Name</Label>
          <Input required type="text" id="name" bind:value={client.name} placeholder="Author name" />
        </div>
        <div class="grid-cols-1">
          <Label for="organization">Organization</Label>
          <Input
            required
            type="text"
            id="organization"
            bind:value={client.organization}
            placeholder="Author organization" />
        </div>

        <div class="grid-cols-1">
          <Label for="email">Email</Label>
          <Input required type="text" id="email" bind:value={client.email} placeholder="Author Email" />
        </div>

        <div class="grid-cols-1">
          <Label for="celular">Celular</Label>
          <Input required type="number" id="celular" bind:value={client.celular} placeholder="Author celular" />
        </div>

        <div class="grid-cols-1">
          <Label for="country">Country</Label>
          <select bind:value={client.country}>
            {#each countries as country (country.label)}
              <option value={country.label} id="country">{country.label}</option>
            {/each}
          </select>
        </div>

        <div class="grid-cols-1">
          <Label for="state">State / Province / Region</Label>
          <Input required type="text" id="state" bind:value={client.state} placeholder="Author region" />
        </div>

        <div class="col-span-2">
          <Label for="address">Address</Label>
          <Input required type="text" id="address" bind:value={client.address} placeholder="Author address" />
        </div>

        <div class="grid-cols-1">
          <Label for="city">City</Label>
          <Input required type="text" id="city" bind:value={client.city} placeholder="Author City" />
        </div>

        <div class="grid-cols-1">
          <Label for="zip">ZIP Code</Label>
          <Input required type="number" id="zip" bind:value={client.zip} placeholder="Author Zip" />
        </div>

        <div class="col-span-2">
          <Label for="web">Situs Web</Label>
          <Input required type="text" id="web" bind:value={client.web} placeholder="Author web" />
        </div>

        <div class="col-span-2">
          <Dropzone
            on:drop={handleFilesSelect}
            minSize={1024}
            maxSize={5242880}
            accept="image/jpeg, image/png, image/gif" />
          <ol>
            {#each files.accepted as item}
              <li>{item.name}</li>
            {/each}
          </ol>
        </div>
      </div>
      <DialogFooter>
        <Button disabled={loading}>
          {#if loading}
            <div class="justify-items-center">
              <l-waveform size="20" stroke="2.0" speed="0.9" color="white" class="mr-0 mt-0" />
            </div>
          {:else}
            Save changes
          {/if}
        </Button>
      </DialogFooter>
    </form>
  </DialogContent>
</Dialog>
