<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";

    export let tasks;
    console.log(tasks);
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

<h1 class="text-3xl">All Tasks</h1>

<div class="space-y-4 mt-5">
    {#each tasks.data as task (task.id)}
        <div class="border border-gray-200 rounded p-4 my-2">
            <a use:inertia href="/"><h2 class="text-2xl font-bold pb-3">{task.title}</h2></a>
            <p>{task.description}</p>
            <a use:inertia href="/" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag.name}</a>
        </div>
    {/each}
</div>

<div>
    <div class="mt-6">
        {#if tasks.prev_page_url === null || tasks.prev_page_url === undefined}
            <span class="px-1">&laquo; Prev</span>
        {:else}
            <a use:inertia href="{tasks.first_page_url}" class="px-1">First Page</a>
            <a use:inertia href="{tasks.prev_page_url}" class="px-1">&laquo; Prev</a>
        {/if}

        {#if tasks.next_page_url === null || tasks.next_page_url === undefined}
            <span class="px-1">Next &raquo;</span>
        {:else}
            <a use:inertia href="{tasks.next_page_url}" class="px-1">Next &raquo;</a>
        {/if}
    </div>
</div>

