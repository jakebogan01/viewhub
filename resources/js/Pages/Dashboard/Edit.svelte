<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from '@inertiajs/inertia';
    import FilePond, { registerPlugin, supported } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    import 'filepond/dist/filepond.min.css';
    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

    /* svelte-ignore unused-export-let */
    export let errors;
    export let tags;
    export let csrf_token;
    export let task;
    export let statuses;
    export let auth;

    // sort tags by id in ascending order
    tags.sort((a, b) => a.id - b.id);

    let form = useForm({
        title: task.title,
        description: task.description,
        tag_id: task.tag,
        status_id: task.status,
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
        $form.patch(`/dashboard/task/${task.id}`, {
            replace: true,
        })
    }
</script>

<svelte:head>
    <title>Edit {task.title}</title>
</svelte:head>


<section class="mb-10 max-w-[530px] mx-auto px-4 md:px-0">
    <div class="mb-6">
        <a use:inertia={{ replace: true }} href="/dashboard/tasks/{task.slug}" class="flex items-center space-x-2">
            {#if auth.user.dark_mode}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {:else}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {/if}
            <span class="font-bold text-13 md:text-sm text-[#647196] dark:text-white">Go Back</span>
        </a>
    </div>

    <form on:submit|preventDefault={submit} enctype="multipart/form-data" class="relative bg-white dark:bg-[#222222] hover:shadow-lg p-6 rounded-[0.625rem] mt-12 md:mt-16">
        <span class="absolute -top-5 md:-top-7 h-10 md:h-14"><svg fill="none" height="56" viewBox="0 0 56 56" width="56" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><radialGradient id="a" cx="0" cy="0" gradientTransform="matrix(-59.30841458 72.17498989 -72.17498989 -59.30841458 58.184 -5.81647)" gradientUnits="userSpaceOnUse" r="1"><stop offset="0" stop-color="#e84d70"/><stop offset=".530886" stop-color="#a337f6"/><stop offset="1" stop-color="#28a7ed"/></radialGradient><circle cx="28" cy="28" fill="url(#a)" r="28"/><path clip-rule="evenodd" d="m29.0825 19.4809 3.749-3.4809 6.5135 6.2719-3.5481 3.6801zm-13.0825 20.114c.9207-3.9416 3.4867-14.0206 3.4867-14.0206l8.2028-4.8208 6.8314 6.3964-5.2192 7.8201-12.9887 5.03 6.1573-5.7896c1.0427.3894 2.5154.0374 3.3115-.8368 1.051-1.1481.9721-2.9313-.1768-3.9822-1.149-1.051-3.0202-1.051-4.0711.0971-.7829.8559-1.0527 2.3659-.6052 3.3613z" fill="#fff" fill-rule="evenodd"/></svg></span>
        <div class="mt-6 md:mt-8">
            <h3 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-white truncate" id="modal-title">Editing '{task.title}'</h3>
            <div class="mt-6 md:mt-16 space-y-6">
                <div>
                    <label for="title" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Feedback Title</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Add a short, descriptive headline</span>
                    <div class="mt-3">
                        <input type="text" value={task.title} on:input={(e)=>{$form.title = e.target.value}} name="title" id="title" spellcheck="true" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600" required>
                    </div>
                    {#if $form.errors.title}
                        <p class="text-red-500 text-[0.9rem]">{$form.errors.title} </p>
                    {/if}
                </div>
                <div>
                    <label for="status_id" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Department</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Choose you department for your feedback</span>
                    <div class="mt-3">
                        <select bind:value={task.status_id} on:input={(e)=>{$form.status_id = parseInt(e.target.value)}} id="status_id" name="status_id" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                            {#each statuses as status (status.id)}
                                {#if task.status === status.id}
                                    <option value={status.id} selected>{status.name}</option>
                                {:else}
                                    <option value={status.id}>{status.name}</option>
                                {/if}
                            {/each}
                        </select>
                    </div>
                    {#if $form.errors.tag_id}
                        <p class="text-red-500 text-[0.9rem]"> {$form.errors.tag_id} </p>
                    {/if}
                </div>
                <div>
                    <label for="tag_id" class="block font-bold text-13 md:text-sm text-[#3A4374] dark:text-white">Category</label>
                    <span class="text-13 md:text-sm text-[#647196] dark:text-[#D1D7E9]">Choose a category for your feedback</span>
                    <div class="mt-3">
                        <select bind:value={task.tag_id} on:input={(e)=>{$form.tag_id = parseInt(e.target.value)}} id="tag_id" name="tag_id" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                            {#each tags as tag (tag.id)}
                                {#if task.tag === tag.id}
                                    <option value={tag.id} selected>{tag.name}</option>
                                {:else}
                                    <option value={tag.id}>{tag.name}</option>
                                {/if}
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
                        <textarea on:input={(e)=>{$form.description = e.target.value}} rows="4" cols="50" spellcheck="true" name="description" id="description" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 placeholder:text-[#3A4374] dark:placeholder:text-white/40 ring-inset dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600" style="resize: none;" required>{task.description}</textarea>
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
                {#if task.images.length > 0}
                    <ul role="list" class="mx-auto my-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-3 lg:mx-0 lg:max-w-none">
                        {#each task.images as image (image.id)}
                            <li class="relative rounded-md overflow-hidden">
                                <div class="absolute inset-0 w-full h-full bg-black bg-opacity-20"></div>
                                <button type="button" use:inertia="{{ href: `/dashboard/image/delete/${image.id}`, method: 'delete', replace: true, }}" class="absolute top-0 right-0 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                                </button>
                                <img src="/images/user{task.user_id}/{image.path}" class="aspect-[3/2] w-full object-cover" alt={image.name}>
                            </li>
                        {/each}
                    </ul>
                {/if}
            </div>
        </div>
        <div class="flex flex-col md:flex-row md:justify-end items-center md:space-x-4 space-y-4 md:space-y-0 mt-10 mb-8">
            <button type="button" use:inertia={{ href: `/dashboard/tasks/${task.slug}`, replace: true }} class="mt-3 inline-flex w-full justify-center items-center rounded-[0.625rem] bg-red-500 hover:bg-red-400 px-3 h-10 md:w-[5.8125rem] text-[#F2F4FE] ring-1 ring-inset ring-gray-300 dark:ring-black sm:col-start-1 sm:mt-0">Cancel</button>
            <button type="submit" class="inline-flex w-full justify-center items-center rounded-[0.625rem] bg-[#238AB6] hover:bg-[#186489] px-3 h-10 md:w-[9rem] text-[#F2F4FE] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2" disabled={$form.processing}>Update feedback</button>
        </div>
    </form>
</section>
