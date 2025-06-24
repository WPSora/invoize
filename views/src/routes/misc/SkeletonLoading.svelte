<script>
  import { Skeleton } from "$lib/components/ui/skeleton";
</script>

<div class="flex flex-col justify-center items-center w-full h-full">
  <Skeleton class="w-full h-20 mb-8" />
  <div class="w-full mb-10 space-y-4">
    <Skeleton class="h-14 w-40" />
    <Skeleton class="h-8 w-60" />
  </div>
  <div class="w-full mb-4">
    <Skeleton class="w-52 h-8" />
  </div>
  <div class="w-full flex items-center justify-between">
    <div class="flex flex-row gap-4 w-full">
      <Skeleton class="w-32 h-10 my-4" />
      <Skeleton class="w-32 h-10 my-4" />
      <Skeleton class="w-32 h-10 my-4" />
      <Skeleton class="w-32 h-10 my-4" />
    </div>
    <Skeleton class="w-60 h-8 my-4" />
  </div>
  {#each [1, 2, 3, 4, 5] as _}
    <Skeleton class="w-full h-8 my-4 mx-5" />
  {/each}
  <div class="w-full flex items-center justify-between mt-2">
    <Skeleton class="w-60 h-8 my-4" />
    <Skeleton class="w-28 h-8 my-4" />
  </div>
</div>
