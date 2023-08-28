<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";

    export let task;
    /* svelte-ignore unused-export-let */
    export let flash = {};
    console.log(task);
</script>

<svelte:head>
    <title>Task</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">
        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <div class="flex justify-end">
            <a use:inertia href="/dashboard/task/{task.slug}/edit" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">Edit Task</a>
            <button type="button" use:inertia="{{ href: `/dashboard/task/${task.id}`, method: 'delete', replace: true, }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>
        </div>
        <div class="space-y-4 mt-5">
            <div class="border border-gray-200 rounded p-4 my-2">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold pb-3">{task.title}</h2>

                    <span>Created by: {task.user}</span>
                </div>
                <p>{task.description}</p>
                <a use:inertia={{ replace: true }} href="/dashboard" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                <button type="button" use:inertia="{{ href: `/dashboard/tasks/${task.id}/like`, method: 'post', replace: true, preserveScroll: true, }}">Like</button>
                <span>{task.likes}</span>
            </div>
        </div>

        <a use:inertia={{ replace: true }} href="/dashboard">Go Back</a>
    </div>
</section>
