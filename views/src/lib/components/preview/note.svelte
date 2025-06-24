<script>
  import QrCode from "svelte-qrcode";
  import NoteContent from "$lib/components/preview/note-content.svelte";

  export let invoiceNote;
  export let link;
</script>

<div class="flex flex-col flex-nowrap space-y-4 w-full">
  <div>
    <div class="font-bold">Note</div>
    <NoteContent content={invoiceNote?.note} />
  </div>
  <div class="flex flex-nowrap justify-between">
    <div>
      <div class="font-bold">Terms & Conditions</div>
      <NoteContent content={invoiceNote?.terms} />
    </div>
    <div class="min-w-20 min-h-20 w-20 h-20">
      {#if link}
        <QrCode value={link} />
      {/if}
    </div>
  </div>
</div>
