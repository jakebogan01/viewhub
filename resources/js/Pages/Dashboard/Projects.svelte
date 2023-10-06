<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia, useForm} from "@inertiajs/inertia-svelte";
    import { router } from '@inertiajs/svelte';
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

    let obj = {
        id: null,
        name: '',
    };
    $: obj;
    let createProject, showForm, makeEdits = false;
    let form = useForm({
        project_name: '',
        project_description: '',
    });

    function updateFormData(id, name, description) {
        showForm = true;
        makeEdits = true;
        obj.id = id;
        obj.name = name;
        obj.description = description;
        $form.project_name = obj.name;
        $form.project_description = obj.description ? obj.description : '';
        console.table($form)
    }

    function submit() {
        if (createProject) {
            $form.post(`/dashboard/project/create`, {
                replace: true,
            })
        }

        if (makeEdits) {
            $form.patch(`/dashboard/project/${obj.id}`, {
                replace: true,
            })
            // router.reload({ only: ['projects'] })
        }

        createProject = false;
        showForm = false;
        makeEdits = false;
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

        <div>
            <button type="button" on:click={()=>{createProject = true; showForm = true;}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">Create Project</button>
        </div>

        {#if showForm}
            <form on:submit|preventDefault={submit}>
                {#if createProject}
                    <input bind:value={$form.project_name} type="text" id="project_name" name="project_name" placeholder="Project Name">
                    {#if $form.errors.project_name}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.project_name} </p>
                    {/if}

                    <textarea bind:value={$form.project_description} id="project_description" name="project_description" placeholder="Project Description"></textarea>
                    {#if $form.errors.project_description}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.project_description} </p>
                    {/if}

                    <button type="submit" class="bg-orange-400 text-white rounded py-2 px-4 hover:bg-blue-500">Create</button>
                {/if}

                {#if makeEdits}
                    <input value={obj.name} on:input={(e)=>{$form.project_name = e.target.value}} type="text" id="project_name" name="project_name" placeholder="Project Name">
                    {#if $form.errors.project_name}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.project_name} </p>
                    {/if}

                    <textarea on:input={(e)=>{$form.project_description = e.target.value}} id="project_description" name="project_description" placeholder="Project Description">{obj.description}</textarea>
                    {#if $form.errors.project_description}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.project_description} </p>
                    {/if}

                    <button type="submit" class="bg-orange-400 text-white rounded py-2 px-4 hover:bg-blue-500">Update</button>
                {/if}
            </form>
        {/if}

        <div class="grid grid-cols-2 gap-4">
            {#each allProjects as project (project.id)}
                <div class="flex flex-col justify-center items-center border border-red-500 w-full p-5">
                    <button type="button" use:inertia="{{ href: `/dashboard/project/${project.id}`, method: 'delete', replace: true, }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z" clip-rule="evenodd" /></svg></button>
                    <button type="button" on:click={()=>{updateFormData(project.id, project.name, project.description)}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-green-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg></button>
                    <a use:inertia href="/dashboard?project={project.name}">{project.name}</a>
                    <p>{project.number_of_tasks} tasks</p>
                    <p>Created on {project.created_at}</p>
                    <p>Description {project.description ? project.description : ''}</p>
                </div>
            {/each}
        </div>

    </div>
</section>

<IntersectionObserver {element} on:intersect={load}>
    <div bind:this={element}>Load More</div>
</IntersectionObserver>
