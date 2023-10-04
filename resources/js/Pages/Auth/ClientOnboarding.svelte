<script context="module">
    export {default as layout} from "../../Layouts/Onboarding.svelte";
</script>

<script>
    import {Inertia, router} from '@inertiajs/inertia';
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    let showProjectInput = false;

    let form = useForm({
        project_name: null,
    });

    function submit() {
        $form.post(`/dashboard/task/create`, {
            replace: true,
        })
    }
</script>

<svelte:head>
    <title>create</title>
</svelte:head>

<section class="p-6 dark:bg-gray-500">
    <div class="max-w-3xl mx-auto">

        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <a use:inertia href="/dashboard/projects">View Dashboard</a>
        <span class="block">or</span>

        {#if !showProjectInput}
            <button type="button" on:click={()=>{showProjectInput = true}}>Create Task</button>
        {/if}

        <form on:submit|preventDefault={submit} enctype="multipart/form-data">
            {#if showProjectInput}
                <input bind:value={$form.project_name} type="text" id="project_name" name="project_name" placeholder="Project Name">
                {#if $form.errors.project_name}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.project_name} </p>
                {/if}

                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500" disabled={$form.processing}>Create</button>
            {/if}
        </form>
    </div>
</section>
