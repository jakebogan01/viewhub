<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";

    export let task;
    /* svelte-ignore unused-export-let */
    export let flash = {};
    console.log(task.images);

    let viewImage = false;
    let imageSrc = '';

    const inspectImage = (image) => {
        viewImage = true;
        imageSrc = image;
    }
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

                {#if task.images.length > 0}
                    <ul role="list" class="mx-auto my-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none">
                        {#each task.images as image}
                            <li on:click={()=>{inspectImage(image)}} on:keydown>
                                <img src="/images/tasks/{image.path}" class="aspect-[3/2] w-full rounded-md object-cover" alt={image.name}>
                            </li>
                        {/each}
                    </ul>
                {/if}

                <div>
                    <a use:inertia={{ replace: true }} href="/dashboard" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                    <button type="button" use:inertia="{{ href: `/dashboard/tasks/${task.id}/like`, method: 'post', replace: true, preserveScroll: true, }}">Like</button>
                    <span>{task.likes}</span>
                </div>
            </div>
        </div>

        {#if viewImage}
            <div class="absolute flex justify-center items-center inset-0 bg-black bg-opacity-20 z-50 p-20">
                <div class="relative bg-black w-full h-full overflow-hidden p-4">
                    <span on:click={()=>{viewImage = false;}} on:keydown class="absolute right-2 top-2 text-white cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                    </span>
                    <img src="/images/tasks/{imageSrc.path}" class="w-full h-full object-contain" alt={imageSrc.name}>
                </div>
            </div>
        {/if}

        <a use:inertia={{ replace: true }} href="/dashboard">Go Back</a>
    </div>
</section>
