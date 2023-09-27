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
    export let projects;
    export let tags;
    export let user;

    console.log('Filters: ', filters);
    console.log('Count: ', count);
    console.log('Tasks: ', tasks);
    console.log('Projects: ', projects);
    console.log('Tags: ', tags);
    console.log('User: ', user);

    let statuses = ['Pending', 'In Progress', 'Live'];
    let route = '/dashboard';
    let showStatusDropdown = false;
    let showSortByDropdown = false;
    let search = filters.search || '';
    let updateStatusDropdownSelection = filters.status || 'All';
    let updateSortByDropdownSelection = filters.date ? 'Due Date' : filters.liked ? 'Most Popular' : filters.priority ? 'High Priority' :  'All';
    let timer;
    let rotateArrow = false;
    let enableDarkMode = auth.user.dark_mode;

    const toggleDarkMode = () => {
        enableDarkMode = !enableDarkMode;

        Inertia.post('/update-dark-mode', { dark_mode: enableDarkMode }, {
            replace: true,
            preserveScroll: true,
        });
    }

    // debounce search input
    const debounce = v => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (v !== '') {
                Inertia.get(route, {project: filters.project, search: v, status: filters.status, tag: filters.tag, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority}, {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                });
            } else {
                Inertia.get(route, {project: filters.project, status: filters.status, tag: filters.tag, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority}, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            });
            }
        }, 300);
    }

    $: filterByDateData = {
        project: filters.project,
        search: filters.search,
        tag: filters.tag,
        status: filters.status,
        date: filters.date,
        liked: filters.liked,
        priority: filters.priority,
        ...(!rotateArrow) && {sortby: 'oldest'},
    }

    const sortByCreatedBy = () => {
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

<section class="p-6 dark:bg-gray-500">
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
                <!--sort by-->
                <div>
                    <div class="relative mt-2">
                        <label for class="font-bold">Sort By:</label>
                        <button on:click={()=>{ showSortByDropdown = !showSortByDropdown }} type="button" class="relative w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">
                            <span class="block truncate">{@html updateSortByDropdownSelection}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd"/></svg>
                        </span>
                        </button>

                        {#if showSortByDropdown}
                            <div class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label">
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>All</button>
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status, date: true }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>Due Date</button>
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status, liked: true}, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>Most Popular</button>
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status, priority: true}, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>High Priority</button>
                            </div>
                        {/if}
                    </div>
                </div>

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
                            <div class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label">
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, tag: filters.tag, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>All</button>
                                {#each statuses as status}
                                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, status: status, tag: filters.tag, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority }, replace: true, }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>{status}</button>
                                {/each}
                            </div>
                        {/if}
                    </div>
                </div>

                <!--list of tags-->
                <div class="mt-4">
                    <div class="bg-gray-300 p-6 min-w-[15.9375rem] max-w-[15.9375rem] rounded-[0.625rem]">
                        <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                            <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, status: filters.status, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority }, replace: true, }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">All</button>

                            {#each tags as tag (tag.id)}
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { project: filters.project, search: filters.search, status: filters.status, tag: tag.name, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority }, replace: true, }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">{tag.name}</button>
                            {/each}
                        </div>
                    </div>
                </div>

                <!--list of projects-->
                <div class="mt-4">
                    <div class="bg-gray-300 p-6 min-w-[15.9375rem] max-w-[15.9375rem] rounded-[0.625rem]">
                        <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                            {#each projects as project (project.id)}
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, tag: filters.tag, project: project.name, sortby: filters.sortby, date: filters.date, liked: filters.liked, priority: filters.priority }, replace: true, }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">{project.name}</button>
                            {/each}
                        </div>
                    </div>
                </div>

                <!--dark mode-->
                <div class="mt-4">
                    <button type="button" on:click={toggleDarkMode} class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">Enable Dark Mode</button>
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
                    <button type="button" on:click={sortByCreatedBy}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transform transition-transform {rotateArrow ? 'rotate-180' : 'rotate-0'}"><path fill-rule="evenodd" d="M11.47 7.72a.75.75 0 011.06 0l7.5 7.5a.75.75 0 11-1.06 1.06L12 9.31l-6.97 6.97a.75.75 0 01-1.06-1.06l7.5-7.5z" clip-rule="evenodd" /></svg>
                    </button>
                </div>

                <!--list of tasks-->
                <div class="space-y-4 mt-5">
                    {#if tasks.data.length < 1}
                        <p>There currently are no tasks</p>
                    {:else}
                        {#each tasks.data as task (task.id)}
                            <div class="relative border border-gray-200 rounded p-4 my-2">
                                {#if task.priority}
                                    <span class="absolute right-1 top-1 text-purple-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" /></svg>
                                    </span>
                                {/if}
                                <div class="flex items-center justify-between">
                                    <a use:inertia href="/dashboard/tasks/{task.slug}">
                                        <h2 class="text-2xl font-bold pb-3">{task.title}</h2>
                                    </a>
                                    <span class="whitespace-nowrap">Status: <span class="text-[#3bba2d] bg-[#d4facf] p-2 rounded">{task.status}</span></span>
                                </div>
                                <p class="mt-4">{@html task.description}</p>
                                <a use:inertia href="/dashboard" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                                <button type="button" use:inertia="{{ href: `/notification/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}">Like</button>
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
                            <a use:inertia href="{tasks.prev_page_url}" class="px-1">&laquo; Prev</a>
                        {/if}

                        {#if tasks.next_page_url}
                            <a use:inertia href="{tasks.next_page_url}" class="px-1">Next &raquo;</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
