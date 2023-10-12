<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {Inertia} from '@inertiajs/inertia';
    import {useForm, inertia} from "@inertiajs/inertia-svelte";
    import FilePond, { registerPlugin } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    import 'filepond/dist/filepond.min.css';
    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

    /* svelte-ignore unused-export-let */
    export let errors;
    /* svelte-ignore unused-export-let */
    export let auth;
    export let tags;
    export let csrf_token;

    // sort tags by id in ascending order
    tags.sort((a, b) => a.id - b.id);

    let form = useForm({
        title: '',
        description: '',
        tag_id: 1,
        images: [],
    });

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
</script>

<svelte:head>
    <title>Create Task</title>
</svelte:head>

<section class="mb-10 max-w-[530px] mx-auto px-4 md:px-0">
    <div class="mb-6">
        <a use:inertia={{ replace: true }} href="/dashboard" class="flex items-center space-x-2">
            {#if auth.user.dark_mode}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {:else}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {/if}
            <span class="font-bold text-13 md:text-sm text-[#647196] dark:text-white">Go Home</span>
        </a>
    </div>

    <form on:submit|preventDefault={submit} enctype="multipart/form-data" class="relative bg-white dark:bg-[#222222] hover:shadow-lg p-6 rounded-[0.625rem] mt-12 md:mt-16">
        <span class="absolute -top-5 md:-top-7 h-10 md:h-14"><svg width="56" height="56" xmlns="http://www.w3.org/2000/svg"><defs><radialGradient cx="103.9%" cy="-10.387%" fx="103.9%" fy="-10.387%" r="166.816%" id="a"><stop stop-color="#E84D70" offset="0%"/><stop stop-color="#A337F6" offset="53.089%"/><stop stop-color="#28A7ED" offset="100%"/></radialGradient></defs><g fill="none" fill-rule="evenodd"><circle fill="url(#a)" cx="28" cy="28" r="28"/><path fill="#FFF" fill-rule="nonzero" d="M30.343 36v-5.834h5.686v-4.302h-5.686V20h-4.597v5.864H20v4.302h5.746V36z"/></g></svg></span>
        <div class="mt-6 md:mt-8">
            <h3 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-white" id="modal-title">Create New Feedback</h3>
            <div class="mt-6 md:mt-16 space-y-6">
                <div>
                    <label for="title" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Feedback Title</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Add a short, descriptive headline</span>
                    <div class="mt-3">
                        <input type="text" bind:value={$form.title} name="title" id="title" spellcheck="true" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600" required>
                    </div>
                    {#if $form.errors.title}
                        <p class="text-red-500 text-[0.9rem]">{$form.errors.title} </p>
                    {/if}
                </div>
                <div>
                    <label for="tag_id" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Category</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Choose a category for your feedback</span>
                    <div class="mt-3">
                        <select bind:value={$form.tag_id} id="tag_id" name="tag_id" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                            {#each tags as tag (tag.id)}
                                <option value={tag.id}>{tag.name}</option>
                            {/each}
                        </select>
                    </div>
                    {#if $form.errors.tag_id}
                        <p class="text-red-500 text-[0.9rem]"> {$form.errors.tag_id} </p>
                    {/if}
                </div>
                <div>
                    <label for="description" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Feedback Detail</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Include any specific comments on what should be improved, added, etc.</span>
                    <div class="mt-3">
                        <textarea bind:value={$form.description} rows="4" cols="50" spellcheck="true" name="description" id="description" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600" style="resize: none;" required></textarea>
                    </div>
                    {#if $form.errors.description}
                        <p class="text-red-500 text-[0.9rem]">{$form.errors.description}</p>
                    {/if}
                </div>
                <div>
                    <FilePond
                        name="image"
                        server={options}
                        allowMultiple={true}
                    />
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row md:justify-end items-center md:space-x-4 space-y-4 md:space-y-0 mt-10 mb-8">
            <button type="button" use:inertia={{ href: '/dashboard', replace: true }} class="mt-3 inline-flex w-full justify-center items-center rounded-[0.625rem] bg-red-500 hover:bg-red-400 px-3 h-10 md:w-[5.8125rem] text-[#F2F4FE] ring-1 ring-inset ring-gray-300 dark:ring-black sm:col-start-1 sm:mt-0">Cancel</button>
            <button type="submit" class="inline-flex w-full justify-center items-center rounded-[0.625rem] bg-[#238AB6] hover:bg-[#186489] px-3 h-10 md:w-[9rem] text-[#F2F4FE] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2" disabled={$form.processing}>Add feedback</button>
        </div>
    </form>
</section>

<style global>
    @import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
</style>
