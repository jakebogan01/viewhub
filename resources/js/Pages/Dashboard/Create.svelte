<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {Inertia} from '@inertiajs/inertia';
    import {inertia, page} from "@inertiajs/inertia-svelte";

    let form;
    export let errors;
    export let auth;
    export let tags;
    export let statuses;

    // sort tags and statuses by id in ascending order
    tags.sort((a, b) => a.id - b.id);
    statuses.sort((a, b) => a.id - b.id);

    $: form = {
        title: '',
        description: '',
        status_id: 1,
        tag_id: 1,
    };

    let handleSubmit = () => {
        Inertia.post('/dashboard/task/create', form, { replace: true });
    }
</script>

<svelte:head>
    <title>Create Task</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">
        <form on:submit|preventDefault|once={handleSubmit} class="max-w-md mx-auto mt-8">
            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="title"> Title </label>

                <input bind:value={form.title} class="border border-gray-400 p-2 w-full" type="text" name="title" id="title"/>
                {#if errors.title}
                    <p class="text-red-500 text-xs mt-1"> {errors.title} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="description"> Description </label>

                <textarea bind:value={form.description} name="description" id="description" rows="4" class="border border-gray-400 p-2 w-full"></textarea>
                {#if errors.description}
                    <p class="text-red-500 text-xs mt-1"> {errors.description} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label for="status_id" class="block text-sm font-medium leading-6 text-gray-900">Status</label>

                <select bind:value={form.status_id} id="status_id" name="status_id" class="mt-2 block w-full capitalize rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    {#each statuses as status (status.id)}
                        <option value={status.id}>{status.name}</option>
                    {/each}
                </select>
                {#if errors.status_id}
                    <p class="text-red-500 text-xs mt-1"> {errors.status_id} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label for="tag_id" class="block text-sm font-medium leading-6 text-gray-900">Tag</label>

                <select bind:value={form.tag_id} id="tag_id" name="tag_id" class="mt-2 block w-full capitalize rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    {#each tags as tag (tag.id)}
                        <option value={tag.id}>{tag.name}</option>
                    {/each}
                </select>
                {#if errors.tag_id}
                    <p class="text-red-500 text-xs mt-1"> {errors.tag_id} </p>
                {/if}
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500">Create</button>
            </div>
        </form>
    </div>
</section>
