<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {Inertia, router} from '@inertiajs/inertia';
    import {useForm, inertia} from "@inertiajs/inertia-svelte";
    import FilePond, { registerPlugin, supported } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    import 'filepond/dist/filepond.min.css';
    import { DateInput } from 'date-picker-svelte';
    import Editor from '@tinymce/tinymce-svelte';
    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

    /* svelte-ignore unused-export-let */
    export let errors;
    /* svelte-ignore unused-export-let */
    export let auth;
    export let tags;
    export let csrf_token;

    // sort tags by id in ascending order
    tags.sort((a, b) => a.id - b.id);

    let includeDate = false;
    let makePriority = 0;

    let form = useForm({
        title: '',
        description: '',
        priority: 0,
        due_date: null,
        tag_id: 1,
        images: [],
    });

    $: {
        if (!makePriority) {
            $form.priority = 0;
        } else {
            $form.priority = 1;
        }

        if (!includeDate) {
            $form.due_date = null;
        } else {
            $form.due_date = new Date();
        }
    }

    let options = {
        url: '',
        process: {
            url: '/dashboard/image/upload',
            method: 'POST',
            onload: handleFilePondLoad
        },
        revert: handleFilePondRevert,
        headers: {
            'X-CSRF-TOKEN': csrf_token
        }
    }

    function handleFilePondLoad(response) {
        $form.images.push(response);
        return response;
    }

    function handleFilePondRevert(uniqueId, load, error) {
        $form.images = $form.images.filter((image) => image !== uniqueId);

        Inertia.delete(`/dashboard/image/revert/${uniqueId}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('image deleted');
            }
        });

        load();
        console.log(error);
    }

    function submit() {
        $form.post(`/dashboard/task/create`, {
            replace: true,
        })
    }

    let conf = {
        height: 300,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'preview',
            'anchor', 'code'
        ],
        toolbar: 'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | code |  bullist numlist outdent indent | ',
        menubar: '',
        statusbar: false
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
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700"> Description </label>

                <Editor
                    apiKey="c6nd39g8lr6fi2qj6ed3jl7xyimi98cd389sbn1crir27xph"
                    id="description"
                    inline=false
                    disabled=false
                    bind:value={$form.description}
                    text="readonly-text-output"
                    name="description"
                    {conf}
                />
                {#if $form.errors.description}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.description} </p>
                {/if}
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" id="includeDate" name="includeDate" value={includeDate} on:click={()=>{includeDate = !includeDate}}>
                <label for="includeDate" class="mr-6 ml-1">Include a Due Date</label>

                {#if includeDate}
                    <DateInput bind:value={$form.due_date} format="MM-dd-yyyy" closeOnSelection="true" browseWithoutSelecting="true" disabled={!includeDate} />
                    {#if $form.errors.due_date}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.due_date} </p>
                    {/if}
                {/if}
            </div>

            <div class="flex items-center mb-6">
                <label for="priority" class="mr-6 ml-1">High Priority</label>
                <input type="checkbox" id="priority" name="priority" bind:value={$form.priority} on:click={()=>{makePriority = !makePriority}}>

                {#if $form.errors.priority}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.priority} </p>
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
                        name="image"
                        server={options}
                        allowMultiple={true}
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
