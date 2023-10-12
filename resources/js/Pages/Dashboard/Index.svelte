<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {inertia, page} from "@inertiajs/inertia-svelte";
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
    export let statuses;
    export let user;

    console.log('Filters: ', filters);
    console.log('Count: ', count);
    console.log('Tasks: ', tasks);
    console.log('Tags: ', tags);
    console.log('Statuses: ', statuses);
    console.log('User: ', user);

    let route = '/dashboard';
    let search = filters.search || '';
    let timer;
    let enableDarkMode = auth.user.dark_mode;
    let showSearch;
    let searchInput;
    let activeLikeFilter, activeCommentFilter, activeSortFilter = false;
    let showNotifications = false;
    const randomColor = ['#D53E4F', '#F46D43', '#FDAE61', '#FEE08B', '#E6F598', '#ABDDA4', '#66C2A6', '#3388BD', '#5E4FA2', '#31207d'];

    // debounce search input
    const debounce = v => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (v !== '') {
                Inertia.get(route, {search: v, status: filters.status, tag: filters.tag, sortby: filters.sortby, liked: filters.liked}, {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                });
            } else {
                Inertia.get(route, {status: filters.status, tag: filters.tag, sortby: filters.sortby, liked: filters.liked}, {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            });
            }
        }, 300);
    }

    window.addEventListener('keydown', (e) => {
        if (e.keyCode === 83 && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            showSearch = true;
            searchInput.focus();
            debounce('');
        }
        if (e.keyCode === 27) {
            showNotifications = false;
        }
    });

    const submitSearch = (e) => {
        if (e.keyCode === 13 || e.keyCode === 27) {
            showSearch = false;
            search = '';
        }
    }
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

{#if showNotifications}
    <div class="fixed right-2.5 bottom-2.5 w-[350px] mt-4 space-y-2 z-50" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        {#each auth.user.unreadNotifications.reverse() as notification}
            <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            {#if notification.type === 'App\\Notifications\\CommentReceived'}
                                <p class="text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span> commented on your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.comment_id}" class="text-blue-500 font-bold">task</a></p>
                            {:else if notification.type === 'App\\Notifications\\CommentReplyReceived'}
                                <p class="text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span> replied to your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.reply_id}" class="text-blue-500 font-bold">comment</a></p>
                            {:else}
                                <p class="text-sm text-gray-500">Your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}" class="text-blue-500 font-bold">task</a> was liked by <span class="text-purple-500 font-bold">{notification.data.user}</span></p>
                            {/if}
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button type="button" use:inertia="{{ href: '/notification-mark-read', method: 'post', data: { id: notification.id }, replace: true, }}" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        {/each}
    </div>
{/if}

<div class="flex flex-col lg:flex-row sm:px-6 1440:px-0 gap-x-[30px]">
    <div class="relative lg:min-w-[255px] lg:max-w-[255px]">
        <div class="flex lg:flex-col lg:fixed lg:max-h-full lg:min-w-[255px] lg:max-w-[255px] space-x-4 lg:space-x-0 lg:space-y-6 sm:pb-10 lg:pb-16 lg:overflow-y-scroll">
            <!--start company banner-->
            <aside class="relative flex-1 flex items-center md:items-end justify-between hover:shadow-lg sm:rounded-[0.625rem] px-6 pb-6 md:px-8 pt-6 sm:pt-[61px]" style="background-image: linear-gradient(to bottom right, #000000, #414042);">
                <div class="mt-1 md:mt-0 whitespace-nowrap">
                    <h1 class="font-bold text-15 md:text-xl text-white leading-none"><span class="text-[#238AB6]">PNY</span> Technologies</h1>
                    <span class="font-medium text-13 md:text-15 text-white">Feedback <span class="text-[#238AB6]">Board</span></span>
                </div>
                <div on:keydown={()=>{}} on:click class="sm:hidden space-y-1">
                    <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
                    <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
                    <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
                </div>
            </aside>
            <!--end company banner-->

            <!--start tags-->
            <div class="hidden sm:block bg-white dark:bg-[#222222] px-3.5 py-6 hover:shadow-lg rounded-[0.625rem]">
                <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#444444] dark:hover:bg-[#238AB6] rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] dark:text-white cursor-pointer">All</button>

                    {#each tags as tag (tag.id)}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, tag: tag.name, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#444444] dark:hover:bg-[#238AB6] dark:hover:text-white rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] dark:text-white cursor-pointer">{tag.name}</button>
                    {/each}
                </div>
            </div>
            <!--end tags-->

            <!--start departments-->
            <div class="hidden lg:block relative bg-white dark:bg-[#222222] p-6 hover:shadow-lg rounded-[0.625rem] overflow-hidden h-full max-h-[250px] overflow-y-scroll">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-lg text-[#3A4374] dark:text-white">Task Board</h4>
                    <a href="/dashboard/all-tasks" class="font-semibold text-13 text-[#4661E6] hover:text-[#CED7FF] dark:text-white dark:hover:text-[#238AB6]">View</a>
                </div>
                <div>
                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="flex items-center justify-between w-full mt-4">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-[#9E0142] rounded-full mr-3"></span>
                            <span class="text-base hover:text-[#AD1FE9] dark:text-[#D1D7E9] dark:hover:text-white capitalize {$page.url === '/dashboard' ? 'text-[#AD1FE9]' : 'text-gray-500 dark:text-white'}">All</span>
                        </span>
                    </button>
                    {#each statuses as status, i (status.id)}
                        {#if status.count > 0}
                            <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: status.name, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="flex items-center justify-between w-full mt-2.5 group">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-3" style="background-color: {randomColor[i]}"></span>
                                    <span class="text-base group-hover:text-[#AD1FE9] dark:group-hover:text-white dark:text-[#D1D7E9] capitalize {$page.url === `/dashboard?status=${status.name}` ? 'text-[#AD1FE9]' : 'text-gray-500 darK:text-white'}">{status.name}</span>
                                </span>
                                <span class="font-bold text-base group-hover:text-[#AD1FE9] dark:group-hover:text-white dark:text-white {$page.url === `/dashboard?status=${status.name}` ? 'text-[#AD1FE9]' : 'text-[#21678B]'}">{status.count}</span>
                            </button>
                        {/if}
                    {/each}
                </div>
            </div>
            <!--end departments-->

            <!--start settings-->
            <div class="hidden sm:flex flex-col lg:flex-row justify-between items-center space-y-1 lg:space-y-0 lg:space-x-4">
                <button type="button" use:inertia={{ href: '/logout', method: 'post' }} class="flex justify-center w-full bg-white text-[#186489] dark:text-white hover:text-[#798ef2] dark:bg-[#222222] p-6 rounded-[0.625rem] hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M19 10a.75.75 0 00-.75-.75H8.704l1.048-.943a.75.75 0 10-1.004-1.114l-2.5 2.25a.75.75 0 000 1.114l2.5 2.25a.75.75 0 101.004-1.114l-1.048-.943h9.546A.75.75 0 0019 10z" clip-rule="evenodd" /></svg>
                </button>
                <button type="button" on:click={()=>{enableDarkMode = !enableDarkMode}} use:inertia="{{ href: '/update-dark-mode', method: 'post', data: { dark_mode: enableDarkMode }, replace: true, preserveScroll: true }}" class="hidden lg:flex justify-center w-full bg-white text-[#186489] dark:text-white hover:text-[#798ef2] dark:bg-[#222222] p-6 rounded-[0.625rem] hover:shadow-lg">
                    {#if enableDarkMode}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" /></svg>
                    {:else}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.967.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd" /></svg>
                    {/if}
                </button>
                <a use:inertia href="/dashboard/settings" class="flex justify-center w-full bg-white text-[#186489] dark:text-white hover:text-[#798ef2] dark:bg-[#222222] p-6 rounded-[0.625rem] hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M8.34 1.804A1 1 0 019.32 1h1.36a1 1 0 01.98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 011.262.125l.962.962a1 1 0 01.125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 01.804.98v1.361a1 1 0 01-.804.98l-1.473.295a6.95 6.95 0 01-.587 1.416l.834 1.25a1 1 0 01-.125 1.262l-.962.962a1 1 0 01-1.262.125l-1.25-.834a6.953 6.953 0 01-1.416.587l-.294 1.473a1 1 0 01-.98.804H9.32a1 1 0 01-.98-.804l-.295-1.473a6.957 6.957 0 01-1.416-.587l-1.25.834a1 1 0 01-1.262-.125l-.962-.962a1 1 0 01-.125-1.262l.834-1.25a6.957 6.957 0 01-.587-1.416l-1.473-.294A1 1 0 011 10.68V9.32a1 1 0 01.804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 01.125-1.262l.962-.962A1 1 0 015.38 3.03l1.25.834a6.957 6.957 0 011.416-.587l.294-1.473zM13 10a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd" /></svg>
                </a>
            </div>
            <!--end settings-->
        </div>
    </div>

    <div class="w-full space-y-6">
        <!--start nav-->
        <nav class="relative flex items-center justify-between hover:shadow-lg sm:rounded-[0.625rem] px-4 py-4 bg-[#000000] dark:bg-[#222222] text-13 text-[#F3F4FE]">
            <div class="hidden md:flex items-center">
                <svg width="23" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 2.274c2.237 0 4.339.854 5.923 2.408a8.123 8.123 0 012.465 5.839 8.084 8.084 0 01-1.7 4.979 8.457 8.457 0 01-3.652 2.71l-.31.112.003.826h.369c.262 0 .475.21.475.469a.47.47 0 01-.39.46l-.085.008h-.365l.004 1.02h.36c.263 0 .476.21.476.469a.47.47 0 01-.39.461l-.085.008h-.358l.006 1.487a.466.466 0 01-.381.46l-.094.01H9.23a.478.478 0 01-.466-.378l-.01-.092.006-1.487h-.357a.472.472 0 01-.475-.47.47.47 0 01.39-.46l.085-.008h.361l.004-1.02h-.365a.472.472 0 01-.475-.468.47.47 0 01.39-.462l.085-.007h.368l.004-.826a8.452 8.452 0 01-3.996-2.867 8.08 8.08 0 01-1.666-5.056c.032-2.127.923-4.152 2.511-5.7 1.508-1.471 3.448-2.322 5.493-2.416l.324-.009h.06zm1.791 19.769H9.709l-.004 1.02h3.59l-.004-1.02zm-.007-1.958H9.716l-.003 1.02h3.574l-.003-1.02zM11.5 3.212h-.054c-3.946.027-7.327 3.325-7.384 7.2-.048 3.266 2.14 6.192 5.322 7.118.174.05.3.193.332.364l.008.088-.004 1.166h3.56l-.004-1.166a.47.47 0 01.34-.452c3.134-.912 5.323-3.794 5.323-7.01a7.197 7.197 0 00-2.185-5.173A7.453 7.453 0 0011.5 3.212zm.829 1.782a.4.4 0 01.401.397v.322c.48.12.932.307 1.346.552l.228-.226a.405.405 0 01.569 0L16.046 7.2a.393.393 0 010 .56l-.23.228c.247.41.437.858.557 1.333h.323a.4.4 0 01.402.397v1.645a.4.4 0 01-.402.396h-.323c-.12.476-.31.924-.557 1.333l.23.228a.393.393 0 010 .56l-1.173 1.163a.405.405 0 01-.57 0l-.227-.227a5.02 5.02 0 01-1.346.553v.322a.4.4 0 01-.401.396H10.67a.4.4 0 01-.402-.396v-.322a5.022 5.022 0 01-1.345-.553l-.228.227a.405.405 0 01-.569 0L6.954 13.88a.393.393 0 010-.56l.23-.228a4.924 4.924 0 01-.557-1.333h-.324a.4.4 0 01-.401-.396V9.719a.4.4 0 01.401-.397h.324c.12-.475.31-.923.557-1.333l-.23-.228a.393.393 0 010-.56L8.127 6.04a.405.405 0 01.569 0l.228.226a5.021 5.021 0 011.345-.552V5.39a.4.4 0 01.402-.397zM11.5 7.721c-1.572 0-2.846 1.263-2.846 2.82 0 1.558 1.274 2.82 2.846 2.82s2.846-1.262 2.846-2.82c0-1.557-1.274-2.82-2.846-2.82zm11.025 4.152c.262 0 .475.21.475.469a.47.47 0 01-.39.461l-.085.008h-.498a.472.472 0 01-.475-.469.47.47 0 01.39-.461l.085-.008h.498zm-21.552 0c.262 0 .475.21.475.469a.47.47 0 01-.39.461l-.085.008H.475A.472.472 0 010 12.342a.47.47 0 01.39-.461l.085-.008h.498zM3.112 3.45l.074.06.46.451c.185.183.186.48 0 .663a.476.476 0 01-.596.062l-.075-.06-.459-.451a.465.465 0 01-.001-.663.48.48 0 01.597-.062zm17.373.062c.162.16.182.408.06.59l-.061.073-.46.45a.476.476 0 01-.67 0 .464.464 0 01-.06-.59l.06-.074.46-.45a.48.48 0 01.671 0zM11.5 0c.233 0 .427.166.467.384l.008.085v.49a.472.472 0 01-.475.468.473.473 0 01-.467-.384l-.008-.084v-.49c0-.26.213-.469.475-.469z" fill="#FFF" fill-rule="nonzero"/></svg>
                <span class="font-bold text-white text-lg ml-4">{count} Suggestions</span>
            </div>
            <div class="dark:bg-[#222222] relative pointer-events-auto">
                <button on:click={()=>{showSearch = !showSearch; debounce('');}} type="button" class="w-full flex items-center text-sm leading-6 text-slate-400 border border-[#212020] dark:border-[#222222] rounded-md ring-1 ring-slate-900/10 shadow-sm py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:highlight-white/5 dark:hover:bg-slate-700">
                    <svg width="24" height="24" fill="none" aria-hidden="true" class="mr-3 flex-none"><path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle></svg>
                    Quick search...<span class="ml-auto pl-3 flex-none text-xs font-semibold">CTR&#43;S</span>
                </button>
            </div>
            <div>
                <a use:inertia href="/dashboard/task/create" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
            </div>
        </nav>
        <!--end nav-->

        <main>
            <!--start filters-->
            <div class="flex justify-between text-[#186489] dark:text-white mb-2 px-6 sm:px-0">
                <div class="flex space-x-px">
                    {#if activeLikeFilter}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, status: filters.status }, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = false; activeCommentFilter = false; activeSortFilter = false;}} class="block py-2 px-3 hover:bg-bg-transparent dark:hover:bg-black rounded-tl-md rounded-bl-md {activeLikeFilter ? 'bg-transparent' : ''}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" /></svg></button>
                    {:else}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, status: filters.status, liked: true}, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = true; activeCommentFilter = false; activeSortFilter = false;}} class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black rounded-tl-md rounded-bl-md {activeLikeFilter ? '' : 'bg-white dark:bg-[#222222]'}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" /></svg></button>
                    {/if}

                    {#if activeCommentFilter}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, status: filters.status}, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = false; activeCommentFilter = false; activeSortFilter = false;}} class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black {activeCommentFilter ? 'bg-transparent' : ''}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg></button>
                    {:else}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, status: filters.status, commented: true}, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = false; activeCommentFilter = true; activeSortFilter = false;}} class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black {activeCommentFilter ? '' : 'bg-white dark:bg-[#222222]'}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg></button>
                    {/if}

                    {#if activeSortFilter}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, status: filters.status}, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = false; activeCommentFilter = false; activeSortFilter = false;}} class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black rounded-tr-md rounded-br-md {activeSortFilter ? 'bg-transparent' : ''}"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg></button>
                    {:else}
                        <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: 'oldest', status: filters.status}, replace: true, preserveScroll: true }}" on:click={()=>{activeLikeFilter = false; activeCommentFilter = false; activeSortFilter = true;}} class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black rounded-tr-md rounded-br-md {activeSortFilter ? '' : 'bg-white dark:bg-[#222222]'}"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" /></svg></button>
                    {/if}
                </div>

                <div class="flex space-x-px">
                    {#if auth.user.unreadNotifications.length > 0}
                        <button type="button" on:click={()=>{showNotifications = !showNotifications}} class="block py-2 px-3 hover:bg-transparent rounded-md bg-white" aria-label="notifications"><svg xmlns="http://www.w3.org/2000/svg" fill="{auth.user.unreadNotifications.length > 0 ? 'currentColor' : 'none'}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg></button>
                    {:else}
                        <button type="button" class="block py-2 px-3 hover:bg-transparent dark:hover:bg-black rounded-md bg-white dark:bg-[#222222]" aria-label="notifications"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg></button>
                    {/if}
                </div>
            </div>
            <!--end filters-->

            <!--start suggestions-->
            <div class="relative space-y-5 px-6 sm:px-0">
                {#if tasks.data.length < 1}
                    <div class="flex flex-col items-center justify-center bg-white dark:bg-[#222222] py-[4.75rem] md:py-[6.9375rem] px-[1.625rem] text-center rounded-[0.625rem] cursor-default">
                        {#if user.dark_mode}
                            <svg width="102" height="108" viewBox="0 0 102 108" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Group 17"><g id="Group 16" opacity="0.5"><path id="Path" d="M48.7304 15.5928C25.8219 15.5884 7.24564 34.2238 7.23536 57.2201C7.22507 80.2164 25.7846 98.8686 48.6932 98.8849C71.6017 98.9011 90.1875 80.2752 90.2095 57.2789C90.2222 46.2275 85.8577 35.6243 78.0775 27.8053C70.2973 19.9862 59.7397 15.5928 48.7304 15.5928Z" stroke="#8E949D" stroke-width="1.04545" stroke-linecap="round" stroke-linejoin="round"/><ellipse id="Oval" cx="70.4762" cy="43.691" rx="1.91663" ry="2.86191" fill="#231F20"/><path id="Path_2" d="M0 44.9016L97.4288 23.2569L78.2625 17.6453C78.2625 17.6453 67.8807 0.3696 65.1096 0.00885439C62.3384 -0.351891 11.9789 10.4304 11.9789 10.4304L8.78456 34.7687L0 44.9016Z" fill="#8E949D"/><path id="Path_3" d="M20.2841 103.503L9.27148 86.0268L57.4109 77.7617L60.7171 94.5083L65.6045 79.0845L90.2332 83.9665L85.226 103.503H20.2841Z" fill="#8E949D"/><path id="Path_4" d="M81.9699 65.6066L86.4077 64.99L88.7003 81.6182C88.86 82.776 88.0544 83.8445 86.901 84.0047L86.64 84.041C85.4866 84.2012 84.4222 83.3926 84.2625 82.2348L81.9699 65.6066Z" fill="#8E949D"/><path id="Shape" fill-rule="evenodd" clip-rule="evenodd" d="M83.8686 82.2987C84.0433 83.5419 85.1014 84.4675 86.3522 84.4711C86.4618 84.4704 86.5711 84.4623 86.6796 84.4471L86.9352 84.407C87.5957 84.3196 88.1939 83.9702 88.596 83.437C88.9982 82.9038 89.1708 82.2311 89.0754 81.5691L86.7994 64.9428C86.7883 64.8371 86.7332 64.741 86.6477 64.6782C86.5637 64.6132 86.4574 64.5843 86.3522 64.5981L81.92 65.2154C81.7019 65.2444 81.5483 65.4452 81.5766 65.6643L83.8686 82.2987ZM84.6592 82.1864L82.4231 65.9529L86.0647 65.4879L88.3008 81.7215C88.4288 82.6573 87.7791 83.521 86.8473 83.6535H86.5838C85.65 83.7771 84.7913 83.1226 84.6592 82.1864Z" fill="#3A4374"/><path id="Rectangle" d="M78.5617 50.9303L85.7207 49.9356L87.963 66.1985L80.804 67.1932L78.5617 50.9303Z" fill="#8E949D"/><path id="Shape_2" fill-rule="evenodd" clip-rule="evenodd" d="M80.5624 67.5246C80.6325 67.5747 80.716 67.6026 80.802 67.6048L80.8499 67.5968L88.0373 66.6027C88.2554 66.5736 88.409 66.3728 88.3807 66.1538L86.1366 49.8882C86.1248 49.7821 86.0704 49.6853 85.9861 49.6203C85.9017 49.5553 85.7946 49.5275 85.6894 49.5435L78.5021 50.5375C78.3985 50.5538 78.3045 50.608 78.2385 50.6898C78.1757 50.7749 78.1498 50.8819 78.1666 50.9864L80.4107 67.2601C80.4198 67.3664 80.4754 67.4633 80.5624 67.5246ZM81.1454 66.747L79.0132 51.275L85.4019 50.3932L87.5342 65.8652L81.1454 66.747Z" fill="#3A4374"/><path id="Rectangle_2" d="M82.8021 81.6749C82.5573 79.8992 83.7972 78.2608 85.5727 78.0142L86.2975 77.9135C88.0756 77.6664 89.7169 78.9086 89.9621 80.6869L92.9747 102.537C93.2196 104.313 91.9796 105.951 90.2041 106.198L89.4793 106.299C87.7012 106.546 86.0599 105.303 85.8148 103.525L82.8021 81.6749Z" fill="#8E949D"/><path id="Shape_3" fill-rule="evenodd" clip-rule="evenodd" d="M85.5059 104.272C85.7089 105.737 86.9556 106.827 88.4287 106.829L88.828 106.837L90.9363 106.541C92.5459 106.313 93.6699 104.823 93.4519 103.206L90.2575 79.9577C90.0347 78.3395 88.5474 77.209 86.9354 77.4325L84.8271 77.7211C84.0525 77.8308 83.3523 78.2427 82.8785 78.8674C82.4053 79.4944 82.2012 80.2852 82.3115 81.064L85.5059 104.272ZM87.0472 78.2021C87.1455 78.1941 87.2443 78.1941 87.3426 78.2021L87.3187 78.2101C88.3909 78.2159 89.296 79.0117 89.443 80.0779L92.6373 103.326C92.7795 104.496 91.963 105.565 90.8006 105.731L88.6923 106.028C87.528 106.166 86.4657 105.348 86.2965 104.184L83.1021 80.9357C83.0168 80.3668 83.1668 79.7876 83.5174 79.3324C83.8594 78.8716 84.3717 78.5683 84.9389 78.4906L87.0472 78.2021Z" fill="#3A4374"/><ellipse id="Oval_2" cx="81.5687" cy="46.2882" rx="19.1903" ry="19.2638" fill="#8E949D"/><path id="Shape_4" fill-rule="evenodd" clip-rule="evenodd" d="M61.9845 47.1747C62.4607 57.6774 71.0794 65.9481 81.5529 65.9529C82.4664 65.9525 83.3788 65.8909 84.2841 65.7685C94.9829 64.2491 102.445 54.3326 100.975 43.5867C99.5407 33.172 90.2025 25.7299 79.7727 26.6897C69.3429 27.6495 61.5083 36.6719 61.9845 47.1747ZM78.9574 27.5936C79.8176 27.4753 80.6847 27.4164 81.5529 27.4172H81.5848C90.9448 27.458 98.8543 34.3937 100.152 43.6989C101.195 51.2598 97.5977 58.7068 91.0378 62.5669C84.478 66.4271 76.2475 65.9402 70.1847 61.3333C64.122 56.7263 61.4209 48.9067 63.3413 41.5211C65.2616 34.1355 71.4251 28.6385 78.9574 27.5936Z" fill="#3A4374"/><ellipse id="Oval_3" cx="81.5686" cy="46.289" rx="15.5886" ry="15.6483" fill="#8E949D"/><path id="Shape_5" fill-rule="evenodd" clip-rule="evenodd" d="M65.598 46.8572C65.8949 55.4918 72.954 62.3375 81.5607 62.3374C82.2954 62.3382 83.0292 62.2873 83.7568 62.185C92.2818 60.997 98.3359 53.2418 97.447 44.6483C96.5581 36.0548 89.0458 29.7137 80.4596 30.3092C71.8733 30.9047 65.3012 38.2227 65.598 46.8572ZM79.4444 31.2171C80.1458 31.1214 80.8528 31.0732 81.5607 31.0728C89.7341 31.0903 96.4251 37.6037 96.6934 45.804C96.9618 54.0044 90.7113 60.9446 82.5566 61.5007C74.4019 62.0567 67.2738 56.0287 66.4351 47.8672C65.5963 39.7057 71.3484 32.3439 79.4444 31.2171Z" fill="#3A4374"/><path id="Path_5" d="M99.8964 89.7141C98.8417 88.3705 97.5059 87.2758 95.9833 86.5075C91.4074 84.1025 86.1606 84.1827 81.345 85.7058C79.636 86.251 76.3219 87.0286 76.1462 89.3053C76.1592 90.2377 76.7787 91.0517 77.6715 91.3094C78.5454 91.5369 79.4579 91.5724 80.3468 91.4136C79.0802 91.3795 77.8287 91.6956 76.7292 92.3275C75.699 93.057 75.132 94.6523 75.9785 95.6223C76.3526 96.0256 76.84 96.3052 77.376 96.424C78.5413 96.7457 79.7736 96.729 80.9298 96.3759C79.6787 96.6165 78.4594 97.0011 77.2962 97.5223C76.5934 97.8349 75.8108 98.388 75.8906 99.1256C75.9705 99.8631 76.6892 100.2 77.3441 100.424C78.7218 100.899 80.1611 101.169 81.6166 101.226C80.3603 101.455 79.1724 101.967 78.1427 102.725C76.5455 104.072 76.5455 106.629 78.7097 107.359C79.4039 107.562 80.1276 107.646 80.8499 107.607C87.2387 107.607 94.8892 106.805 99.2176 101.466C101.348 98.968 102.113 95.5754 101.262 92.3997C100.966 91.4314 100.504 90.5226 99.8964 89.7141Z" fill="#8E949D"/><path id="Shape_6" fill-rule="evenodd" clip-rule="evenodd" d="M78.5819 107.72C79.3021 107.931 80.0525 108.021 80.802 107.984L80.826 108C87.1509 107.992 94.9931 107.271 99.5211 101.715C101.742 99.1142 102.535 95.5771 101.637 92.2717C101.319 91.2654 100.83 90.3217 100.192 89.482C99.1056 88.0783 97.7228 86.9339 96.1431 86.1311C91.8466 83.9345 86.536 83.6379 81.2013 85.3294L80.6902 85.4817C80.6725 85.4872 80.6546 85.4927 80.6367 85.4982C78.7359 86.0832 75.9049 86.9546 75.723 89.2736C75.714 90.2817 76.3184 91.1934 77.2483 91.5743C76.9662 91.6869 76.6982 91.8322 76.4497 92.0072C75.7473 92.5152 75.2805 93.2876 75.156 94.1476C75.0623 94.7657 75.2334 95.3948 75.6271 95.8792C76.0516 96.3435 76.61 96.6631 77.2243 96.7931C77.3662 96.8354 77.5101 96.8702 77.6556 96.8973L77.0886 97.1378C75.9865 97.6589 75.3716 98.4926 75.4914 99.182C75.6112 99.8715 76.1462 100.417 77.2084 100.785C77.9885 101.058 78.7899 101.264 79.6041 101.403C78.9772 101.637 78.394 101.976 77.8792 102.405C76.9498 103.191 76.4958 104.406 76.6813 105.611C76.8789 106.612 77.6094 107.423 78.5819 107.72ZM77.4559 97.8352C78.2906 97.4585 79.1563 97.1554 80.0434 96.9294C80.3784 96.8901 80.7096 96.8231 81.0336 96.729C81.2348 96.6637 81.3527 96.4547 81.3052 96.248C81.2538 96.0412 81.0504 95.9109 80.842 95.9514C80.5225 96.0155 80.1951 96.0876 79.8757 96.1678C79.0744 96.2733 78.2602 96.2188 77.4799 96.0075C77.024 95.9137 76.6067 95.6847 76.282 95.3501C76.0354 95.0352 75.9312 94.6312 75.9945 94.2358C76.0874 93.5922 76.436 93.0138 76.9608 92.6325C77.6552 92.1915 78.4487 91.9324 79.2687 91.8789C79.4537 91.8751 79.6368 91.8544 79.8171 91.8341C80.0105 91.8122 80.2008 91.7908 80.3868 91.7908C80.6023 91.7656 80.7604 91.5752 80.7461 91.3579C80.7214 91.1435 80.5378 90.9835 80.3229 90.9891C80.2998 90.9907 80.2766 90.9923 80.2533 90.9938C79.9231 91.0164 79.5795 91.0398 79.2288 91.0773C78.7455 91.1087 78.2606 91.0489 77.7993 90.9009C77.0774 90.6969 76.5682 90.0498 76.5375 89.2976C76.6811 87.5036 79.2479 86.7258 80.941 86.2127L80.9458 86.2112L81.4649 86.0509C86.5999 84.4476 91.6869 84.7282 95.7997 86.8525C97.2752 87.5991 98.5658 88.6673 99.577 89.979C100.161 90.7435 100.609 91.6032 100.903 92.5203C101.717 95.5699 100.976 98.8266 98.9222 101.218C94.6098 106.517 86.9992 107.207 80.842 107.207C80.1659 107.251 79.4873 107.175 78.8375 106.982C78.1361 106.78 77.606 106.201 77.4639 105.483C77.3222 104.557 77.6773 103.625 78.3983 103.03C79.3886 102.318 80.5228 101.834 81.7204 101.611C81.9124 101.561 82.0383 101.376 82.0159 101.178C81.9957 100.98 81.831 100.828 81.6326 100.825C80.214 100.765 78.8121 100.495 77.4719 100.024C76.9608 99.8554 76.3539 99.647 76.29 99.0538C76.2261 98.4605 77.0806 98.0036 77.4559 97.8352Z" fill="#3A4374"/><path id="Path_6" d="M55.3669 46.5928C55.3669 46.5928 65.0938 61.2631 59.2081 61.4715C53.3225 61.6799 52.8193 60.1328 52.8193 60.1328" fill="#8E949D"/><path id="Path_7" d="M58.1538 61.9372C53.2185 61.9372 52.5077 60.5824 52.4119 60.3339C52.3429 60.115 52.4596 59.8807 52.6754 59.8048C52.8914 59.7361 53.1223 59.8538 53.1945 60.0694C53.1945 60.0694 53.9133 61.3119 59.192 61.1195C59.7173 61.1681 60.2226 60.9051 60.4857 60.4461C61.7475 57.985 56.9719 49.8242 55.0233 46.8901C54.9149 46.7006 54.9707 46.4591 55.1512 46.3369C55.3318 46.2147 55.5757 46.2532 55.7101 46.4252C56.4528 47.5475 62.8975 57.488 61.2204 60.855C60.8289 61.5879 60.0513 62.0282 59.2239 61.9853L58.1538 61.9372Z" fill="#3A4374"/><ellipse id="Oval_4" cx="82.4551" cy="45.7992" rx="3.5298" ry="6.03647" fill="#C0C5DC"/><ellipse id="Oval_5" cx="39.2591" cy="45.7991" rx="2.69127" ry="4.88209" fill="#8E949D"/></g></g></svg>
                        {:else}
                            <svg width="102" height="108" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none" opacity=".5"><path d="M48.73 15.593C25.822 15.59 7.246 34.224 7.235 57.22c-.01 22.997 18.55 41.649 41.458 41.665 22.909.016 41.494-18.61 41.516-41.606a41.72 41.72 0 00-12.132-29.473A41.4 41.4 0 0048.73 15.593z" stroke="#3A4374" stroke-width="1.045" stroke-linecap="round" stroke-linejoin="round"/><ellipse fill="#231F20" cx="70.476" cy="43.691" rx="1.917" ry="2.862"/><path d="M0 44.902l97.429-21.645-19.167-5.612S67.881.37 65.11.01C62.338-.351 11.979 10.43 11.979 10.43L8.785 34.77 0 44.902zM20.284 103.503L9.272 86.027l48.14-8.265 3.305 16.746 4.888-15.424 24.628 4.882-5.007 19.537z" fill="#3A4374"/><path d="M81.97 65.607l4.438-.617L88.7 81.618a2.115 2.115 0 01-1.799 2.387l-.261.036a2.11 2.11 0 01-2.377-1.806L81.97 65.607z" fill="#FFF"/><path d="M86.352 84.471A2.519 2.519 0 0183.87 82.3l-2.292-16.634a.4.4 0 01.343-.45l4.432-.617a.398.398 0 01.296.08c.085.063.14.16.151.265l2.276 16.626a2.508 2.508 0 01-.479 1.868 2.49 2.49 0 01-1.66.97l-.256.04a2.474 2.474 0 01-.328.024zm-3.929-18.518l2.236 16.234c.132.936.991 1.59 1.925 1.467h.263a1.714 1.714 0 001.454-1.932l-2.236-16.234-3.642.465z" fill="#3A4374"/><path fill="#FFF" d="M78.562 50.93l7.159-.995 2.242 16.263-7.16.995z"/><path d="M80.802 67.605a.43.43 0 01-.24-.08.361.361 0 01-.151-.265l-2.244-16.274a.394.394 0 01.072-.296.423.423 0 01.263-.153l7.187-.994a.39.39 0 01.448.345l2.244 16.266a.4.4 0 01-.344.448l-7.187.995-.048.008zm-1.789-16.33l2.132 15.472 6.39-.882-2.133-15.472-6.389.882z" fill="#3A4374"/><path d="M85.573 78.014l.725-.1a3.248 3.248 0 013.664 2.773l3.013 21.85a3.248 3.248 0 01-2.77 3.661l-.726.1a3.248 3.248 0 01-3.664-2.773l-3.013-21.85a3.248 3.248 0 012.77-3.66z" fill="#FFF"/><path d="M88.429 106.83a2.959 2.959 0 01-2.923-2.558L82.31 81.064a2.959 2.959 0 012.516-3.343l2.108-.288a2.949 2.949 0 013.322 2.525l3.195 23.248a2.964 2.964 0 01-2.516 3.335l-2.108.297-.4-.008zm-1.086-28.628a1.838 1.838 0 00-.296 0l-2.108.289a2.13 2.13 0 00-1.422.842c-.35.455-.5 1.034-.415 1.603l3.194 23.248a2.166 2.166 0 002.396 1.844l2.108-.297a2.163 2.163 0 001.837-2.405l-3.194-23.248a2.159 2.159 0 00-2.124-1.868l.024-.008z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="19.19" ry="19.264"/><path d="M81.553 65.953c-10.474-.005-19.093-8.275-19.569-18.778-.476-10.503 7.359-19.525 17.789-20.485 10.43-.96 19.768 6.482 21.202 16.897 1.47 10.746-5.992 20.662-16.691 22.182-.905.122-1.818.184-2.731.184zm0-38.536c-.868 0-1.736.059-2.596.177-7.532 1.045-13.696 6.542-15.616 13.927-1.92 7.386.78 15.206 6.844 19.812a18.74 18.74 0 0020.853 1.234c6.56-3.86 10.157-11.307 9.114-18.868-1.298-9.305-9.207-16.24-18.567-16.282h-.032z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="15.589" ry="15.648"/><path d="M81.56 62.338c-8.606 0-15.665-6.846-15.962-15.48-.297-8.635 6.275-15.953 14.862-16.548 8.586-.596 16.098 5.745 16.987 14.339.889 8.593-5.165 16.348-13.69 17.536-.728.103-1.462.154-2.196.153zm0-31.265c-.707 0-1.414.049-2.116.144-8.096 1.127-13.848 8.489-13.009 16.65.839 8.162 7.967 14.19 16.122 13.634 8.154-.556 14.405-7.496 14.136-15.697-.268-8.2-6.959-14.713-15.132-14.731z" fill="#3A4374"/><path d="M99.896 89.714a11.645 11.645 0 00-3.913-3.206c-4.576-2.405-9.822-2.325-14.638-.802-1.709.545-5.023 1.323-5.199 3.6a2.115 2.115 0 001.526 2.004 6.254 6.254 0 002.675.104 6.887 6.887 0 00-3.618.914c-1.03.73-1.597 2.324-.75 3.294.374.404.861.683 1.397.802a6.365 6.365 0 003.554-.048c-1.251.24-2.47.625-3.634 1.146-.703.313-1.485.866-1.405 1.604.08.737.798 1.074 1.453 1.298 1.378.475 2.817.745 4.273.802a8.4 8.4 0 00-3.474 1.5c-1.598 1.346-1.598 3.903.567 4.633a6.366 6.366 0 002.14.248c6.389 0 14.04-.801 18.368-6.14a9.993 9.993 0 002.044-9.067 8.702 8.702 0 00-1.366-2.686z" fill="#FFF"/><path d="M80.802 107.984c-.75.037-1.5-.053-2.22-.265a2.762 2.762 0 01-1.9-2.108 3.502 3.502 0 011.197-3.207 5.952 5.952 0 011.725-1.002 14.816 14.816 0 01-2.396-.617c-1.062-.369-1.597-.914-1.717-1.603-.12-.69.495-1.523 1.598-2.044l.567-.24a4.211 4.211 0 01-.432-.105 3.008 3.008 0 01-1.597-.914 2.219 2.219 0 01-.471-1.732 3.209 3.209 0 011.294-2.14c.248-.175.516-.32.798-.433a2.461 2.461 0 01-1.525-2.3c.184-2.342 3.067-3.207 4.967-3.793l.511-.152c5.335-1.691 10.646-1.395 14.942.802a11.99 11.99 0 014.049 3.35 9.207 9.207 0 011.445 2.79 10.363 10.363 0 01-2.116 9.444c-4.528 5.555-12.37 6.277-18.695 6.285l-.024-.016zm-.759-11.055c-.887.226-1.752.53-2.587.906-.375.168-1.23.625-1.166 1.219.064.593.67.801 1.182.97 1.34.47 2.742.74 4.16.801a.392.392 0 01.384.353.4.4 0 01-.296.433 8.289 8.289 0 00-3.322 1.419 2.659 2.659 0 00-.934 2.453 1.953 1.953 0 001.373 1.499 5.73 5.73 0 002.005.224c6.157 0 13.768-.69 18.08-5.988a9.567 9.567 0 001.98-8.698 8.413 8.413 0 00-1.325-2.541 11.094 11.094 0 00-3.777-3.127c-4.113-2.124-9.2-2.405-14.335-.801l-.52.16c-1.692.513-4.264 1.29-4.408 3.086.031.753.54 1.4 1.262 1.604a3.86 3.86 0 001.43.176c.375-.04.742-.064 1.094-.088a.416.416 0 01.423.369.408.408 0 01-.36.433c-.359 0-.734.08-1.117.088a4.898 4.898 0 00-2.308.753c-.525.382-.874.96-.967 1.604-.063.395.041.799.288 1.114.325.334.742.563 1.198.657a6.11 6.11 0 002.396.16c.32-.08.646-.152.966-.216a.4.4 0 01.463.297.41.41 0 01-.271.48 6.097 6.097 0 01-.99.201z" fill="#3A4374"/><path d="M55.367 46.593s9.727 14.67 3.84 14.879c-5.885.208-6.388-1.339-6.388-1.339" fill="#FFF"/><path d="M58.154 61.937c-4.936 0-5.646-1.355-5.742-1.603a.425.425 0 01.263-.53.415.415 0 01.52.265s.718 1.243 5.997 1.05a1.348 1.348 0 001.294-.673c1.261-2.461-3.514-10.622-5.463-13.556a.418.418 0 01.128-.553.414.414 0 01.56.088c.742 1.122 7.186 11.063 5.51 14.43a2.139 2.139 0 01-1.997 1.13l-1.07-.048z" fill="#3A4374"/><ellipse fill="#C0C5DC" cx="82.455" cy="45.799" rx="3.53" ry="6.036"/><ellipse fill="#3A4374" cx="39.259" cy="45.799" rx="2.691" ry="4.882"/></g></svg>
                        {/if}
                        <div class="md:max-w-[25.625rem]">
                            <h2 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-[#248AB6] leading-none mt-10">There is no feedback yet.</h2>
                            <p class="text-13 md:text-base text-[#647196] dark:text-[#D1D7E9] my-6 md:mt-4 md:mb-10">Got a suggestion? Found a bug that needs to be squashed? We love hearing about new ideas to improve our app.</p>
                        </div>
                        <div>
                            <a use:inertia href="/dashboard/task/create" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
                        </div>
                    </div>
                {:else}
                    {#each tasks.data as task (task.id)}
                        <div class="flex flex-col md:flex-row bg-white dark:bg-[#222222] hover:shadow-lg p-6 md:py-7 md:px-8 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
                            <div class="hidden md:block pr-10">
                                <button type="button" use:inertia="{{ href: `/notification/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}" class="flex flex-row md:flex-col items-center md:justify-center space-x-2 md:space-x-0 bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#444444] dark:hover:bg-[#238AB6] text-[#3A4374] dark:text-white rounded-[0.625rem] px-4 py-1 md:p-0 md:w-10 md:h-[3.3125rem] font-bold">
                                    {#if user.dark_mode}
                                        <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                                    {:else}
                                        <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                                    {/if}
                                    <span class="mt-1">{task.likes}</span>
                                </button>
                            </div>
                            <div class="flex-1 md:pr-[1.5625rem]">
                                <a use:inertia href="/dashboard/tasks/{task.slug}">
                                    <h2 class="font-bold text-[#3A4374] group-hover:text-[#238AB6] dark:text-white md:text-lg leading-3">{task.title}</h2>
                                </a>
                                    <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>
                                    <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#444444] dark:text-white rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] text-base cursor-default">{task.tag}</span>
                            </div>
                            <div class="flex items-end md:items-center justify-between">
                                <a use:inertia href="/dashboard/tasks/{task.slug}" class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                    <svg width="18" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.62 16H1.346l.902-.91c.486-.491.79-1.13.872-1.823C1.036 11.887 0 9.89 0 7.794 0 3.928 3.52 0 9.03 0 14.87 0 18 3.615 18 7.455c0 3.866-3.164 7.478-8.97 7.478-1.017 0-2.078-.137-3.025-.388A4.705 4.705 0 012.62 16z" fill="#CDD2EE" fill-rule="nonzero"/></svg>
                                    <span class="dark:text-[#D1D7E9]">{task.comment_count}</span>
                                </a>
                            </div>
                        </div>
                    {:else}
                        <div class="flex flex-col items-center justify-center bg-white dark:bg-[#222222] py-[4.75rem] md:py-[6.9375rem] px-[1.625rem] text-center rounded-[0.625rem] cursor-default">
                            {#if user.dark_mode}
                                <svg width="102" height="108" viewBox="0 0 102 108" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Group 17"><g id="Group 16" opacity="0.5"><path id="Path" d="M48.7304 15.5928C25.8219 15.5884 7.24564 34.2238 7.23536 57.2201C7.22507 80.2164 25.7846 98.8686 48.6932 98.8849C71.6017 98.9011 90.1875 80.2752 90.2095 57.2789C90.2222 46.2275 85.8577 35.6243 78.0775 27.8053C70.2973 19.9862 59.7397 15.5928 48.7304 15.5928Z" stroke="#8E949D" stroke-width="1.04545" stroke-linecap="round" stroke-linejoin="round"/><ellipse id="Oval" cx="70.4762" cy="43.691" rx="1.91663" ry="2.86191" fill="#231F20"/><path id="Path_2" d="M0 44.9016L97.4288 23.2569L78.2625 17.6453C78.2625 17.6453 67.8807 0.3696 65.1096 0.00885439C62.3384 -0.351891 11.9789 10.4304 11.9789 10.4304L8.78456 34.7687L0 44.9016Z" fill="#8E949D"/><path id="Path_3" d="M20.2841 103.503L9.27148 86.0268L57.4109 77.7617L60.7171 94.5083L65.6045 79.0845L90.2332 83.9665L85.226 103.503H20.2841Z" fill="#8E949D"/><path id="Path_4" d="M81.9699 65.6066L86.4077 64.99L88.7003 81.6182C88.86 82.776 88.0544 83.8445 86.901 84.0047L86.64 84.041C85.4866 84.2012 84.4222 83.3926 84.2625 82.2348L81.9699 65.6066Z" fill="#8E949D"/><path id="Shape" fill-rule="evenodd" clip-rule="evenodd" d="M83.8686 82.2987C84.0433 83.5419 85.1014 84.4675 86.3522 84.4711C86.4618 84.4704 86.5711 84.4623 86.6796 84.4471L86.9352 84.407C87.5957 84.3196 88.1939 83.9702 88.596 83.437C88.9982 82.9038 89.1708 82.2311 89.0754 81.5691L86.7994 64.9428C86.7883 64.8371 86.7332 64.741 86.6477 64.6782C86.5637 64.6132 86.4574 64.5843 86.3522 64.5981L81.92 65.2154C81.7019 65.2444 81.5483 65.4452 81.5766 65.6643L83.8686 82.2987ZM84.6592 82.1864L82.4231 65.9529L86.0647 65.4879L88.3008 81.7215C88.4288 82.6573 87.7791 83.521 86.8473 83.6535H86.5838C85.65 83.7771 84.7913 83.1226 84.6592 82.1864Z" fill="#3A4374"/><path id="Rectangle" d="M78.5617 50.9303L85.7207 49.9356L87.963 66.1985L80.804 67.1932L78.5617 50.9303Z" fill="#8E949D"/><path id="Shape_2" fill-rule="evenodd" clip-rule="evenodd" d="M80.5624 67.5246C80.6325 67.5747 80.716 67.6026 80.802 67.6048L80.8499 67.5968L88.0373 66.6027C88.2554 66.5736 88.409 66.3728 88.3807 66.1538L86.1366 49.8882C86.1248 49.7821 86.0704 49.6853 85.9861 49.6203C85.9017 49.5553 85.7946 49.5275 85.6894 49.5435L78.5021 50.5375C78.3985 50.5538 78.3045 50.608 78.2385 50.6898C78.1757 50.7749 78.1498 50.8819 78.1666 50.9864L80.4107 67.2601C80.4198 67.3664 80.4754 67.4633 80.5624 67.5246ZM81.1454 66.747L79.0132 51.275L85.4019 50.3932L87.5342 65.8652L81.1454 66.747Z" fill="#3A4374"/><path id="Rectangle_2" d="M82.8021 81.6749C82.5573 79.8992 83.7972 78.2608 85.5727 78.0142L86.2975 77.9135C88.0756 77.6664 89.7169 78.9086 89.9621 80.6869L92.9747 102.537C93.2196 104.313 91.9796 105.951 90.2041 106.198L89.4793 106.299C87.7012 106.546 86.0599 105.303 85.8148 103.525L82.8021 81.6749Z" fill="#8E949D"/><path id="Shape_3" fill-rule="evenodd" clip-rule="evenodd" d="M85.5059 104.272C85.7089 105.737 86.9556 106.827 88.4287 106.829L88.828 106.837L90.9363 106.541C92.5459 106.313 93.6699 104.823 93.4519 103.206L90.2575 79.9577C90.0347 78.3395 88.5474 77.209 86.9354 77.4325L84.8271 77.7211C84.0525 77.8308 83.3523 78.2427 82.8785 78.8674C82.4053 79.4944 82.2012 80.2852 82.3115 81.064L85.5059 104.272ZM87.0472 78.2021C87.1455 78.1941 87.2443 78.1941 87.3426 78.2021L87.3187 78.2101C88.3909 78.2159 89.296 79.0117 89.443 80.0779L92.6373 103.326C92.7795 104.496 91.963 105.565 90.8006 105.731L88.6923 106.028C87.528 106.166 86.4657 105.348 86.2965 104.184L83.1021 80.9357C83.0168 80.3668 83.1668 79.7876 83.5174 79.3324C83.8594 78.8716 84.3717 78.5683 84.9389 78.4906L87.0472 78.2021Z" fill="#3A4374"/><ellipse id="Oval_2" cx="81.5687" cy="46.2882" rx="19.1903" ry="19.2638" fill="#8E949D"/><path id="Shape_4" fill-rule="evenodd" clip-rule="evenodd" d="M61.9845 47.1747C62.4607 57.6774 71.0794 65.9481 81.5529 65.9529C82.4664 65.9525 83.3788 65.8909 84.2841 65.7685C94.9829 64.2491 102.445 54.3326 100.975 43.5867C99.5407 33.172 90.2025 25.7299 79.7727 26.6897C69.3429 27.6495 61.5083 36.6719 61.9845 47.1747ZM78.9574 27.5936C79.8176 27.4753 80.6847 27.4164 81.5529 27.4172H81.5848C90.9448 27.458 98.8543 34.3937 100.152 43.6989C101.195 51.2598 97.5977 58.7068 91.0378 62.5669C84.478 66.4271 76.2475 65.9402 70.1847 61.3333C64.122 56.7263 61.4209 48.9067 63.3413 41.5211C65.2616 34.1355 71.4251 28.6385 78.9574 27.5936Z" fill="#3A4374"/><ellipse id="Oval_3" cx="81.5686" cy="46.289" rx="15.5886" ry="15.6483" fill="#8E949D"/><path id="Shape_5" fill-rule="evenodd" clip-rule="evenodd" d="M65.598 46.8572C65.8949 55.4918 72.954 62.3375 81.5607 62.3374C82.2954 62.3382 83.0292 62.2873 83.7568 62.185C92.2818 60.997 98.3359 53.2418 97.447 44.6483C96.5581 36.0548 89.0458 29.7137 80.4596 30.3092C71.8733 30.9047 65.3012 38.2227 65.598 46.8572ZM79.4444 31.2171C80.1458 31.1214 80.8528 31.0732 81.5607 31.0728C89.7341 31.0903 96.4251 37.6037 96.6934 45.804C96.9618 54.0044 90.7113 60.9446 82.5566 61.5007C74.4019 62.0567 67.2738 56.0287 66.4351 47.8672C65.5963 39.7057 71.3484 32.3439 79.4444 31.2171Z" fill="#3A4374"/><path id="Path_5" d="M99.8964 89.7141C98.8417 88.3705 97.5059 87.2758 95.9833 86.5075C91.4074 84.1025 86.1606 84.1827 81.345 85.7058C79.636 86.251 76.3219 87.0286 76.1462 89.3053C76.1592 90.2377 76.7787 91.0517 77.6715 91.3094C78.5454 91.5369 79.4579 91.5724 80.3468 91.4136C79.0802 91.3795 77.8287 91.6956 76.7292 92.3275C75.699 93.057 75.132 94.6523 75.9785 95.6223C76.3526 96.0256 76.84 96.3052 77.376 96.424C78.5413 96.7457 79.7736 96.729 80.9298 96.3759C79.6787 96.6165 78.4594 97.0011 77.2962 97.5223C76.5934 97.8349 75.8108 98.388 75.8906 99.1256C75.9705 99.8631 76.6892 100.2 77.3441 100.424C78.7218 100.899 80.1611 101.169 81.6166 101.226C80.3603 101.455 79.1724 101.967 78.1427 102.725C76.5455 104.072 76.5455 106.629 78.7097 107.359C79.4039 107.562 80.1276 107.646 80.8499 107.607C87.2387 107.607 94.8892 106.805 99.2176 101.466C101.348 98.968 102.113 95.5754 101.262 92.3997C100.966 91.4314 100.504 90.5226 99.8964 89.7141Z" fill="#8E949D"/><path id="Shape_6" fill-rule="evenodd" clip-rule="evenodd" d="M78.5819 107.72C79.3021 107.931 80.0525 108.021 80.802 107.984L80.826 108C87.1509 107.992 94.9931 107.271 99.5211 101.715C101.742 99.1142 102.535 95.5771 101.637 92.2717C101.319 91.2654 100.83 90.3217 100.192 89.482C99.1056 88.0783 97.7228 86.9339 96.1431 86.1311C91.8466 83.9345 86.536 83.6379 81.2013 85.3294L80.6902 85.4817C80.6725 85.4872 80.6546 85.4927 80.6367 85.4982C78.7359 86.0832 75.9049 86.9546 75.723 89.2736C75.714 90.2817 76.3184 91.1934 77.2483 91.5743C76.9662 91.6869 76.6982 91.8322 76.4497 92.0072C75.7473 92.5152 75.2805 93.2876 75.156 94.1476C75.0623 94.7657 75.2334 95.3948 75.6271 95.8792C76.0516 96.3435 76.61 96.6631 77.2243 96.7931C77.3662 96.8354 77.5101 96.8702 77.6556 96.8973L77.0886 97.1378C75.9865 97.6589 75.3716 98.4926 75.4914 99.182C75.6112 99.8715 76.1462 100.417 77.2084 100.785C77.9885 101.058 78.7899 101.264 79.6041 101.403C78.9772 101.637 78.394 101.976 77.8792 102.405C76.9498 103.191 76.4958 104.406 76.6813 105.611C76.8789 106.612 77.6094 107.423 78.5819 107.72ZM77.4559 97.8352C78.2906 97.4585 79.1563 97.1554 80.0434 96.9294C80.3784 96.8901 80.7096 96.8231 81.0336 96.729C81.2348 96.6637 81.3527 96.4547 81.3052 96.248C81.2538 96.0412 81.0504 95.9109 80.842 95.9514C80.5225 96.0155 80.1951 96.0876 79.8757 96.1678C79.0744 96.2733 78.2602 96.2188 77.4799 96.0075C77.024 95.9137 76.6067 95.6847 76.282 95.3501C76.0354 95.0352 75.9312 94.6312 75.9945 94.2358C76.0874 93.5922 76.436 93.0138 76.9608 92.6325C77.6552 92.1915 78.4487 91.9324 79.2687 91.8789C79.4537 91.8751 79.6368 91.8544 79.8171 91.8341C80.0105 91.8122 80.2008 91.7908 80.3868 91.7908C80.6023 91.7656 80.7604 91.5752 80.7461 91.3579C80.7214 91.1435 80.5378 90.9835 80.3229 90.9891C80.2998 90.9907 80.2766 90.9923 80.2533 90.9938C79.9231 91.0164 79.5795 91.0398 79.2288 91.0773C78.7455 91.1087 78.2606 91.0489 77.7993 90.9009C77.0774 90.6969 76.5682 90.0498 76.5375 89.2976C76.6811 87.5036 79.2479 86.7258 80.941 86.2127L80.9458 86.2112L81.4649 86.0509C86.5999 84.4476 91.6869 84.7282 95.7997 86.8525C97.2752 87.5991 98.5658 88.6673 99.577 89.979C100.161 90.7435 100.609 91.6032 100.903 92.5203C101.717 95.5699 100.976 98.8266 98.9222 101.218C94.6098 106.517 86.9992 107.207 80.842 107.207C80.1659 107.251 79.4873 107.175 78.8375 106.982C78.1361 106.78 77.606 106.201 77.4639 105.483C77.3222 104.557 77.6773 103.625 78.3983 103.03C79.3886 102.318 80.5228 101.834 81.7204 101.611C81.9124 101.561 82.0383 101.376 82.0159 101.178C81.9957 100.98 81.831 100.828 81.6326 100.825C80.214 100.765 78.8121 100.495 77.4719 100.024C76.9608 99.8554 76.3539 99.647 76.29 99.0538C76.2261 98.4605 77.0806 98.0036 77.4559 97.8352Z" fill="#3A4374"/><path id="Path_6" d="M55.3669 46.5928C55.3669 46.5928 65.0938 61.2631 59.2081 61.4715C53.3225 61.6799 52.8193 60.1328 52.8193 60.1328" fill="#8E949D"/><path id="Path_7" d="M58.1538 61.9372C53.2185 61.9372 52.5077 60.5824 52.4119 60.3339C52.3429 60.115 52.4596 59.8807 52.6754 59.8048C52.8914 59.7361 53.1223 59.8538 53.1945 60.0694C53.1945 60.0694 53.9133 61.3119 59.192 61.1195C59.7173 61.1681 60.2226 60.9051 60.4857 60.4461C61.7475 57.985 56.9719 49.8242 55.0233 46.8901C54.9149 46.7006 54.9707 46.4591 55.1512 46.3369C55.3318 46.2147 55.5757 46.2532 55.7101 46.4252C56.4528 47.5475 62.8975 57.488 61.2204 60.855C60.8289 61.5879 60.0513 62.0282 59.2239 61.9853L58.1538 61.9372Z" fill="#3A4374"/><ellipse id="Oval_4" cx="82.4551" cy="45.7992" rx="3.5298" ry="6.03647" fill="#C0C5DC"/><ellipse id="Oval_5" cx="39.2591" cy="45.7991" rx="2.69127" ry="4.88209" fill="#8E949D"/></g></g></svg>
                            {:else}
                                <svg width="102" height="108" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none" opacity=".5"><path d="M48.73 15.593C25.822 15.59 7.246 34.224 7.235 57.22c-.01 22.997 18.55 41.649 41.458 41.665 22.909.016 41.494-18.61 41.516-41.606a41.72 41.72 0 00-12.132-29.473A41.4 41.4 0 0048.73 15.593z" stroke="#3A4374" stroke-width="1.045" stroke-linecap="round" stroke-linejoin="round"/><ellipse fill="#231F20" cx="70.476" cy="43.691" rx="1.917" ry="2.862"/><path d="M0 44.902l97.429-21.645-19.167-5.612S67.881.37 65.11.01C62.338-.351 11.979 10.43 11.979 10.43L8.785 34.77 0 44.902zM20.284 103.503L9.272 86.027l48.14-8.265 3.305 16.746 4.888-15.424 24.628 4.882-5.007 19.537z" fill="#3A4374"/><path d="M81.97 65.607l4.438-.617L88.7 81.618a2.115 2.115 0 01-1.799 2.387l-.261.036a2.11 2.11 0 01-2.377-1.806L81.97 65.607z" fill="#FFF"/><path d="M86.352 84.471A2.519 2.519 0 0183.87 82.3l-2.292-16.634a.4.4 0 01.343-.45l4.432-.617a.398.398 0 01.296.08c.085.063.14.16.151.265l2.276 16.626a2.508 2.508 0 01-.479 1.868 2.49 2.49 0 01-1.66.97l-.256.04a2.474 2.474 0 01-.328.024zm-3.929-18.518l2.236 16.234c.132.936.991 1.59 1.925 1.467h.263a1.714 1.714 0 001.454-1.932l-2.236-16.234-3.642.465z" fill="#3A4374"/><path fill="#FFF" d="M78.562 50.93l7.159-.995 2.242 16.263-7.16.995z"/><path d="M80.802 67.605a.43.43 0 01-.24-.08.361.361 0 01-.151-.265l-2.244-16.274a.394.394 0 01.072-.296.423.423 0 01.263-.153l7.187-.994a.39.39 0 01.448.345l2.244 16.266a.4.4 0 01-.344.448l-7.187.995-.048.008zm-1.789-16.33l2.132 15.472 6.39-.882-2.133-15.472-6.389.882z" fill="#3A4374"/><path d="M85.573 78.014l.725-.1a3.248 3.248 0 013.664 2.773l3.013 21.85a3.248 3.248 0 01-2.77 3.661l-.726.1a3.248 3.248 0 01-3.664-2.773l-3.013-21.85a3.248 3.248 0 012.77-3.66z" fill="#FFF"/><path d="M88.429 106.83a2.959 2.959 0 01-2.923-2.558L82.31 81.064a2.959 2.959 0 012.516-3.343l2.108-.288a2.949 2.949 0 013.322 2.525l3.195 23.248a2.964 2.964 0 01-2.516 3.335l-2.108.297-.4-.008zm-1.086-28.628a1.838 1.838 0 00-.296 0l-2.108.289a2.13 2.13 0 00-1.422.842c-.35.455-.5 1.034-.415 1.603l3.194 23.248a2.166 2.166 0 002.396 1.844l2.108-.297a2.163 2.163 0 001.837-2.405l-3.194-23.248a2.159 2.159 0 00-2.124-1.868l.024-.008z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="19.19" ry="19.264"/><path d="M81.553 65.953c-10.474-.005-19.093-8.275-19.569-18.778-.476-10.503 7.359-19.525 17.789-20.485 10.43-.96 19.768 6.482 21.202 16.897 1.47 10.746-5.992 20.662-16.691 22.182-.905.122-1.818.184-2.731.184zm0-38.536c-.868 0-1.736.059-2.596.177-7.532 1.045-13.696 6.542-15.616 13.927-1.92 7.386.78 15.206 6.844 19.812a18.74 18.74 0 0020.853 1.234c6.56-3.86 10.157-11.307 9.114-18.868-1.298-9.305-9.207-16.24-18.567-16.282h-.032z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="15.589" ry="15.648"/><path d="M81.56 62.338c-8.606 0-15.665-6.846-15.962-15.48-.297-8.635 6.275-15.953 14.862-16.548 8.586-.596 16.098 5.745 16.987 14.339.889 8.593-5.165 16.348-13.69 17.536-.728.103-1.462.154-2.196.153zm0-31.265c-.707 0-1.414.049-2.116.144-8.096 1.127-13.848 8.489-13.009 16.65.839 8.162 7.967 14.19 16.122 13.634 8.154-.556 14.405-7.496 14.136-15.697-.268-8.2-6.959-14.713-15.132-14.731z" fill="#3A4374"/><path d="M99.896 89.714a11.645 11.645 0 00-3.913-3.206c-4.576-2.405-9.822-2.325-14.638-.802-1.709.545-5.023 1.323-5.199 3.6a2.115 2.115 0 001.526 2.004 6.254 6.254 0 002.675.104 6.887 6.887 0 00-3.618.914c-1.03.73-1.597 2.324-.75 3.294.374.404.861.683 1.397.802a6.365 6.365 0 003.554-.048c-1.251.24-2.47.625-3.634 1.146-.703.313-1.485.866-1.405 1.604.08.737.798 1.074 1.453 1.298 1.378.475 2.817.745 4.273.802a8.4 8.4 0 00-3.474 1.5c-1.598 1.346-1.598 3.903.567 4.633a6.366 6.366 0 002.14.248c6.389 0 14.04-.801 18.368-6.14a9.993 9.993 0 002.044-9.067 8.702 8.702 0 00-1.366-2.686z" fill="#FFF"/><path d="M80.802 107.984c-.75.037-1.5-.053-2.22-.265a2.762 2.762 0 01-1.9-2.108 3.502 3.502 0 011.197-3.207 5.952 5.952 0 011.725-1.002 14.816 14.816 0 01-2.396-.617c-1.062-.369-1.597-.914-1.717-1.603-.12-.69.495-1.523 1.598-2.044l.567-.24a4.211 4.211 0 01-.432-.105 3.008 3.008 0 01-1.597-.914 2.219 2.219 0 01-.471-1.732 3.209 3.209 0 011.294-2.14c.248-.175.516-.32.798-.433a2.461 2.461 0 01-1.525-2.3c.184-2.342 3.067-3.207 4.967-3.793l.511-.152c5.335-1.691 10.646-1.395 14.942.802a11.99 11.99 0 014.049 3.35 9.207 9.207 0 011.445 2.79 10.363 10.363 0 01-2.116 9.444c-4.528 5.555-12.37 6.277-18.695 6.285l-.024-.016zm-.759-11.055c-.887.226-1.752.53-2.587.906-.375.168-1.23.625-1.166 1.219.064.593.67.801 1.182.97 1.34.47 2.742.74 4.16.801a.392.392 0 01.384.353.4.4 0 01-.296.433 8.289 8.289 0 00-3.322 1.419 2.659 2.659 0 00-.934 2.453 1.953 1.953 0 001.373 1.499 5.73 5.73 0 002.005.224c6.157 0 13.768-.69 18.08-5.988a9.567 9.567 0 001.98-8.698 8.413 8.413 0 00-1.325-2.541 11.094 11.094 0 00-3.777-3.127c-4.113-2.124-9.2-2.405-14.335-.801l-.52.16c-1.692.513-4.264 1.29-4.408 3.086.031.753.54 1.4 1.262 1.604a3.86 3.86 0 001.43.176c.375-.04.742-.064 1.094-.088a.416.416 0 01.423.369.408.408 0 01-.36.433c-.359 0-.734.08-1.117.088a4.898 4.898 0 00-2.308.753c-.525.382-.874.96-.967 1.604-.063.395.041.799.288 1.114.325.334.742.563 1.198.657a6.11 6.11 0 002.396.16c.32-.08.646-.152.966-.216a.4.4 0 01.463.297.41.41 0 01-.271.48 6.097 6.097 0 01-.99.201z" fill="#3A4374"/><path d="M55.367 46.593s9.727 14.67 3.84 14.879c-5.885.208-6.388-1.339-6.388-1.339" fill="#FFF"/><path d="M58.154 61.937c-4.936 0-5.646-1.355-5.742-1.603a.425.425 0 01.263-.53.415.415 0 01.52.265s.718 1.243 5.997 1.05a1.348 1.348 0 001.294-.673c1.261-2.461-3.514-10.622-5.463-13.556a.418.418 0 01.128-.553.414.414 0 01.56.088c.742 1.122 7.186 11.063 5.51 14.43a2.139 2.139 0 01-1.997 1.13l-1.07-.048z" fill="#3A4374"/><ellipse fill="#C0C5DC" cx="82.455" cy="45.799" rx="3.53" ry="6.036"/><ellipse fill="#3A4374" cx="39.259" cy="45.799" rx="2.691" ry="4.882"/></g></svg>
                            {/if}
                            <div class="md:max-w-[25.625rem]">
                                <h2 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-[#248AB6] leading-none mt-10">There is no feedback yet.</h2>
                                <p class="text-13 md:text-base text-[#647196] dark:text-[#D1D7E9] my-6 md:mt-4 md:mb-10">Got a suggestion? Found a bug that needs to be squashed? We love hearing about new ideas to improve our app.</p>
                            </div>
                            <div>
                                <a use:inertia href="/dashboard/task/create" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
            <!--end suggestions-->

            <!--pagination-->
            <div class="flex justify-between my-6 px-6 sm:px-0">
                {#if tasks.prev_page_url}
                    <a use:inertia={{replace: true}} href="{tasks.prev_page_url}" class="text-[#238AB6] hover:opacity-40"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></a>
                {:else}
                    <button type="button" class="text-[#238AB6] opacity-40" disabled><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></button>
                {/if}

                {#if tasks.next_page_url}
                    <a use:inertia={{replace: true}} href="{tasks.next_page_url}" class="text-[#238AB6] hover:opacity-40"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></a>
                {:else}
                    <button type="button" class="text-[#238AB6] opacity-40" disabled><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></button>
                {/if}
            </div>
        </main>
    </div>
</div>

{#if showSearch}
    <div
        class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24"
        id="headlessui-dialog-530"
        role="dialog"
        aria-modal="true"
        data-headlessui-state="open"
        on:keydown={() => {}}
        on:click|self={() => (showSearch = false)}>
        <div class="fixed inset-0 bg-slate-900/25 backdrop-blur transition-opacity opacity-100" on:keydown={() => {}} on:click|self={() => (showSearch = false)}/>
        <div class="relative w-full max-w-lg transform px-4 transition-all opacity-100 scale-100" on:keydown={() => {}} on:click|self={() => (showSearch = false)}>
            <div class="overflow-hidden rounded-lg bg-white shadow-md" id="headlessui-dialog-panel-531" data-headlessui-state="open">
                <form on:submit|preventDefault class="relative flex items-center justify-between shadow-sm">
                    <input
                        on:keyup={({ target: { value } }) => debounce(value)}
                        on:keydown={submitSearch}
                        class="block w-full appearance-none bg-transparent py-4 pl-4 pr-12 text-base text-slate-900 outline-none border-0 border-none placeholder:text-slate-600 focus:outline-none sm:text-sm sm:leading-6 focus-visible"
                        aria-label="Search components"
                        id="headlessui-combobox-input-532"
                        role="combobox"
                        type="text"
                        aria-expanded="true"
                        aria-autocomplete="list"
                        data-headlessui-state="open"
                        value={search}
                        tabindex="0"
                        style="caret-color: rgb(107, 114, 128);"
                        data-focus-visible-added=""
                        aria-controls="headlessui-combobox-options-533"
                        placeholder="Search by Title or Tag or User"
                        autofocus
                    />
                    <button type="submit"><svg class="cursor-pointer absolute right-4 top-4 h-6 w-6 fill-slate-400" xmlns="http://www.w3.org/2000/svg"><path d="M20.47 21.53a.75.75 0 1 0 1.06-1.06l-1.06 1.06Zm-9.97-4.28a6.75 6.75 0 0 1-6.75-6.75h-1.5a8.25 8.25 0 0 0 8.25 8.25v-1.5ZM3.75 10.5a6.75 6.75 0 0 1 6.75-6.75v-1.5a8.25 8.25 0 0 0-8.25 8.25h1.5Zm6.75-6.75a6.75 6.75 0 0 1 6.75 6.75h1.5a8.25 8.25 0 0 0-8.25-8.25v1.5Zm11.03 16.72-5.196-5.197-1.061 1.06 5.197 5.197 1.06-1.06Zm-4.28-9.97c0 1.864-.755 3.55-1.977 4.773l1.06 1.06A8.226 8.226 0 0 0 18.75 10.5h-1.5Zm-1.977 4.773A6.727 6.727 0 0 1 10.5 17.25v1.5a8.226 8.226 0 0 0 5.834-2.416l-1.061-1.061Z"/></svg></button>
                </form>
            </div>
        </div>
    </div>
{/if}

