<script script>
  import { Button } from "$lib/components/ui/button";
  import {
    Trash,
    Package,
    Send,
    Files,
    CircleDollarSign,
    Ban,
    History,
    Pencil,
    RefreshCcw,
    CalendarClock,
  } from "lucide-svelte";
  import { PaymentStatus, InvoiceStatus, InvoiceStatusAction } from "$lib/common/enum";
  import { Tooltip, TooltipContent, TooltipTrigger } from "$lib/components/ui/tooltip";

  export let invoiceActions;
  export let actionHandler;
  export let isSent;
  export let isRecurring = false;

  const iconSizeClass = "h-4 w-4";
</script>

<div class="grid grid-cols-3 xl:grid-cols-2 gap-y-2 gap-x-1">
  {#each invoiceActions as action}
    {#if action.status === InvoiceStatus.TO_RECURRING}
      <Tooltip openDelay="{200}">
        <TooltipTrigger asChild let:builder>
          <Button
            builders="{[builder]}"
            variant="outline"
            on:click="{() => {
              if (!isRecurring) {
                actionHandler(action);
              }
            }}"
            class="w-full flex flex-row h-10 p-4 col-span-2 flex-nowrap items-center justify-between text-xs gap-2 px-3 {isRecurring
              ? 'opacity-50 cursor-not-allowed'
              : ''}">
            <div class="flex flex-row flex-nowrap h-10 items-center w-full gap-2">
              <CalendarClock class="{iconSizeClass} text-teal-500" />
              <div class="w-full text-start text-wrap">
                {action.label}
              </div>
            </div>
          </Button>
        </TooltipTrigger>
        {#if isRecurring}
          <TooltipContent>
            <div>Invoice is already recurring</div>
          </TooltipContent>
        {/if}
      </Tooltip>
    {:else}
      <Button
        variant="outline"
        on:click="{() => actionHandler(action)}"
        class="w-full flex flex-row h-10 p-4 flex-nowrap items-center justify-between text-xs gap-2 px-3">
        <div class="flex flex-row flex-nowrap h-10 items-center w-full gap-2">
          {#if action.status === PaymentStatus.PAID}
            <CircleDollarSign class="{iconSizeClass} text-green-600" />
          {:else if action.status === PaymentStatus.UNPAID}
            <CircleDollarSign class="{iconSizeClass} text-destructive" />
          {:else if action.status === InvoiceStatus.SEND}
            <Send class="{iconSizeClass} text-cyan-500" />
          {:else if action.status === InvoiceStatus.DUPLICATE}
            <Files class="{iconSizeClass} text-primary-300" />
          {:else if action.status === InvoiceStatus.CANCELLED}
            <Ban class="{iconSizeClass} text-gray-400" />
          {:else if action.status === InvoiceStatus.ARCHIVED}
            <Package class="{iconSizeClass} text-amber-500" />
          {:else if action.status === InvoiceStatus.TRASHED}
            <Trash class="{iconSizeClass} text-red-500" />
          {:else if action.status === InvoiceStatus.EDIT}
            <Pencil class="{iconSizeClass} text-blue-400" />
          {:else if action.status === InvoiceStatus.REGENERATE}
            <RefreshCcw class="{iconSizeClass} text-teal-400" />
          {:else}
            <History class="{iconSizeClass} text-teal-500" />
          {/if}
          <div class="w-full text-start text-wrap">
            {action.label === InvoiceStatusAction.DUPLICATE ? "Duplicate" : action.label}
          </div>
        </div>
        <!-- Email sent logo -->
        {#if isSent && action.status === InvoiceStatus.SEND}
          <svg
            data-bs-toggle="tooltip"
            title="Email sent"
            class="w-5 h-5"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 64 64"
            xmlns:v="https://vecta.io/nano">
            <path
              fill="#22c55e"
              d="M63.94 39.721l-3.956-22.388c-.369-2.093-2.37-3.495-4.465-3.125l-31.726 5.605c-1.971.348-3.308 2.148-3.15 4.102-.022.162 3.983 22.751 3.983 22.751a3.82 3.82 0 0 0 1.586 2.487c.655.458 1.418.696 2.201.696a3.88 3.88 0 0 0 .678-.06l31.724-5.605c1.014-.179 1.896-.741 2.487-1.586a3.83 3.83 0 0 0 .638-2.879zm-2.673-3.636L50.6 27.879c3.003-3.233 6.138-6.785 7.702-8.572l2.965 16.778zM24.143 21.783l31.725-5.606c.109-.018.217-.028.323-.028.613 0 1.174.311 1.515.8-.932 1.072-9.375 10.757-13.433 14.56-1.062.993-2.603 1.243-3.918.632-4.79-2.213-15.22-7.446-17.727-8.707a1.84 1.84 0 0 1 .32-.887c.284-.406.709-.677 1.195-.764zm-1.164 4.064l10.512 5.219-7.523 11.702-2.99-16.921zm38.686 15.607a1.84 1.84 0 0 1-1.195.763l-31.725 5.606a1.85 1.85 0 0 1-1.386-.308 1.84 1.84 0 0 1-.763-1.195l-.115-.652 8.82-13.72h0l4.215 2.009a5.56 5.56 0 0 0 2.335.516 5.53 5.53 0 0 0 3.789-1.505c1.005-.941 2.255-2.215 3.586-3.622l12.558 9.66.188 1.063a1.84 1.84 0 0 1-.308 1.385zM19.008 29.867c-.096-.544-.616-.911-1.158-.811L.826 32.065a1 1 0 0 0 .172 1.984.95.95 0 0 0 .175-.016l17.024-3.008a1 1 0 0 0 .81-1.158zm1.021 5.785a1 1 0 0 0-1.158-.811L8.657 36.646a1 1 0 0 0 .173 1.984.95.95 0 0 0 .175-.016l10.214-1.805a1 1 0 0 0 .811-1.158zm-.141 4.945L13.079 41.8a1 1 0 0 0 .173 1.984.95.95 0 0 0 .175-.016l6.809-1.203a1 1 0 0 0 .811-1.158c-.095-.544-.617-.912-1.158-.811z">
            </path>
          </svg>
        {/if}
      </Button>
    {/if}
  {/each}
</div>
