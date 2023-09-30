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
    export let client_d;

    console.log(projects)

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
                <div class="flex flex-col justify-center items-center border border-red-500 w-full p-5">
                    {#if project.user_id === client_d}
                        <button type="button" use:inertia="{{ href: `/dashboard/project/${project.id}`, method: 'delete', replace: true, }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd" /></svg></button>
                    {/if}
                    <a use:inertia href="/dashboard?project={project.name}">{project.name}</a>
                    <p>{project.number_of_tasks} tasks</p>
                    <p>Created on {project.created_at}</p>
                    <p>Creator {project.creator}</p>
                    <p>Creator {project.description}</p>
                </div>
            {/each}
        </div>

    </div>
</section>

<IntersectionObserver {element} on:intersect={load}>
    <div bind:this={element}>Load More</div>
</IntersectionObserver>
