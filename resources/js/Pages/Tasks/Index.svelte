<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";

    export let tasks;
    export let filters;

    console.log(tasks);

    let search = filters.search || '';
    let timer;

    // debounce search input
    const debounce = v => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            Inertia.get('/', {search: v}, {
                // search input doesn't reload the page and lose focus
                preserveState: true,
                // prevents the browser's history from being updated
                replace: true,
                // preserve scroll position on back navigation
                preserveScroll: true,
            });
        }, 300);
    }
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

<div class="flex justify-between mb-6">
    <h1 class="text-3xl">All Tasks</h1>

    <input on:keyup={({ target: { value } }) => debounce(value)} value={search} type="text" placeholder="Search..." class="border px-2 rounded-lg"/>
</div>

<div class="space-y-4 mt-5">
    {#if tasks.data.length < 1}
        <p>There currently are no tasks</p>
    {:else}
        {#each tasks.data as task (task.id)}
            <div class="border border-gray-200 rounded p-4 my-2">
                <div class="flex items-center justify-between">
                    <a use:inertia href="/tasks/{task.slug}"><h2 class="text-2xl font-bold pb-3">{task.title}</h2></a>
                    <span class="whitespace-nowrap">Status: <span class="text-[#3bba2d] bg-[#d4facf] p-2 rounded">{task.status}</span></span>
                </div>
                <p class="mt-4">{task.description}</p>
                <a use:inertia href="/" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
            </div>
        {:else}
            <p>There currently are no tasks</p>
        {/each}
    {/if}
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

