<script>
  import { CardContent, CardHeader, CardTitle } from "$lib/components/ui/card";
  import { Skeleton } from "$lib/components/ui/skeleton";
  import Separator from "$lib/components/ui/separator/separator.svelte";

  export let isPublic = false;
</script>

<CardHeader>
  <CardTitle class="text-center flex items-center justify-around">
    <div>
      <div>Status</div>
      <div>
        <Skeleton class="h-6 w-28" />
      </div>
    </div>
    <div>
      <div>Due</div>
      <div>
        <Skeleton class="h-6 w-28" />
      </div>
    </div>
  </CardTitle>
</CardHeader>
<CardContent class="space-y-4">
  {#if !isPublic}
    <div class="grid grid-cols-3 xl:grid-cols-2 gap-4 justify-center items-center">
      {#each [1, 2, 3, 4, 5, 6] as _}
        <Skeleton class="h-24 w-full" />
      {/each}
    </div>
    <Separator />
  {/if}
  <Skeleton class="h-8 w-full" />
  <Skeleton class="h-8 w-full" />
  <Skeleton class="h-8 w-full" />
</CardContent>
