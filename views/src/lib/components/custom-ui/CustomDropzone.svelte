<script>
  import Dropzone from "dropzone";
  import { onMount } from "svelte";

  /*
   * Url as to be specified on elements other than form (or when the form doesn't
   * have an `action` attribute).
   *
   * You can also provide a function that will be called with `files` and
   * `dataBlocks`  and must return the url as string.
   */
  export let url = "/";
  export let maxFiles = 1;
  export let initMessage = "Drop or Click here to upload image";
  export let maxFilesize = 256; // in MiB
  export let uploadMultiple = false;
  export let savedFileName = "file";
  export let showThumbnail = true;
  export let thumbnailWidth = 120;
  export let thumbnailHeight = 120;
  /*
   * If `true` the fallback will be forced. This is very useful to test your server
   * implementations first and make sure that everything works as
   * expected without dropzone if you experience problems, and to test
   * how your fallbacks will look.
   */
  export let forceFallback = false;
  export let fallbackMessage = "Your browser does not support drag'n'drop file uploads.";
  /*
     The default implementation of `accept` checks the file's mime type or
   * extension against this list. This is a comma separated list of mime
   * types or file extensions.
   *
   * Eg.: `image/*,application/pdf,.psd`
  */
  export let acceptedFiles = null;
  let className = "";
  export { className as class };

  onMount(() => {
    const dropzone = new Dropzone("#dropzone", {
      url: url,
      dictDefaultMessage: initMessage,
      maxFiles: maxFiles,
      maxFilesize: maxFilesize,
      uploadMultiple: uploadMultiple,
      paramName: savedFileName,
      createImageThumbnails: showThumbnail,
      thumbnailWidth: thumbnailWidth,
      thumbnailHeight: thumbnailHeight,
      acceptedFilesFormat: acceptedFiles,
      forceFallback: forceFallback,
      dictFallbackMessage: fallbackMessage,
    });

    dropzone.on("addedfile", (file) => {
      // Remove initMessage
      const button = document.querySelector(".dz-button");
      button.innerHTML = "";
      // TODO: Implement to send data
    });
  });
</script>

<form class={`dropzone border-2 border-dashed rounded-lg flex items-center my-2 ${className}`} id="dropzone" action="/">
  <!-- when javascript not available in browser, show this instead -->
  <div class="fallback">
    <input name="file" type="file" />
  </div>
</form>
