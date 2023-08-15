<script>
    import {inertia} from "@inertiajs/inertia-svelte";

    export let links;
</script>

<div>
    <div class="mt-6">
        {#each links as link}
            <a use:inertia href="{link.url}" class="px-1 {link.active ? 'font-bold' : ''}">{@html link.label}</a>
            <!--{#if link.url}-->
            <!--    <span class="px-1 {!link.url ? 'text-gray-500' : ''}">{link}</span>-->
            <!--{:else}-->
            <!--    <a use:inertia href="{link.url}" class="px-1 {link.active ? 'font-bold' : ''}">{link}</a>-->
            <!--{/if}-->
        {/each}
    </div>
</div>
