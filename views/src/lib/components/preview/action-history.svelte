<script>
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "$lib/components/ui/accordion";
  import { Separator } from "$lib/components/ui/separator";
  import { ChevronsRight } from "lucide-svelte";
  import moment from "moment";

  export let actionHistory;
</script>

<Accordion>
  <AccordionItem value="history">
    <AccordionTrigger class="text-base">History</AccordionTrigger>
    <AccordionContent class="max-h-48 overflow-y-scroll pr-2">
      {#each actionHistory as action}
        {@const time = moment(action.time).format(`${invoize.date_format}, HH:mm:ss a`)}
        <div class="bg-muted rounded-xl p-4 space-y-1 text-xs mb-3">
          <div class="font-medium text-primary">{time}</div>
          <Separator class="bg-white" />
          <div class="text-slate-700 italic">{action.message}</div>
          <Separator class="bg-white" />
          <div class="border-r border-r-primary pr-2">
            <div class="text-right text-primary font-medium">{action.user?.username ?? action.user?.name ?? "-"}</div>
            <!-- <div class="text-right text-slate-700 italic">{action.user.email}</div> -->
            {#if action.from}
              <div class="flex flex-row flex-nowrap w-full items-center justify-end gap-x-5">
                <div class="italic text-slate-700 text-center">
                  {action.from}
                </div>
                <ChevronsRight class="h-4 w-4 text-primary" />
                <div class="italic text-slate-700 text-center">
                  {action.to}
                </div>
              </div>
            {:else}
              <div></div>
            {/if}
          </div>
        </div>
      {:else}
        <div class="text-muted-foreground italic text-sm text-center">No action history</div>
      {/each}
    </AccordionContent>
  </AccordionItem>
</Accordion>
