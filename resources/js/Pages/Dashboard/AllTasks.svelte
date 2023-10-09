<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";
    import IntersectionObserver from "svelte-intersection-observer";

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    /* svelte-ignore unused-export-let */
    export let tasks;

    console.log(tasks)

    let element;
    let allTasks = [];
    allTasks = [...tasks.data];

    if (window.performance) {
        if (performance.navigation.type === 1) {
            Inertia.get(tasks.first_page_url, {})
        }
    }

    const load = () => {
        if (tasks.next_page_url) {
            Inertia.get(tasks.next_page_url, {}, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    allTasks = [...allTasks, ...page.props.tasks.data];
                }
            })
        }
    }
</script>

<svelte:head>
    <title>All Tasks</title>
</svelte:head>

<section class="p-6 dark:bg-gray-500 mb-[500px]">
    <div class="max-w-3xl mx-auto">

        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <div class="grid grid-cols-2 gap-4">
            {#each allTasks as task (task.id)}
                <div class="flex flex-col justify-center items-center border border-red-500 w-full p-5">
                    <a use:inertia href="/dashboard/tasks/{task.slug}">{task.title}</a>
                </div>
            {/each}
        </div>

    </div>
</section>

<IntersectionObserver {element} on:intersect={load}>
    <div bind:this={element}>Load More</div>
</IntersectionObserver>
