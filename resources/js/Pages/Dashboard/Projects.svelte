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
    export let projects;

    let element;
    let allProjects = [];
    allProjects = [...projects.data];

    if (window.performance) {
        if (performance.navigation.type === 1) {
            Inertia.get(projects.first_page_url, {})
        }
    }

    const load = () => {
        if (projects.next_page_url) {
            Inertia.get(projects.next_page_url, {}, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    allProjects = [...allProjects, ...page.props.projects.data];
                }
            })
        }
    }
</script>

<svelte:head>
    <title>Projects</title>
</svelte:head>

<section class="p-6 dark:bg-gray-500 mb-[500px]">
    <div class="max-w-3xl mx-auto">

        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <div class="grid grid-cols-2 gap-4">
            {#each allProjects as project (project.id)}
                <div class="flex justify-center items-center border border-red-500 h-[200px] w-full">
                    <a use:inertia href="/dashboard?project={project.name}">{project.name}</a>
                </div>
            {/each}
        </div>

    </div>
</section>

<IntersectionObserver {element} on:intersect={load}>
    <div bind:this={element}>Load More</div>
</IntersectionObserver>
