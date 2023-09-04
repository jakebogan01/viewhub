<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";
    import {Inertia, router} from '@inertiajs/inertia';
    import FilePond, { registerPlugin, supported } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    import 'filepond/dist/filepond.min.css';
    import {DateInput} from "date-picker-svelte";
    import Editor from "@tinymce/tinymce-svelte";

    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

    /* svelte-ignore unused-export-let */
    export let errors;
    export let tags;
    export let csrf_token;
    export let task;

    // sort tags by id in ascending order
    tags.sort((a, b) => a.id - b.id);

    let includeDate = task.due_date !== null;

    let form = useForm({
        title: task.title,
        description: task.description,
        due_date: includeDate ? new Date(task.due_date) : null,
        tag_id: task.tag,
        images: [],
    });

    $: {
        if (!includeDate) {
            $form.due_date = null;
        } else {
            $form.due_date = task.due_date ? new Date(task.due_date) : new Date();
        }
    }

    $: console.log($form.due_date)

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
        $form.patch(`/dashboard/task/${task.id}`, {
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

                <Editor
                    apiKey="c6nd39g8lr6fi2qj6ed3jl7xyimi98cd389sbn1crir27xph"
                    id="description"
                    inline=false
                    disabled=false
                    modelEvents="input change"
                    value={task.description}
                    on:keydown={(e)=>{$form.description = e.detail.event.currentTarget.innerHTML}}
                    text="readonly-text-output"
                    name="description"
                    {conf}
                />
<!--                <textarea on:input={(e)=>{$form.description = e.target.value}} name="description" id="description" rows="4" class="border border-gray-400 p-2 w-full">{task.description}</textarea>-->
                {#if $form.errors.description}
                    <p class="text-red-500 text-xs mt-1"> {$form.errors.description} </p>
                {/if}
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" id="includeDate" name="includeDate" value={includeDate} on:click={()=>{includeDate = !includeDate}} checked={includeDate}>
                <label for="includeDate" class="mr-6 ml-1">Include a Due Date</label>

                {#if includeDate}
                    <DateInput bind:value={$form.due_date} format="MM-dd-yyyy" closeOnSelection="true" browseWithoutSelecting="true" disabled={!includeDate} />
                    {#if $form.errors.due_date}
                        <p class="text-red-500 text-xs mt-1"> {$form.errors.due_date} </p>
                    {/if}
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
                <div class="app">
                    <FilePond
                        name="image"
                        server={options}
                        allowMultiple={true}
                    />
                </div>
            </div>

            {#if task.images.length > 0}
                <ul role="list" class="mx-auto my-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-3 lg:mx-0 lg:max-w-none">
                    {#each task.images as image (image.id)}
                        <li class="relative rounded-md overflow-hidden">
                            <div class="absolute inset-0 w-full h-full bg-black bg-opacity-20"></div>
                            <button type="button" use:inertia="{{ href: `/dashboard/task/delete/${image.id}`, method: 'delete', replace: true, }}" class="absolute top-0 right-0 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                            </button>
                            <img src="/images/tasks/{image.path}" class="aspect-[3/2] w-full object-cover" alt={image.name}>
                        </li>
                    {/each}
                </ul>
            {/if}

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500" disabled={$form.processing}>Update</button>
                <a use:inertia href="/dashboard" class="bg-red-400 text-white rounded py-2 px-4 hover:bg-red-500">Cancel</a>
            </div>
        </form>
    </div>
</section>
