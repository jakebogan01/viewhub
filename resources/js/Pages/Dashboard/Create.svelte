<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {Inertia, router} from '@inertiajs/inertia';
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let errors;
    /* svelte-ignore unused-export-let */
    export let auth;
    export let tags;
    export let statuses;
    export let csrf_token;

    import FilePond, { registerPlugin, supported } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);
    let pond;
    let options = {
        url: '',
        process: {
            url: '/dashboard/task/upload',
            method: 'POST',
            onload: handleFilePondLoad
        },
        revert: handleFilePondRevert,
        headers: {
            'X-CSRF-TOKEN': csrf_token
        }
    }

    // sort tags and statuses by id in ascending order
    tags.sort((a, b) => a.id - b.id);
    statuses.sort((a, b) => a.id - b.id);

    let form = useForm({
        title: '',
        description: '',
        tag_id: 1,
        images: [],
    });

    function handleFilePondLoad(response) {
        $form.images.push(response);
        return response;
    }

    function handleFilePondRevert(uniqueId, load, error) {
        $form.images = $form.images.filter((image) => image !== uniqueId);

        Inertia.delete(`/dashboard/task/revert/${uniqueId}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('image deleted');
            }
        });

        load();
    }

    function submit() {
        $form.post(`/dashboard/task/create`, {
            replace: true,
        })
    }
</script>

<svelte:head>
    <title>Create Task</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">
        <form on:submit|preventDefault={submit} class="max-w-md mx-auto mt-8" enctype="multipart/form-data">
            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="title"> Title </label>

                <input bind:value={$form.title} class="border border-gray-400 p-2 w-full" type="text" name="title" id="title"/>
                {#if $form.errors.title}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.title} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="description"> Description </label>

                <textarea bind:value={$form.description} name="description" id="description" rows="4" class="border border-gray-400 p-2 w-full"></textarea>
                {#if $form.errors.description}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.description} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label for="tag_id" class="block text-sm font-medium leading-6 text-gray-900">Tag</label>

                <select bind:value={$form.tag_id} id="tag_id" name="tag_id" class="mt-2 block w-full capitalize rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    {#each tags as tag (tag.id)}
                        <option value={tag.id}>{tag.name}</option>
                    {/each}
                </select>
                {#if $form.errors.tag_id}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.tag_id} </p>
                {/if}
            </div>

            <div class="mb-6">
                <div class="app">
                    <FilePond
                        bind:this={pond}
                        class="my-pond"
                        name="image"
                        server={options}
                        allowMultiple={true}
                        acceptedFileTypes={['image/*']}
                    />
                </div>
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500" disabled={$form.processing}>Create</button>
                <a use:inertia href="/dashboard" class="bg-red-400 text-white rounded py-2 px-4 hover:bg-red-500">Cancel</a>
            </div>
        </form>
    </div>
</section>

<style global>
    @import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
</style>
