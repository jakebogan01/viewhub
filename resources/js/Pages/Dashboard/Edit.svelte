<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    export let errors;
    export let tags;
    export let statuses;
    export let task;

    // sort tags and statuses by id in ascending order
    tags.sort((a, b) => a.id - b.id);
    statuses.sort((a, b) => a.id - b.id);

    let form = useForm({
        title: task.title,
        description: task.description,
        status_id: task.status,
        tag_id: task.tag
    });

    function submit() {
        $form.patch(`/dashboard/task/${task.id}`, {
            replace: true,
        })
    }
</script>

<svelte:head>
    <title>Edit {task.title}</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">
        {#if $form.progress}
            <progress value={$form.progress.percentage} max="100">
                {$form.progress.percentage}%
            </progress>
        {/if}

        <form on:submit|preventDefault={submit} class="max-w-md mx-auto mt-8">
            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="title"> Title </label>

                <input value={task.title} on:input={(e)=>{$form.title = e.target.value}} class="border border-gray-400 p-2 w-full" type="text" name="title" id="title"/>
                {#if $form.errors.title}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.title} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="description"> Description </label>

                <textarea on:input={(e)=>{$form.description = e.target.value}} name="description" id="description" rows="4" class="border border-gray-400 p-2 w-full">{task.description}</textarea>
                {#if $form.errors.description}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.description} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label for="status_id" class="block text-sm font-medium leading-6 text-gray-900">Status</label>

                <select bind:value={task.status_id} on:input={(e)=>{$form.status_id = parseInt(e.target.value)}} id="status_id" name="status_id" class="mt-2 block w-full capitalize rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    {#each statuses as status (status.id)}
                        {#if task.status === status.id}
                            <option value={status.id} selected>{status.name}</option>
                        {:else}
                            <option value={status.id}>{status.name}</option>
                        {/if}
                    {/each}
                </select>
                {#if $form.errors.status_id}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.status_id} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label for="tag_id" class="block text-sm font-medium leading-6 text-gray-900">Tag</label>

                <select bind:value={task.tag_id} on:input={(e)=>{$form.tag_id = parseInt(e.target.value)}} id="tag_id" name="tag_id" class="mt-2 block w-full capitalize rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    {#each tags as tag (tag.id)}
                        {#if task.tag === tag.id}
                            <option value={tag.id} selected>{tag.name}</option>
                        {:else}
                            <option value={tag.id}>{tag.name}</option>
                        {/if}
                    {/each}
                </select>
                {#if $form.errors.tag_id}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.tag_id} </p>
                {/if}
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500" disabled={$form.processing}>Update</button>
                <a use:inertia href="/dashboard" class="bg-red-400 text-white rounded py-2 px-4 hover:bg-red-500">Cancel</a>
            </div>
        </form>
    </div>
</section>
