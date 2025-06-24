<script>
    import Tags from "svelte-tags-input";
    export let value = [];
    export let placeholder = "Add tags";
    export let name = "tags";
    export let addKeys = [9, 188, 32]; // TAB, Space or Comma
</script>

<style>
    .ivz-tags :global(.svelte-tags-input-tag) {
        background: rgb(248 250 252 / var(--tw-bg-opacity)) !important;
        border-radius: 5px !important;
        color: black !important;
        border:1px solid gray !important;
    }

    .ivz-tags :global(.svelte-tags-input-tag):focus {
        box-shadow: unset !important;
    }
    

</style>

<div class="ivz-tags">
    <Tags bind:tags={value} {placeholder} {name} onlyUnique={true} {addKeys}/>
</div>  