<script>
  import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuGroup,
    DropdownMenuItem,
  } from "$lib/components/ui/dropdown-menu";
  import { toast } from "svelte-french-toast";
  import { Button } from "$lib/components/ui/button";
  import { ChevronDown, Plus } from "lucide-svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { createEventDispatcher } from "svelte";
  import { handleError } from "$lib/helpers/errorHelper";

  const dispatch = createEventDispatcher();

  let loading;

  const syncClient = () => {
    if (loading) {
      return;
    }
    loading = true;
    toast.promise(
      createGetRequest("client/sync", () => {
        dispatch("update");
        loading = false;
      }),
      {
        loading: "Syncing...",
        success: "Synced",
        error: (err) => {
          loading = false;
          return handleError(err, "Failed to sync");
        },
      },
    );
  };
</script>

<DropdownMenu>
  <DropdownMenuTrigger asChild let:builder>
    <Button builders={[builder]} variant="default">
      Actions
      <ChevronDown class="ml-2 h-4 w-4" />
    </Button>
  </DropdownMenuTrigger>
  <DropdownMenuContent class="w-56">
    <DropdownMenuLabel>Actions</DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
      <DropdownMenuItem on:click={() => dispatch("open-modal")}>
        <Plus class="mr-2 h-4 w-4" />
        New
      </DropdownMenuItem>
      <!-- <DropdownMenu.Item on:click={syncClient} disabled={loading}>
        <Reload class="mr-2 h-4 w-4" />
        <span> Sync with Woocomeerce </span>
      </DropdownMenu.Item> -->
    </DropdownMenuGroup>
  </DropdownMenuContent>
</DropdownMenu>
