<script context="module">
    export {default as layout} from "../../Layouts/Onboarding.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
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
        <button type="button">Create Task</button>

        <form>
            <input type="text" id="project_name" name="project_name" placeholder="Project Name">
        </form>
    </div>
</section>
