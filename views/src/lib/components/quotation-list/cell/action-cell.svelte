<script>
  import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuGroup,
    DropdownMenuSeparator,
  } from "$lib/components/ui/dropdown-menu";
  import {
    Eye,
    Trash,
    Package,
    Send,
    Settings2,
    Files,
    CircleDollarSign,
    Ban,
    History,
    RefreshCcw,
    Loader2,
    Pencil,
    Archive,
  } from "lucide-svelte";
  import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogCancel,
    AlertDialogDescription,
    AlertDialogTitle,
    AlertDialogFooter,
  } from "$lib/components/ui/alert-dialog";
  import { createPostRequest } from "$lib/helpers/request";
  import { getInvoiceActions } from "$lib/helpers/actionHelper";
  import {
    PaymentStatus,
    PaymentStatusAction,
    InvoiceStatus,
    InvoiceListTab,
  } from "$lib/common/enum";
  import { receiptStartFromNumber } from "$lib/stores/settings-store";
  import { Checkbox } from "$lib/components/ui/checkbox";
  import { Label } from "$lib/components/ui/label";
  import { handleError } from "$lib/helpers/errorHelper";
  import { Button } from "$lib/components/ui/button";
  import { toast } from "svelte-french-toast";
  import moment from "moment";
  import { quotation } from "$lib/helpers/quotation";

  export let id;
  export let status;
  export let token;
  export let prefix;
  export let clientName;
  export let tab;
  export let dueDate;
  export let updateTabList = (tab1) => {};

  let isLoading;
  let isDialogOpen;
  let payloadKey;
  let tab1;
  let tab2;
  let isSendEmail = false;
  // for dialog button message
  let actionMessage;
  // action to do / tab to go
  let actionStatus;

  const archiveQuotation = () => {
    isLoading = true;
    toast.promise(quotation.archive(token), {
      loading: "Archiving quotation...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        updateTabList("archive");
        updateTabList("active");
        return "Quotation archived successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to archive quotation", false);
      },
    });
  };

  const sendQuotation = () => {
    isLoading = true;
    toast.promise(quotation.send(token), {
      loading: "Sending quotation...",
      success: () => {
        isLoading = false;
        isDialogOpen = false;
        updateTabList("archive");
        updateTabList("active");
        return "Quotation sent successfully";
      },
      error: (err) => {
        isLoading = false;
        isDialogOpen = false;
        return handleError(err, "Failed to send quotation", false);
      },
    });
  };
</script>

<DropdownMenu>
  <div class="flex justify-center">
    <DropdownMenuTrigger class="p-0">
      <Button
        size="icon"
        variant="link"
        disabled={isLoading}
        class="hover:text-background w-6 h-6 hover:bg-primary active:bg-primary-300"
      >
        {#if isLoading}
          <RefreshCcw class="h-4 animate-spin" />
        {:else}
          <Settings2 class="h-4" />
        {/if}
      </Button>
    </DropdownMenuTrigger>
  </div>
  <DropdownMenuContent
    class="w-48 p-2 shadow-xl bg-gradient-to-tr from-slate-200 to-white"
  >
    <DropdownMenuGroup>
      <!-- Preview -->
      <DropdownMenuItem
        on:click={() => (location.href = `#/quotation/${token}`)}
        class="cursor-pointer gap-2 flex justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white"
      >
        Preview
        <Eye class="text-inherit" size={16} />
      </DropdownMenuItem>
      {#if status == "active"}
        <DropdownMenuItem
          on:click={archiveQuotation}
          class="cursor-pointer gap-2 flex justify-between text-yellow-700 text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white"
        >
          Archive
          <Archive class="text-inherit" size={16} />
        </DropdownMenuItem>
      {/if}
      {#if status == "active"}
        <DropdownMenuItem
          on:click={sendQuotation}
          class="cursor-pointer gap-2 flex text-success justify-between text-[13px] data-[highlighted]:bg-primary data-[highlighted]:text-white"
        >
          Send
          <Send class="text-inherit" size={16} />
        </DropdownMenuItem>
      {/if}
    </DropdownMenuGroup>
  </DropdownMenuContent>
</DropdownMenu>
