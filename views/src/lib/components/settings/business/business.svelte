<script>
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "$lib/components/ui/card";
  import { Dialog, Content, Title } from "$lib/components/ui/dialog";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import { FileDropzone } from "@skeletonlabs/skeleton";
  import { MinusCircleIcon, Loader2, Mail, Phone, Globe, MapPin } from "lucide-svelte";
  import { slide } from "svelte/transition";
  import { Plus } from "radix-icons-svelte";
  import {
    defaultBusinessId,
    businesses,
    hasBusinessTabSettings,
    hasDefaultBusinessId,
  } from "$lib/stores/settings-store";
  import EditButton from "$lib/components/custom-ui/EditButton.svelte";
  import Separator from "$lib/components/ui/separator/separator.svelte";
  import Button from "$lib/components/ui/button/button.svelte";
  import Input from "$lib/components/ui/input/input.svelte";
  import Label from "$lib/components/ui/label/label.svelte";
  import toast from "svelte-french-toast";
  import Skeleton from "$lib/components/ui/skeleton/skeleton.svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import MiniStar from "$lib/components/custom-ui/MiniStar.svelte";
  import Textarea from "$lib/components/ui/textarea/textarea.svelte";
  import MultilineText from "$lib/components/custom-ui/MultilineText.svelte";

  /**
   * @typedef {Object} Business
   * @property {number} id
   * @property {string} business_name
   * @property {string | number} phone_number
   * @property {string} email
   * @property {string} web
   * @property {string} address
   * @property {string | number} zip
   * @property {object} logo
   */

  const listApi = "business/list?";
  const addApi = "business/add";
  const updateApi = "business/update";
  const deleteApi = "business/delete";

  // true means modal is for adding business, false means for editing
  let isEditing = false;
  let isLoading = false;
  let isModalOpen = false;
  let isDeleteModalOpen = false;
  let pagination = { page: 0, perPage: 0, totalItems: 0, totalPages: 0 };
  let selectedPagination = { value: 5, label: "5" };
  // let isLoadingDefaultButton = false;
  // let paginationOption = [5, 10, 25];

  /** @type {Array<boolean>} */
  let defaultButtonState = [];

  /** @type {number}*/
  let selectedId;

  /** @type {string}*/
  let logoPreviewUrl; // for inside the modal

  /** @type {Business} */
  let business = {
    id: 0,
    business_name: "",
    phone_number: "",
    email: "",
    web: "",
    address: "",
    zip: "",
    logo: null,
  };

  const onChangeHandler = (event) => {
    const file = event.target.files[0];
    const allowedTypes = ["image/jpeg", "image/png"];
    const allowedSize = 2 * 1024 * 1024; // 2MB
    if (file && file.size > allowedSize) {
      toast.error("File size is too large. Maximum size is 2MB.");
      return;
    }
    if (file && allowedTypes.includes(file.type)) {
      logoPreviewUrl = URL.createObjectURL(file);
    } else {
      toast.error("Invalid file type. Only images are allowed.");
    }
  };

  /**
   * when user click on delete button
   * @param {Business} data
   */
  const handleDelete = (data) => {
    selectedId = data.id;
    isDeleteModalOpen = true;
  };

  /**
   * when user click on edit button
   * @param {Business} data
   */
  const handleEdit = (data) => {
    isEditing = true;
    isModalOpen = true;
    business = data;
    logoPreviewUrl = data.logo;
  };

  const handleAdd = () => {
    business = resetBusiness();
    isEditing = false;
    isModalOpen = true;
    logoPreviewUrl = null;
  };

  const resetBusiness = () => {
    logoPreviewUrl = null;
    return {
      id: 0,
      business_name: "",
      phone_number: "",
      email: "",
      web: "",
      address: "",
      zip: "",
      logo: null,
    };
  };

  const setDefaultButtonState = () => {
    $businesses.map((b) => defaultButtonState.push(false));
  };

  /** when user submit the edited business */
  const updateBusiness = async () => {
    const isFile = business.logo && typeof business.logo === "object";
    if (isFile) {
      business.logo = business.logo[0];
    }
    isLoading = true;
    toast.promise(createPostRequest(updateApi, business), {
      loading: "Saving...",
      success: () => {
        isLoading = false;
        isModalOpen = false;
        business = resetBusiness();
        logoPreviewUrl = null;
        getBusinessList();
        return "Business Updated";
      },
      error: (err) => {
        isLoading = false;
        return handleError(err, "Failed to update business");
      },
    });
  };

  const addBusiness = () => {
    business.logo = business.logo ? business.logo[0] : null;
    isLoading = true;
    toast.promise(
      createPostRequest(addApi, business, (res) => {
        isLoading = false;
        isModalOpen = false;
        business = resetBusiness();
        logoPreviewUrl = null;
        getBusinessList();
        getDefaultBusinessSetting();
      }),
      {
        loading: "Saving...",
        success: "Business Added",
        error: (err) => {
          isModalOpen = false;
          isLoading = false;
          return handleError(err, "Could not save settings");
        },
      },
    );
  };

  const deleteBusiness = (id) => {
    isLoading = true;
    toast.promise(createPostRequest(deleteApi, { id }), {
      loading: "Deleting...",
      success: () => {
        isLoading = false;
        isDeleteModalOpen = false;
        selectedId = null;
        getBusinessList();
        getDefaultBusinessSetting();
        return "Business Deleted";
      },
      error: (err) => {
        isLoading = false;
        isDeleteModalOpen = false;
        return handleError(err, "Failed to delete business");
      },
    });
  };

  // const updateDefaultBusiness = ({ detail }) => {
  //   const { id, index: i } = detail;
  //   defaultButtonState[i] = true;
  //   isLoadingDefaultButton = true;
  //   toast.promise(createPostRequest("settings/update?tab=business", { default: id }), {
  //     loading: "Saving...",
  //     success: () => {
  //       defaultButtonState[i] = false;
  //       isLoadingDefaultButton = false;
  //       $defaultBusinessId = id;
  //       return "Default business updated";
  //     },
  //     error: (err) => {
  //       defaultButtonState[i] = false;
  //       isLoadingDefaultButton = false;
  //       return handleError(err, "Failed to update default business");
  //     },
  //   });
  // };

  const getBusinessList = async () => {
    try {
      isLoading = true;
      const response = await createGetRequest(`${listApi}?per_page=${selectedPagination.value}`);
      const { page, per_page, total_items, total_pages, items } = response.data;
      pagination = { page, perPage: per_page, totalItems: total_items, totalPages: total_pages };
      isLoading = false;
      $businesses = items;
      $hasBusinessTabSettings = true;
    } catch (err) {
      isLoading = false;
      handleError(err, "Failed to retrieve business list");
    }
  };

  const getDefaultBusinessSetting = () => {
    createGetRequest(`settings/retrieve?tab=business`)
      .then((res) => {
        res.data.data.map((item) => {
          if (item.name === "default") {
            $defaultBusinessId = parseInt(item.value);
          }
        });
        $hasDefaultBusinessId = true;
      })
      .catch((err) => {
        handleError(err, "Failed to retrieve business settings");
      });
  };

  onMount(() => {
    !$hasBusinessTabSettings && getBusinessList();
    !$hasDefaultBusinessId && getDefaultBusinessSetting();
    setDefaultButtonState();
  });
</script>

<Card class="p-4">
  <CardHeader class="space-y-0.5 md:p-6 px-0">
    <CardTitle class="md:text-lg text-base border-l-2 border-primary pl-2">Business</CardTitle>
    <CardDescription class="ml-3 md:text-sm text-xs">Manage your business information.</CardDescription>
  </CardHeader>
  {#if isLoading}
    <div class="space-y-12 rounded-md p-4">
      {#each $businesses.length > 0 ? $businesses : [1, 2, 3, 4, 5] as _}
        <div class="space-y-4">
          <Skeleton class="h-32 w-full" />
        </div>
      {/each}
    </div>
  {:else}
    <CardContent class="md:px-6 md:pb-6 p-0">
      {#each $businesses as business, i}
        <div
          class="w-full rounded-xl mb-8 shadow-md border border-border bg-secondary flex md:flex-row flex-col flex-nowrap items-center">
          <!-- Image -->
          <div
            class="flex justify-center items-center md:w-24 w-16 md:h-24 h-16 mx-6 my-4 bg-white rounded-full aspect-square">
            {#if business?.logo}
              <img class="aspect-square rounded-full object-cover" src={business.logo} alt="logo" />
            {:else}
              <div></div>
            {/if}
          </div>
          <!-- Content -->
          <div class="w-full bg-white px-4 rounded-r-xl">
            <div class="flex justify-between items-center py-2">
              <div class="font-semibold md:text-xl text-base mt-2 text-primary md:text-start text-center w-full">
                {business.business_name}
              </div>

              <!-- Action button (large screen)-->
              <div class="hidden md:flex items-center gap-x-2">
                <!-- <DefaultButton
                  on:updateDefault={updateDefaultBusiness}
                  data={business}
                  storeDataId={$defaultBusinessId}
                  isLoading={isLoadingDefaultButton}
                  buttonState={defaultButtonState}
                  index={i} /> -->
                <EditButton on:click={() => handleEdit(business)} />
                <!-- <DeleteButton on:click={() => handleDelete(business)} /> -->
              </div>
            </div>
            <Separator class="w-full h-0.5 my-2 bg-accent" />

            <div class="flex md:text-sm text-xsjustify-start p-4">
              <div class="w-1/2 space-y-1">
                <div class="text-muted-foreground flex gap-x-2 items-center">
                  <Mail class="h-4 w-4 text-primary-200" />
                  {business.email}
                </div>
                <div class="text-muted-foreground flex gap-x-2 items-center">
                  <Phone class="h-4 w-4 text-primary-200" />
                  {business.phone_number}
                </div>
                <div class="text-muted-foreground text-blue-400 flex gap-x-2 items-center">
                  <Globe class="h-4 w-4 text-primary-200" />
                  <a href="http://{business.web}" target="_blank" class="text-blue-400">
                    {business.web ? business.web : "-"}
                  </a>
                </div>
              </div>
              <div class="w-1/2 text-muted-foreground flex gap-x-2 items-center">
                <div class="flex gap-x-2">
                  <MapPin class="h-4 w-4 text-primary-200" />
                  <MultilineText text={business.address} />
                  <!-- {business.address}, {business.zip} -->
                </div>
              </div>
            </div>

            <!-- Action button (small screen)-->
            <div class="md:hidden flex items-center justify-end gap-x-2 mb-4">
              <!-- <DefaultButton
                on:updateDefault={updateDefaultBusiness}
                data={business}
                storeDataId={$defaultBusinessId}
                isLoading={isLoadingDefaultButton}
                buttonState={defaultButtonState}
                index={i} /> -->
              <EditButton on:click={() => handleEdit(business)} />
              <!-- <DeleteButton on:click={() => handleDelete(business)} /> -->
            </div>
          </div>
        </div>
      {:else}
        <Separator />
        <div class="italic text-sm text-muted-foreground mt-4 mb-8">You have no saved business entity.</div>
      {/each}

      <!-- Add new business button -->
      {#if $businesses.length === 0}
        <Button on:click={handleAdd} class="sm:w-fit w-full">
          <Plus />
          <div class="ml-1">Business Entity</div>
        </Button>
      {/if}
    </CardContent>
  {/if}
</Card>

<!-- Pagination -->
<!-- <Pagination
  {listApi}
  {paginationOption}
  name="business"
  bind:isLoading
  bind:dataList={$businesses}
  bind:pagination
  bind:selectedPagination /> -->

<!-- Add or Edit business modal -->
<Dialog bind:open={isModalOpen} onOpenChange={resetBusiness}>
  <Content class="md:w-fit w-11/12">
    <Title class="md:text-lg text-base">{isEditing ? "Update" : "Add"} Business Information</Title>
    <form
      on:submit|preventDefault={() => {
        isEditing ? updateBusiness() : addBusiness();
      }}>
      <div class="md:space-y-6 space-y-2 md:p-4 p-0 pt-1">
        <div class="space-y-2">
          <Label for="general_businessname">Company Name <MiniStar /></Label>
          <Input
            type="text"
            id="general_businessname"
            required
            bind:value={business.business_name}
            placeholder="Your company name" />
        </div>
        <div class="space-y-2">
          <Label for="general_phone_number">Company Phone Number <MiniStar /></Label>
          <Input
            type="tel"
            required
            id="general_phone_number"
            bind:value={business.phone_number}
            placeholder="Your company phone number" />
        </div>
        <div class="gap-x-4 gap-y-2 grid md:grid-cols-2 grid-cols-1">
          <div class=" space-y-2">
            <Label for="general_email">Company Email <MiniStar /></Label>
            <Input
              type="email"
              id="general_email"
              required
              bind:value={business.email}
              placeholder="Your company email" />
          </div>
          <div class=" space-y-2">
            <Label for="general_web">Website</Label>
            <Input type="text" id="general_web" bind:value={business.web} placeholder="Example-website.com" />
          </div>
        </div>
        <div class="space-y-2">
          <Label for="general_address">Additional Information</Label>
          <Textarea
            class="h-24"
            id="general_address"
            bind:value={business.address}
            placeholder="Add additional information such as address, tax number, company number identification, etc..." />
        </div>
        <!-- <div class="space-y-2">
          <Label for="general_zip">ZIP Code</Label>
          <Input id="general_zip" type="text" bind:value={business.zip} placeholder="Your company ZIP code" />
        </div> -->

        <div class="flex justify-between md:flex-row flex-col">
          <!-- Company Logo -->
          <div class="w-full">
            <Label>Company Logo</Label>
            <!-- Dropzone and Logo preview -->
            <div class="flex flex-row flex-nowrap mt-2">
              <!-- Dropzone -->
              {#if !logoPreviewUrl}
                <div class="w-full">
                  <FileDropzone name="logoFile" bind:files={business.logo} padding="p-4" on:change={onChangeHandler}>
                    <svelte:fragment slot="message">
                      <span class="xl:text-sm text-xs"><b>Click</b> here or <b>Drag</b> here</span>
                    </svelte:fragment>
                    <svelte:fragment slot="meta">
                      <span class="text-xs">JPG or PNG format. Max size 2MB.</span>
                    </svelte:fragment>
                  </FileDropzone>
                </div>
              {/if}

              <!-- Logo preview -->
              {#if logoPreviewUrl}
                <div class="w-28" transition:slide>
                  <img class="shadow-lg rounded-lg" src={logoPreviewUrl} alt="company logo" />
                  <Button
                    variant="outline"
                    class="text-xs mt-2 w-full"
                    type="button"
                    on:click={() => {
                      business.logo = null;
                      logoPreviewUrl = null;
                    }}>
                    <MinusCircleIcon class="mr-2 text-destructive" />
                    Remove
                  </Button>
                </div>
              {/if}
            </div>
          </div>
        </div>

        <div class="space-y-2 md:pt-4 pt-2 w-full flex justify-end">
          <Button disabled={isLoading} class="md:w-fit w-full">
            {#if isLoading}
              <Loader2 class="mr-2 w-4 h-4 animate-spin" />
              Saving
            {:else}
              Save Changes
            {/if}
          </Button>
        </div>
      </div>
    </form>
  </Content>
</Dialog>

<!-- Delete modal -->
<AlertDialog bind:open={isDeleteModalOpen}>
  <AlertDialogContent>
    <AlertDialogHeader>
      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
      <AlertDialogDescription>
        This action cannot be undone. This will permanently delete and remove your data.
      </AlertDialogDescription>
    </AlertDialogHeader>
    <AlertDialogFooter>
      <AlertDialogCancel on:click={() => (selectedId = null)}>Cancel</AlertDialogCancel>
      <Button variant="destructive" disabled={isLoading} on:click={() => deleteBusiness(selectedId)}>
        {#if isLoading}
          <Loader2 class="mr-2 w-4 h-4 animate-spin" />
          Deleting
        {:else}
          Delete
        {/if}
      </Button>
    </AlertDialogFooter>
  </AlertDialogContent>
</AlertDialog>
