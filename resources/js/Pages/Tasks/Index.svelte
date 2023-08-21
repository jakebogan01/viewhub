<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";

    export let tasks;
    export let filters;
    export let statuses;

    console.log(filters);
    console.log(tasks);
    console.log(statuses);

    let showStatusDropdown = false;
    let search = filters.search || '';
    let updateDropdownSelection = filters.status || 'Default';
    let timer;

    // debounce search input
    const debounce = v => {
        let data;
        if (v !== '') {
            data = {
                search: v,
            }
        }
        clearTimeout(timer);
        timer = setTimeout(() => {
            Inertia.get(`/${filters.status ? `?status=${filters.status}`: ''}`, data, {
                // search input doesn't reload the page and lose focus
                preserveState: true,
                // prevents the browser's history from being updated
                replace: true,
                // preserve scroll position on back navigation
                preserveScroll: true,
            });
        }, 300);
    }
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

<div on:keydown={()=>{}} class="flex space-x-8">
    <div on:keydown={()=>{}} class="basis-4/12">
        <div on:keydown={()=>{}}>
            <label id="listbox-label" class="block text-sm font-medium leading-6 text-gray-900">Assigned to</label>
            <div class="relative mt-2" on:keydown={()=>{}}>
                <button on:click={()=>{ showStatusDropdown = !showStatusDropdown }} type="button" class="relative w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">
                    <span class="block truncate">{updateDropdownSelection}</span>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd"/></svg>
                    </span>
                </button>

                {#if showStatusDropdown}
                    <ul class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-option-3">
                        <li class="text-gray-900 relative cursor-default select-none py-2 pl-3 pr-9" id="listbox-option-0" role="option">
                            <button type="button" use:inertia="{{ href: `/${filters.search ? `?search=${filters.search}` : ''}`, method: 'get', replace: true, }}" class="font-normal block truncate" on:click={(event)=>{ updateDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>Default</button>
                        </li>
                        {#each statuses as status, x (status.id)}
                            <li class="text-gray-900 relative cursor-default select-none py-2 pl-3 pr-9" id="listbox-option-{x + 1}" role="option">
                                <button type="button" use:inertia="{{ href: `/${filters.search ? `?search=${filters.search}` : ''}`, method: 'get', data: { status: status.name}, replace: true, }}" class="font-normal block truncate" on:click={(event)=>{ updateDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>{status.name}</button>
                            </li>
                        {/each}
                    </ul>
                {/if}
            </div>
        </div>
    </div>

    <div class="basis-full">
        <div class="flex justify-between mb-6">
            <h1 class="text-3xl">All Tasks</h1>

            <input on:keyup={({ target: { value } }) => debounce(value)} value={search} type="text"
                   placeholder="Search by Title or Tag" class="border px-2 rounded-lg"/>
        </div>

        <div class="space-y-4 mt-5">
            {#if tasks.data.length < 1}
                <p>There currently are no tasks</p>
            {:else}
                {#each tasks.data as task (task.id)}
                    <div class="border border-gray-200 rounded p-4 my-2">
                        <div class="flex items-center justify-between">
                            <a use:inertia={{ replace: true }} href="/tasks/{task.slug}"><h2
                                class="text-2xl font-bold pb-3">{task.title}</h2>
                            </a>
                            <span class="whitespace-nowrap">Status: <span
                                class="text-[#3bba2d] bg-[#d4facf] p-2 rounded">{task.status}</span></span>
                        </div>
                        <p class="mt-4">{task.description}</p>
                        <a use:inertia={{ replace: true }} href="/"
                           class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                    </div>
                {:else}
                    <p>There currently are no tasks</p>
                {/each}
            {/if}
        </div>

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
