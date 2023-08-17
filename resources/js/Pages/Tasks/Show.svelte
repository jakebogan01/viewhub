<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";

    export let task;
    console.log(task);
</script>

<svelte:head>
    <title>Task</title>
</svelte:head>

<div class="space-y-4 mt-5">
    <div class="border border-gray-200 rounded p-4 my-2">
        <h2 class="text-2xl font-bold pb-3">{task.title}</h2>
        <p>{task.description}</p>
        <a use:inertia href="/" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag.name}</a>
    </div>
</div>

<a use:inertia href="/">Go Back</a>

