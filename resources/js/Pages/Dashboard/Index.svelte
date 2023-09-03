<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    export let tasks;
    export let count;
    export let filters;
    export let tags;
    export let user;

    console.log('Filters: ', filters);
    console.log('Count: ', count);
    console.log('Tasks: ', tasks);
    console.log('Tags: ', tags);
    console.log('User: ', user);

    let statuses = ['Pending', 'In Progress', 'Live'];
    let route = '/dashboard';
    let showStatusDropdown = false;
    let showSortByDropdown = false;
    let search = filters.search || '';
    let updateStatusDropdownSelection = filters.status || 'All';
    let updateSortByDropdownSelection = filters.sortby || 'Newest';
    let timer;
    let rotateArrow = false;

    // debounce search input
    const debounce = v => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (v !== '') {
                Inertia.get(route, {search: v, status: filters.status, tag: filters.tag, sortby: filters.sortby}, {
                    // search input doesn't reload the page and lose focus
                    preserveState: true,
                    // prevents the browser's history from being updated
                    replace: true,
                    // preserve scroll position on back navigation
                    preserveScroll: true,
                });
            } else {
                Inertia.get(route, {status: filters.status, tag: filters.tag, sortby: filters.sortby}, {
                // search input doesn't reload the page and lose focus
                preserveState: true,
                // prevents the browser's history from being updated
                replace: true,
                // preserve scroll position on back navigation
                preserveScroll: true,
            });
            }
        }, 300);
    }


    $: filterByDateData = {
        search: filters.search, 
        tag: filters.tag, 
        status: filters.status,
        ...(!rotateArrow) && {sortby: 'oldest'},
    }

    const test = () => {
        rotateArrow = !rotateArrow;
        Inertia.get(route, filterByDateData, {
            // prevents the browser's history from being updated
            replace: true,
        });
    }
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">

        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <div class="flex justify-between items-center mb-12">
            <div>
                <p>Total: {count}</p>
            </div>
            <div>
                <a use:inertia href="/dashboard/task/create" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">Create Task</a>
            </div>
        </div>

        <div class="flex space-x-8">
            <div class="basis-4/12">
                <!--list of statuses-->
                <div>
                    <div class="relative mt-2">
                        <label for class="font-bold">Statuses:</label>
                        <button on:click={()=>{ showStatusDropdown = !showStatusDropdown }} type="button" class="relative w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">
                            <span class="block truncate">{@html updateStatusDropdownSelection}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd"/></svg>
                        </span>
                        </button>

                        {#if showStatusDropdown}
                            <div class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-0">
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>All</button>
                                {#each statuses as status}
                                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: status, tag: filters.tag, sortby: filters.sortby }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>{status}</button>
                                {/each}
                            </div>
                        {/if}
                    </div>
                </div>

                <!--list of tags-->
                <div class="mt-4">
                    <div class="bg-gray-300 p-6 min-w-[15.9375rem] max-w-[15.9375rem] rounded-[0.625rem]">
                        <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                            <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, sortby: filters.sortby }, replace: true, }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">All</button>

                            {#each tags as tag (tag.id)}
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, tag: tag.name, sortby: filters.sortby }, replace: true, }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">{tag.name}</button>
                            {/each}
                        </div>
                    </div>
                </div>
            </div>

            <div class="basis-full">
                <!--Search-->
                <div class="flex justify-between mb-6">
                    <h1 class="text-3xl">All Tasks</h1>
                    <input on:keyup={({ target: { value } }) => debounce(value)} value={search} type="text"
                           placeholder="Search by Title or Tag" class="border px-2 rounded-lg"/>
                </div>

                <!--sort by oldest and newest-->
                <div class="flex justify-end">
                    <!-- use:inertia="{{ href: route, method: 'get', data: data, replace: true }}" -->
                    <button type="button" on:click={test}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transform transition-transform {rotateArrow ? 'rotate-180' : 'rotate-0'}"><path fill-rule="evenodd" d="M11.47 7.72a.75.75 0 011.06 0l7.5 7.5a.75.75 0 11-1.06 1.06L12 9.31l-6.97 6.97a.75.75 0 01-1.06-1.06l7.5-7.5z" clip-rule="evenodd" /></svg>
                    </button>
                </div>

                <!--list of tasks-->
                <div class="space-y-4 mt-5">
                    {#if tasks.data.length < 1}
                        <p>There currently are no tasks</p>
                    {:else}
                        {#each tasks.data as task (task.id)}
                            <div class="border border-gray-200 rounded p-4 my-2">
                                <div class="flex items-center justify-between">
                                    <a use:inertia href="/dashboard/tasks/{task.slug}">
                                        <h2 class="text-2xl font-bold pb-3">{task.title}</h2>
                                    </a>
                                    <span class="whitespace-nowrap">Status: <span class="text-[#3bba2d] bg-[#d4facf] p-2 rounded">{task.status}</span></span>
                                </div>
                                <p class="mt-4">{task.description}</p>
                                <a use:inertia href="/dashboard" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                                <button type="button" use:inertia="{{ href: `/dashboard/tasks/${task.id}/like`, method: 'post', replace: true, preserveScroll: true, }}">Like</button>
                                <span>{task.likes}</span>
                            </div>
                        {:else}
                            <p>There currently are no tasks</p>
                        {/each}
                    {/if}
                </div>

                <!--pagination-->
                <div>
                    <div class="mt-6">
                        {#if tasks.prev_page_url}
                            <a use:inertia={{ replace: true }} href="{tasks.first_page_url}" class="px-1">First Page</a>
                            <a use:inertia={{ replace: true }} href="{tasks.prev_page_url}" class="px-1">&laquo; Prev</a>
                        {/if}

                        {#if tasks.next_page_url}
                            <a use:inertia={{ replace: true }} href="{tasks.next_page_url}" class="px-1">Next &raquo;</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
