<script>
    import {inertia, page} from "@inertiajs/inertia-svelte";

    export let active;
    export let url = "#";
</script>

<a href={url} use:inertia class="text-black hover:underline {$page.component === active ? 'font-bold underline': ''}">
    <slot/>
</a>
