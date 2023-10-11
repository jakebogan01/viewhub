<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";
    import IntersectionObserver from "svelte-intersection-observer";
    import {onMount} from "svelte";

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    /* svelte-ignore unused-export-let */
    export let tasks;

    console.log(tasks)

    let element;
    let allTasks = [];
    allTasks = [...tasks.data];

    if (window.performance) {
        if (performance.navigation.type === 1) {
            Inertia.get(tasks.first_page_url, {})
        }
    }

    const load = () => {
        if (tasks.next_page_url) {
            Inertia.get(tasks.next_page_url, {}, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    allTasks = [...allTasks, ...page.props.tasks.data];
                }
            })
        }
    }

    onMount(()=>{
        if (window.performance) {
            if (performance.navigation.type === 1) {
                Inertia.get(window.location.pathname, {}, {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                    onSuccess: () => {
                       window.location.replace("/dashboard/all-tasks");
                    }
                })
            }
        }
    });
</script>

<svelte:head>
    <title>All Tasks</title>
</svelte:head>

<section class="mb-[500px]">
    <nav class="relative flex items-center justify-between max-w-[69.375rem] mx-auto md:rounded-[0.625rem] px-6 py-6 md:px-9 md:py-8 bg-[#10263E] z-50">
        <div class="text-13 text-[#F3F4FE]">
            <a use:inertia={{ replace: true }} href="/dashboard" class="flex items-center space-x-2">
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                <span class="font-bold text-13 md:text-sm text-white">Go Back</span>
            </a>
            <h1 class="font-bold text-lg md:text-2xl md:mt-2">All Tasks</h1>
        </div>
        <div>
            <a use:inertia href="/dashboard/task/create" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
        </div>
    </nav>

    <div class="flex gap-4 py-6">
        <div class="flex-1 space-y-6">
            {#each allTasks as task, i (task.id)}
                {#if i % 2 === 0}
                    <a use:inertia href="/dashboard/tasks/{task.slug}" class="block bg-white dark:bg-[#1E283A] hover:shadow-lg p-6 md:py-7 md:px-8 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
                        <div class="flex-1 md:pr-[1.5625rem]">
                            <h2 class="font-bold text-[#3A4374] group-hover:text-[#238AB6] dark:text-white md:text-lg leading-3">{task.title}</h2>
                            <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>
                            <div class="flex justify-between pt-4">
                                <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#17202F] rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] text-base">{task.tag}</span>
                                <div class="flex space-x-5">
                                    <div class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M1 8.25a1.25 1.25 0 112.5 0v7.5a1.25 1.25 0 11-2.5 0v-7.5zM11 3V1.7c0-.268.14-.526.395-.607A2 2 0 0114 3c0 .995-.182 1.948-.514 2.826-.204.54.166 1.174.744 1.174h2.52c1.243 0 2.261 1.01 2.146 2.247a23.864 23.864 0 01-1.341 5.974C17.153 16.323 16.072 17 14.9 17h-3.192a3 3 0 01-1.341-.317l-2.734-1.366A3 3 0 006.292 15H5V8h.963c.685 0 1.258-.483 1.612-1.068a4.011 4.011 0 012.166-1.73c.432-.143.853-.386 1.011-.814.16-.432.248-.9.248-1.388z" /></svg>
                                        <span class="dark:text-[#D1D7E9]">{task.likes}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                        <svg width="18" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.62 16H1.346l.902-.91c.486-.491.79-1.13.872-1.823C1.036 11.887 0 9.89 0 7.794 0 3.928 3.52 0 9.03 0 14.87 0 18 3.615 18 7.455c0 3.866-3.164 7.478-8.97 7.478-1.017 0-2.078-.137-3.025-.388A4.705 4.705 0 012.62 16z" fill="#CDD2EE" fill-rule="nonzero"/></svg>
                                        <span class="dark:text-[#D1D7E9]">{task.comment_count}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                {/if}
            {/each}
        </div>
        <div class="flex-1 space-y-6">
            {#each allTasks as task, i (task.id)}
                {#if i % 2 !== 0}
                    <a use:inertia href="/dashboard/tasks/{task.slug}" class="block bg-white dark:bg-[#1E283A] hover:shadow-lg p-6 md:py-7 md:px-8 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
                        <div class="flex-1 md:pr-[1.5625rem]">
                            <h2 class="font-bold text-[#3A4374] group-hover:text-[#238AB6] dark:text-white md:text-lg leading-3">{task.title}</h2>
                            <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>
                            <div class="flex justify-between pt-4">
                                <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#17202F] rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] text-base">{task.tag}</span>
                                <div class="flex space-x-5">
                                    <div class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M1 8.25a1.25 1.25 0 112.5 0v7.5a1.25 1.25 0 11-2.5 0v-7.5zM11 3V1.7c0-.268.14-.526.395-.607A2 2 0 0114 3c0 .995-.182 1.948-.514 2.826-.204.54.166 1.174.744 1.174h2.52c1.243 0 2.261 1.01 2.146 2.247a23.864 23.864 0 01-1.341 5.974C17.153 16.323 16.072 17 14.9 17h-3.192a3 3 0 01-1.341-.317l-2.734-1.366A3 3 0 006.292 15H5V8h.963c.685 0 1.258-.483 1.612-1.068a4.011 4.011 0 012.166-1.73c.432-.143.853-.386 1.011-.814.16-.432.248-.9.248-1.388z" /></svg>
                                        <span class="dark:text-[#D1D7E9]">{task.likes}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                        <svg width="18" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.62 16H1.346l.902-.91c.486-.491.79-1.13.872-1.823C1.036 11.887 0 9.89 0 7.794 0 3.928 3.52 0 9.03 0 14.87 0 18 3.615 18 7.455c0 3.866-3.164 7.478-8.97 7.478-1.017 0-2.078-.137-3.025-.388A4.705 4.705 0 012.62 16z" fill="#CDD2EE" fill-rule="nonzero"/></svg>
                                        <span class="dark:text-[#D1D7E9]">{task.comment_count}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                {/if}
            {/each}
        </div>
    </div>
<!--    <div class="grid grid-cols-2 gap-4 py-6">-->
<!--        {#each allTasks as task (task.id)}-->
<!--            <div class="flex flex-col justify-center items-center border border-red-500 w-full p-5">-->
<!--                <a use:inertia href="/dashboard/tasks/{task.slug}">{task.title}</a>-->
<!--            </div>-->
<!--        {/each}-->
<!--    </div>-->
</section>

<IntersectionObserver {element} on:intersect={load}>
    <div bind:this={element}></div>
</IntersectionObserver>
