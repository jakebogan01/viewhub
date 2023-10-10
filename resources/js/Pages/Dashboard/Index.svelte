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
    export let statuses;
    export let user;
    export let showSearch;
    export let searchInput;
    const randomColor = ['#D53E4F', '#F46D43', '#FDAE61', '#FEE08B', '#E6F598', '#ABDDA4', '#66C2A6', '#3388BD', '#5E4FA2', '#31207d'];

    console.log('Filters: ', filters);
    console.log('Count: ', count);
    console.log('Tasks: ', tasks);
    console.log('Tags: ', tags);
    console.log('Statuses: ', statuses);
    console.log('User: ', user);

    let route = '/dashboard';
    let showStatusDropdown = false;
    let showSortByDropdown = false;
    let search = filters.search || '';
    // let updateStatusDropdownSelection = filters.status || 'All';
    let updateSortByDropdownSelection = filters.liked ? 'Most Popular' :  'All';
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

    $: filterByDateData = {
        search: filters.search,
        tag: filters.tag,
        status: filters.status,
        liked: filters.liked,
        ...(!rotateArrow) && {sortby: 'oldest'},
    }

    const sortByCreatedBy = () => {
        rotateArrow = !rotateArrow;
        Inertia.get(route, filterByDateData, {
            // prevents the browser's history from being updated
            replace: true,
            preserveScroll: true
        });
    }
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>


<div class="flex gap-x-[30px]">
    <div class="min-w-[255px] max-w-[255px] space-y-6">
        <!--start company banner-->
        <aside class="relative flex-1 flex items-center md:items-end justify-between bg-green-500 h-[11.125rem] 1440:max-h-[8.5625rem] md:rounded-[0.625rem] md:h-auo px-6 py-6 md:px-8 md:py-6 z-50">
            <div class="mt-1 md:mt-0 whitespace-nowrap">
                <h1 class="font-bold text-15 md:text-xl text-white leading-none">PNY Technologies</h1>
                <span class="font-medium text-13 md:text-15 text-[#E1D5FC]">Feedback Board</span>
            </div>
            <div on:keydown={()=>{}} on:click class="md:hidden space-y-1">
                <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
                <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
                <div class="w-5 h-[0.1875rem] bg-white duration-300 ease-in-out"></div>
            </div>
        </aside>
        <!--end company banner-->

        <!--start tags-->
        <div class="bg-white dark:bg-[#1E283A] p-6 rounded-[0.625rem]">
            <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#17202F] dark:hover:bg-[#3A4374] dark:hover:text-white rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">All</button>

                {#each tags as tag (tag.id)}
                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, tag: tag.name, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#17202F] dark:hover:bg-[#3A4374] dark:hover:text-white rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">{tag.name}</button>
                {/each}
            </div>
        </div>
        <!--end tags-->

        <!--start departments-->
        <div class="hidden md:block ">
            <div class="bg-white dark:bg-[#1E293B] p-6 rounded-[0.625rem]">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-lg text-[#3A4374] dark:text-white">Task Board</h4>
                    <a href="/roadmap" class="font-semibold text-13 text-[#4661E6] hover:text-[#CED7FF] dark:text-[#95A1B8] dark:hover:text-[#4661E6]">View</a>
                </div>
                <div>
                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="flex items-center justify-between w-full mt-4 1440:mt-6">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-[#9E0142] rounded-full mr-3"></span>
                            <span class="text-base text-[#647196] dark:text-[#D1D7E9] dark:hover:text-[#4661E6] capitalize">All</span>
                        </span>
                    </button>
                    {#each statuses as status, i (status.id)}
                        {#if status.count > 0}
                            <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: status.name, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="flex items-center justify-between w-full mt-4 1440:mt-6">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-3" style="background-color: {randomColor[i]}"></span>
                                    <span class="text-base text-[#647196] dark:text-[#D1D7E9] dark:hover:text-[#4661E6] capitalize">{status.name}</span>
                                </span>
                                <span class="font-bold text-base text-[#647196] dark:text-[#D1D7E9] cursor-default">{status.count}</span>
                            </button>
                        {/if}
                    {/each}
                </div>
            </div>
        </div>
        <!--end departments-->
    </div>

    <div class="w-full space-y-6">
        <!--start nav-->
        <nav class="relative flex items-center justify-between md:rounded-[0.625rem] px-6 py-2 md:py-3.5 md:px-3 1440:px-4 1440:py-4 bg-[#10263E] text-13 text-[#F3F4FE] z-50">
            <div class="flex items-center">
                <div class="hidden md:flex items-center mr-10">
                    <svg width="23" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 2.274c2.237 0 4.339.854 5.923 2.408a8.123 8.123 0 012.465 5.839 8.084 8.084 0 01-1.7 4.979 8.457 8.457 0 01-3.652 2.71l-.31.112.003.826h.369c.262 0 .475.21.475.469a.47.47 0 01-.39.46l-.085.008h-.365l.004 1.02h.36c.263 0 .476.21.476.469a.47.47 0 01-.39.461l-.085.008h-.358l.006 1.487a.466.466 0 01-.381.46l-.094.01H9.23a.478.478 0 01-.466-.378l-.01-.092.006-1.487h-.357a.472.472 0 01-.475-.47.47.47 0 01.39-.46l.085-.008h.361l.004-1.02h-.365a.472.472 0 01-.475-.468.47.47 0 01.39-.462l.085-.007h.368l.004-.826a8.452 8.452 0 01-3.996-2.867 8.08 8.08 0 01-1.666-5.056c.032-2.127.923-4.152 2.511-5.7 1.508-1.471 3.448-2.322 5.493-2.416l.324-.009h.06zm1.791 19.769H9.709l-.004 1.02h3.59l-.004-1.02zm-.007-1.958H9.716l-.003 1.02h3.574l-.003-1.02zM11.5 3.212h-.054c-3.946.027-7.327 3.325-7.384 7.2-.048 3.266 2.14 6.192 5.322 7.118.174.05.3.193.332.364l.008.088-.004 1.166h3.56l-.004-1.166a.47.47 0 01.34-.452c3.134-.912 5.323-3.794 5.323-7.01a7.197 7.197 0 00-2.185-5.173A7.453 7.453 0 0011.5 3.212zm.829 1.782a.4.4 0 01.401.397v.322c.48.12.932.307 1.346.552l.228-.226a.405.405 0 01.569 0L16.046 7.2a.393.393 0 010 .56l-.23.228c.247.41.437.858.557 1.333h.323a.4.4 0 01.402.397v1.645a.4.4 0 01-.402.396h-.323c-.12.476-.31.924-.557 1.333l.23.228a.393.393 0 010 .56l-1.173 1.163a.405.405 0 01-.57 0l-.227-.227a5.02 5.02 0 01-1.346.553v.322a.4.4 0 01-.401.396H10.67a.4.4 0 01-.402-.396v-.322a5.022 5.022 0 01-1.345-.553l-.228.227a.405.405 0 01-.569 0L6.954 13.88a.393.393 0 010-.56l.23-.228a4.924 4.924 0 01-.557-1.333h-.324a.4.4 0 01-.401-.396V9.719a.4.4 0 01.401-.397h.324c.12-.475.31-.923.557-1.333l-.23-.228a.393.393 0 010-.56L8.127 6.04a.405.405 0 01.569 0l.228.226a5.021 5.021 0 011.345-.552V5.39a.4.4 0 01.402-.397zM11.5 7.721c-1.572 0-2.846 1.263-2.846 2.82 0 1.558 1.274 2.82 2.846 2.82s2.846-1.262 2.846-2.82c0-1.557-1.274-2.82-2.846-2.82zm11.025 4.152c.262 0 .475.21.475.469a.47.47 0 01-.39.461l-.085.008h-.498a.472.472 0 01-.475-.469.47.47 0 01.39-.461l.085-.008h.498zm-21.552 0c.262 0 .475.21.475.469a.47.47 0 01-.39.461l-.085.008H.475A.472.472 0 010 12.342a.47.47 0 01.39-.461l.085-.008h.498zM3.112 3.45l.074.06.46.451c.185.183.186.48 0 .663a.476.476 0 01-.596.062l-.075-.06-.459-.451a.465.465 0 01-.001-.663.48.48 0 01.597-.062zm17.373.062c.162.16.182.408.06.59l-.061.073-.46.45a.476.476 0 01-.67 0 .464.464 0 01-.06-.59l.06-.074.46-.45a.48.48 0 01.671 0zM11.5 0c.233 0 .427.166.467.384l.008.085v.49a.472.472 0 01-.475.468.473.473 0 01-.467-.384l-.008-.084v-.49c0-.26.213-.469.475-.469z" fill="#FFF" fill-rule="nonzero"/></svg>
                    <span class="font-bold text-white text-lg ml-4">{count} Suggestions</span>
                </div>
            </div>
            <div class="dark:bg-slate-900 relative pointer-events-auto">
                <button on:click={()=>{showSearch = !showSearch}} type="button" class="hidden w-full lg:flex items-center text-sm leading-6 text-slate-400 border border-[#1e3e66] rounded-md ring-1 ring-slate-900/10 shadow-sm py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700">
                    <svg width="24" height="24" fill="none" aria-hidden="true" class="mr-3 flex-none"><path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle></svg>
                    Quick search...<span class="ml-auto pl-3 flex-none text-xs font-semibold">&#8984;K</span>
                </button>
            </div>
            <div>
                <a use:inertia href="/dashboard/task/create" class="bg-[#AD1FE9] hover:bg-[#C75AF6] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
            </div>
        </nav>
        <!--end nav-->

        <main>
            <!--start suggestions-->
            <div class="relative space-y-5 px-6 md:px-0">
                {#if tasks.data.length < 1}
                    <div class="flex flex-col items-center justify-center bg-white dark:bg-[#1E293B] py-[4.75rem] md:py-[6.9375rem] px-[1.625rem] text-center rounded-[0.625rem] cursor-default">
                        <svg width="102" height="108" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none" opacity=".5"><path d="M48.73 15.593C25.822 15.59 7.246 34.224 7.235 57.22c-.01 22.997 18.55 41.649 41.458 41.665 22.909.016 41.494-18.61 41.516-41.606a41.72 41.72 0 00-12.132-29.473A41.4 41.4 0 0048.73 15.593z" stroke="#3A4374" stroke-width="1.045" stroke-linecap="round" stroke-linejoin="round"/><ellipse fill="#231F20" cx="70.476" cy="43.691" rx="1.917" ry="2.862"/><path d="M0 44.902l97.429-21.645-19.167-5.612S67.881.37 65.11.01C62.338-.351 11.979 10.43 11.979 10.43L8.785 34.77 0 44.902zM20.284 103.503L9.272 86.027l48.14-8.265 3.305 16.746 4.888-15.424 24.628 4.882-5.007 19.537z" fill="#3A4374"/><path d="M81.97 65.607l4.438-.617L88.7 81.618a2.115 2.115 0 01-1.799 2.387l-.261.036a2.11 2.11 0 01-2.377-1.806L81.97 65.607z" fill="#FFF"/><path d="M86.352 84.471A2.519 2.519 0 0183.87 82.3l-2.292-16.634a.4.4 0 01.343-.45l4.432-.617a.398.398 0 01.296.08c.085.063.14.16.151.265l2.276 16.626a2.508 2.508 0 01-.479 1.868 2.49 2.49 0 01-1.66.97l-.256.04a2.474 2.474 0 01-.328.024zm-3.929-18.518l2.236 16.234c.132.936.991 1.59 1.925 1.467h.263a1.714 1.714 0 001.454-1.932l-2.236-16.234-3.642.465z" fill="#3A4374"/><path fill="#FFF" d="M78.562 50.93l7.159-.995 2.242 16.263-7.16.995z"/><path d="M80.802 67.605a.43.43 0 01-.24-.08.361.361 0 01-.151-.265l-2.244-16.274a.394.394 0 01.072-.296.423.423 0 01.263-.153l7.187-.994a.39.39 0 01.448.345l2.244 16.266a.4.4 0 01-.344.448l-7.187.995-.048.008zm-1.789-16.33l2.132 15.472 6.39-.882-2.133-15.472-6.389.882z" fill="#3A4374"/><path d="M85.573 78.014l.725-.1a3.248 3.248 0 013.664 2.773l3.013 21.85a3.248 3.248 0 01-2.77 3.661l-.726.1a3.248 3.248 0 01-3.664-2.773l-3.013-21.85a3.248 3.248 0 012.77-3.66z" fill="#FFF"/><path d="M88.429 106.83a2.959 2.959 0 01-2.923-2.558L82.31 81.064a2.959 2.959 0 012.516-3.343l2.108-.288a2.949 2.949 0 013.322 2.525l3.195 23.248a2.964 2.964 0 01-2.516 3.335l-2.108.297-.4-.008zm-1.086-28.628a1.838 1.838 0 00-.296 0l-2.108.289a2.13 2.13 0 00-1.422.842c-.35.455-.5 1.034-.415 1.603l3.194 23.248a2.166 2.166 0 002.396 1.844l2.108-.297a2.163 2.163 0 001.837-2.405l-3.194-23.248a2.159 2.159 0 00-2.124-1.868l.024-.008z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="19.19" ry="19.264"/><path d="M81.553 65.953c-10.474-.005-19.093-8.275-19.569-18.778-.476-10.503 7.359-19.525 17.789-20.485 10.43-.96 19.768 6.482 21.202 16.897 1.47 10.746-5.992 20.662-16.691 22.182-.905.122-1.818.184-2.731.184zm0-38.536c-.868 0-1.736.059-2.596.177-7.532 1.045-13.696 6.542-15.616 13.927-1.92 7.386.78 15.206 6.844 19.812a18.74 18.74 0 0020.853 1.234c6.56-3.86 10.157-11.307 9.114-18.868-1.298-9.305-9.207-16.24-18.567-16.282h-.032z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="15.589" ry="15.648"/><path d="M81.56 62.338c-8.606 0-15.665-6.846-15.962-15.48-.297-8.635 6.275-15.953 14.862-16.548 8.586-.596 16.098 5.745 16.987 14.339.889 8.593-5.165 16.348-13.69 17.536-.728.103-1.462.154-2.196.153zm0-31.265c-.707 0-1.414.049-2.116.144-8.096 1.127-13.848 8.489-13.009 16.65.839 8.162 7.967 14.19 16.122 13.634 8.154-.556 14.405-7.496 14.136-15.697-.268-8.2-6.959-14.713-15.132-14.731z" fill="#3A4374"/><path d="M99.896 89.714a11.645 11.645 0 00-3.913-3.206c-4.576-2.405-9.822-2.325-14.638-.802-1.709.545-5.023 1.323-5.199 3.6a2.115 2.115 0 001.526 2.004 6.254 6.254 0 002.675.104 6.887 6.887 0 00-3.618.914c-1.03.73-1.597 2.324-.75 3.294.374.404.861.683 1.397.802a6.365 6.365 0 003.554-.048c-1.251.24-2.47.625-3.634 1.146-.703.313-1.485.866-1.405 1.604.08.737.798 1.074 1.453 1.298 1.378.475 2.817.745 4.273.802a8.4 8.4 0 00-3.474 1.5c-1.598 1.346-1.598 3.903.567 4.633a6.366 6.366 0 002.14.248c6.389 0 14.04-.801 18.368-6.14a9.993 9.993 0 002.044-9.067 8.702 8.702 0 00-1.366-2.686z" fill="#FFF"/><path d="M80.802 107.984c-.75.037-1.5-.053-2.22-.265a2.762 2.762 0 01-1.9-2.108 3.502 3.502 0 011.197-3.207 5.952 5.952 0 011.725-1.002 14.816 14.816 0 01-2.396-.617c-1.062-.369-1.597-.914-1.717-1.603-.12-.69.495-1.523 1.598-2.044l.567-.24a4.211 4.211 0 01-.432-.105 3.008 3.008 0 01-1.597-.914 2.219 2.219 0 01-.471-1.732 3.209 3.209 0 011.294-2.14c.248-.175.516-.32.798-.433a2.461 2.461 0 01-1.525-2.3c.184-2.342 3.067-3.207 4.967-3.793l.511-.152c5.335-1.691 10.646-1.395 14.942.802a11.99 11.99 0 014.049 3.35 9.207 9.207 0 011.445 2.79 10.363 10.363 0 01-2.116 9.444c-4.528 5.555-12.37 6.277-18.695 6.285l-.024-.016zm-.759-11.055c-.887.226-1.752.53-2.587.906-.375.168-1.23.625-1.166 1.219.064.593.67.801 1.182.97 1.34.47 2.742.74 4.16.801a.392.392 0 01.384.353.4.4 0 01-.296.433 8.289 8.289 0 00-3.322 1.419 2.659 2.659 0 00-.934 2.453 1.953 1.953 0 001.373 1.499 5.73 5.73 0 002.005.224c6.157 0 13.768-.69 18.08-5.988a9.567 9.567 0 001.98-8.698 8.413 8.413 0 00-1.325-2.541 11.094 11.094 0 00-3.777-3.127c-4.113-2.124-9.2-2.405-14.335-.801l-.52.16c-1.692.513-4.264 1.29-4.408 3.086.031.753.54 1.4 1.262 1.604a3.86 3.86 0 001.43.176c.375-.04.742-.064 1.094-.088a.416.416 0 01.423.369.408.408 0 01-.36.433c-.359 0-.734.08-1.117.088a4.898 4.898 0 00-2.308.753c-.525.382-.874.96-.967 1.604-.063.395.041.799.288 1.114.325.334.742.563 1.198.657a6.11 6.11 0 002.396.16c.32-.08.646-.152.966-.216a.4.4 0 01.463.297.41.41 0 01-.271.48 6.097 6.097 0 01-.99.201z" fill="#3A4374"/><path d="M55.367 46.593s9.727 14.67 3.84 14.879c-5.885.208-6.388-1.339-6.388-1.339" fill="#FFF"/><path d="M58.154 61.937c-4.936 0-5.646-1.355-5.742-1.603a.425.425 0 01.263-.53.415.415 0 01.52.265s.718 1.243 5.997 1.05a1.348 1.348 0 001.294-.673c1.261-2.461-3.514-10.622-5.463-13.556a.418.418 0 01.128-.553.414.414 0 01.56.088c.742 1.122 7.186 11.063 5.51 14.43a2.139 2.139 0 01-1.997 1.13l-1.07-.048z" fill="#3A4374"/><ellipse fill="#C0C5DC" cx="82.455" cy="45.799" rx="3.53" ry="6.036"/><ellipse fill="#3A4374" cx="39.259" cy="45.799" rx="2.691" ry="4.882"/></g></svg>
                        <div class="md:max-w-[25.625rem]">
                            <h2 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-[#4761E6] leading-none mt-10">There is no feedback yet.</h2>
                            <p class="text-13 md:text-base text-[#647196] dark:text-[#D1D7E9] my-6 md:mt-4 md:mb-10">Got a suggestion? Found a bug that needs to be squashed? We love hearing about new ideas to improve our app.</p>
                        </div>
                        <div>
                            <a use:inertia href="/dashboard/task/create" class="bg-[#AD1FE9] hover:bg-[#C75AF6] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
                        </div>
                    </div>
                {:else}
                    {#each tasks.data as task (task.id)}
                        <div class="flex flex-col md:flex-row bg-white dark:bg-[#1E283A] p-6 md:py-7 md:px-8 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
                            <div class="hidden md:block pr-10">
                                <button type="button" use:inertia="{{ href: `/notification/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}" class="flex flex-row md:flex-col items-center md:justify-center space-x-2 md:space-x-0 bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#17202F] dark:hover:bg-[#3A4374] dark:hover:text-white rounded-[0.625rem] px-4 py-1 md:p-0 md:w-10 md:h-[3.3125rem] font-bold text-[#3A4374] dark:text-white">
                                    <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                                    <span class="mt-1">{task.likes}</span>
                                </button>
                            </div>
                            <a use:inertia href="/dashboard/tasks/{task.slug}" class="block flex-1 md:pr-[1.5625rem]">
                                <h2 class="font-bold text-[#3A4374] group-hover:text-[#4761E6] dark:text-white md:text-lg leading-3">{task.title}</h2>
                                <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>
                                <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#17202F] rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6]">{task.tag}</span>
                            </a>
                            <div class="flex items-end md:items-center justify-between">
                                <a use:inertia href="/dashboard/tasks/{task.slug}" class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                                    <svg width="18" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.62 16H1.346l.902-.91c.486-.491.79-1.13.872-1.823C1.036 11.887 0 9.89 0 7.794 0 3.928 3.52 0 9.03 0 14.87 0 18 3.615 18 7.455c0 3.866-3.164 7.478-8.97 7.478-1.017 0-2.078-.137-3.025-.388A4.705 4.705 0 012.62 16z" fill="#CDD2EE" fill-rule="nonzero"/></svg>
                                    <span class="dark:text-[#D1D7E9]">{task.comment_count}</span>
                                </a>
                            </div>
                        </div>
                    {:else}
                        <div class="flex flex-col items-center justify-center bg-white dark:bg-[#1E293B] py-[4.75rem] md:py-[6.9375rem] px-[1.625rem] text-center rounded-[0.625rem] cursor-default">
                            <svg width="102" height="108" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero" fill="none" opacity=".5"><path d="M48.73 15.593C25.822 15.59 7.246 34.224 7.235 57.22c-.01 22.997 18.55 41.649 41.458 41.665 22.909.016 41.494-18.61 41.516-41.606a41.72 41.72 0 00-12.132-29.473A41.4 41.4 0 0048.73 15.593z" stroke="#3A4374" stroke-width="1.045" stroke-linecap="round" stroke-linejoin="round"/><ellipse fill="#231F20" cx="70.476" cy="43.691" rx="1.917" ry="2.862"/><path d="M0 44.902l97.429-21.645-19.167-5.612S67.881.37 65.11.01C62.338-.351 11.979 10.43 11.979 10.43L8.785 34.77 0 44.902zM20.284 103.503L9.272 86.027l48.14-8.265 3.305 16.746 4.888-15.424 24.628 4.882-5.007 19.537z" fill="#3A4374"/><path d="M81.97 65.607l4.438-.617L88.7 81.618a2.115 2.115 0 01-1.799 2.387l-.261.036a2.11 2.11 0 01-2.377-1.806L81.97 65.607z" fill="#FFF"/><path d="M86.352 84.471A2.519 2.519 0 0183.87 82.3l-2.292-16.634a.4.4 0 01.343-.45l4.432-.617a.398.398 0 01.296.08c.085.063.14.16.151.265l2.276 16.626a2.508 2.508 0 01-.479 1.868 2.49 2.49 0 01-1.66.97l-.256.04a2.474 2.474 0 01-.328.024zm-3.929-18.518l2.236 16.234c.132.936.991 1.59 1.925 1.467h.263a1.714 1.714 0 001.454-1.932l-2.236-16.234-3.642.465z" fill="#3A4374"/><path fill="#FFF" d="M78.562 50.93l7.159-.995 2.242 16.263-7.16.995z"/><path d="M80.802 67.605a.43.43 0 01-.24-.08.361.361 0 01-.151-.265l-2.244-16.274a.394.394 0 01.072-.296.423.423 0 01.263-.153l7.187-.994a.39.39 0 01.448.345l2.244 16.266a.4.4 0 01-.344.448l-7.187.995-.048.008zm-1.789-16.33l2.132 15.472 6.39-.882-2.133-15.472-6.389.882z" fill="#3A4374"/><path d="M85.573 78.014l.725-.1a3.248 3.248 0 013.664 2.773l3.013 21.85a3.248 3.248 0 01-2.77 3.661l-.726.1a3.248 3.248 0 01-3.664-2.773l-3.013-21.85a3.248 3.248 0 012.77-3.66z" fill="#FFF"/><path d="M88.429 106.83a2.959 2.959 0 01-2.923-2.558L82.31 81.064a2.959 2.959 0 012.516-3.343l2.108-.288a2.949 2.949 0 013.322 2.525l3.195 23.248a2.964 2.964 0 01-2.516 3.335l-2.108.297-.4-.008zm-1.086-28.628a1.838 1.838 0 00-.296 0l-2.108.289a2.13 2.13 0 00-1.422.842c-.35.455-.5 1.034-.415 1.603l3.194 23.248a2.166 2.166 0 002.396 1.844l2.108-.297a2.163 2.163 0 001.837-2.405l-3.194-23.248a2.159 2.159 0 00-2.124-1.868l.024-.008z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="19.19" ry="19.264"/><path d="M81.553 65.953c-10.474-.005-19.093-8.275-19.569-18.778-.476-10.503 7.359-19.525 17.789-20.485 10.43-.96 19.768 6.482 21.202 16.897 1.47 10.746-5.992 20.662-16.691 22.182-.905.122-1.818.184-2.731.184zm0-38.536c-.868 0-1.736.059-2.596.177-7.532 1.045-13.696 6.542-15.616 13.927-1.92 7.386.78 15.206 6.844 19.812a18.74 18.74 0 0020.853 1.234c6.56-3.86 10.157-11.307 9.114-18.868-1.298-9.305-9.207-16.24-18.567-16.282h-.032z" fill="#3A4374"/><ellipse fill="#FFF" cx="81.569" cy="46.288" rx="15.589" ry="15.648"/><path d="M81.56 62.338c-8.606 0-15.665-6.846-15.962-15.48-.297-8.635 6.275-15.953 14.862-16.548 8.586-.596 16.098 5.745 16.987 14.339.889 8.593-5.165 16.348-13.69 17.536-.728.103-1.462.154-2.196.153zm0-31.265c-.707 0-1.414.049-2.116.144-8.096 1.127-13.848 8.489-13.009 16.65.839 8.162 7.967 14.19 16.122 13.634 8.154-.556 14.405-7.496 14.136-15.697-.268-8.2-6.959-14.713-15.132-14.731z" fill="#3A4374"/><path d="M99.896 89.714a11.645 11.645 0 00-3.913-3.206c-4.576-2.405-9.822-2.325-14.638-.802-1.709.545-5.023 1.323-5.199 3.6a2.115 2.115 0 001.526 2.004 6.254 6.254 0 002.675.104 6.887 6.887 0 00-3.618.914c-1.03.73-1.597 2.324-.75 3.294.374.404.861.683 1.397.802a6.365 6.365 0 003.554-.048c-1.251.24-2.47.625-3.634 1.146-.703.313-1.485.866-1.405 1.604.08.737.798 1.074 1.453 1.298 1.378.475 2.817.745 4.273.802a8.4 8.4 0 00-3.474 1.5c-1.598 1.346-1.598 3.903.567 4.633a6.366 6.366 0 002.14.248c6.389 0 14.04-.801 18.368-6.14a9.993 9.993 0 002.044-9.067 8.702 8.702 0 00-1.366-2.686z" fill="#FFF"/><path d="M80.802 107.984c-.75.037-1.5-.053-2.22-.265a2.762 2.762 0 01-1.9-2.108 3.502 3.502 0 011.197-3.207 5.952 5.952 0 011.725-1.002 14.816 14.816 0 01-2.396-.617c-1.062-.369-1.597-.914-1.717-1.603-.12-.69.495-1.523 1.598-2.044l.567-.24a4.211 4.211 0 01-.432-.105 3.008 3.008 0 01-1.597-.914 2.219 2.219 0 01-.471-1.732 3.209 3.209 0 011.294-2.14c.248-.175.516-.32.798-.433a2.461 2.461 0 01-1.525-2.3c.184-2.342 3.067-3.207 4.967-3.793l.511-.152c5.335-1.691 10.646-1.395 14.942.802a11.99 11.99 0 014.049 3.35 9.207 9.207 0 011.445 2.79 10.363 10.363 0 01-2.116 9.444c-4.528 5.555-12.37 6.277-18.695 6.285l-.024-.016zm-.759-11.055c-.887.226-1.752.53-2.587.906-.375.168-1.23.625-1.166 1.219.064.593.67.801 1.182.97 1.34.47 2.742.74 4.16.801a.392.392 0 01.384.353.4.4 0 01-.296.433 8.289 8.289 0 00-3.322 1.419 2.659 2.659 0 00-.934 2.453 1.953 1.953 0 001.373 1.499 5.73 5.73 0 002.005.224c6.157 0 13.768-.69 18.08-5.988a9.567 9.567 0 001.98-8.698 8.413 8.413 0 00-1.325-2.541 11.094 11.094 0 00-3.777-3.127c-4.113-2.124-9.2-2.405-14.335-.801l-.52.16c-1.692.513-4.264 1.29-4.408 3.086.031.753.54 1.4 1.262 1.604a3.86 3.86 0 001.43.176c.375-.04.742-.064 1.094-.088a.416.416 0 01.423.369.408.408 0 01-.36.433c-.359 0-.734.08-1.117.088a4.898 4.898 0 00-2.308.753c-.525.382-.874.96-.967 1.604-.063.395.041.799.288 1.114.325.334.742.563 1.198.657a6.11 6.11 0 002.396.16c.32-.08.646-.152.966-.216a.4.4 0 01.463.297.41.41 0 01-.271.48 6.097 6.097 0 01-.99.201z" fill="#3A4374"/><path d="M55.367 46.593s9.727 14.67 3.84 14.879c-5.885.208-6.388-1.339-6.388-1.339" fill="#FFF"/><path d="M58.154 61.937c-4.936 0-5.646-1.355-5.742-1.603a.425.425 0 01.263-.53.415.415 0 01.52.265s.718 1.243 5.997 1.05a1.348 1.348 0 001.294-.673c1.261-2.461-3.514-10.622-5.463-13.556a.418.418 0 01.128-.553.414.414 0 01.56.088c.742 1.122 7.186 11.063 5.51 14.43a2.139 2.139 0 01-1.997 1.13l-1.07-.048z" fill="#3A4374"/><ellipse fill="#C0C5DC" cx="82.455" cy="45.799" rx="3.53" ry="6.036"/><ellipse fill="#3A4374" cx="39.259" cy="45.799" rx="2.691" ry="4.882"/></g></svg>
                            <div class="md:max-w-[25.625rem]">
                                <h2 class="font-bold text-lg md:text-2xl text-[#3A4374] dark:text-[#4761E6] leading-none mt-10">There is no feedback yet.</h2>
                                <p class="text-13 md:text-base text-[#647196] dark:text-[#D1D7E9] my-6 md:mt-4 md:mb-10">Got a suggestion? Found a bug that needs to be squashed? We love hearing about new ideas to improve our app.</p>
                            </div>
                            <div>
                                <a use:inertia href="/dashboard/task/create" class="bg-[#AD1FE9] hover:bg-[#C75AF6] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">&#43; Add Feedback</a>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
            <!--end suggestions-->

            <!--pagination-->
            <div class="flex {tasks.prev_page_url ? 'justify-between' : 'justify-end'} my-6">
                {#if tasks.prev_page_url}
                    <a use:inertia href="{tasks.prev_page_url}" class="text-[#AD1FE9]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></a>
                {/if}

                {#if tasks.next_page_url}
                    <a use:inertia href="{tasks.next_page_url}" class="text-[#AD1FE9]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></a>
                {/if}
            </div>
        </main>
    </div>
</div>


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
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status }, replace: true, preserveScroll: true }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>All</button>
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby, status: filters.status, liked: true}, replace: true, preserveScroll: true }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateSortByDropdownSelection = event.target.innerText; showSortByDropdown = !showSortByDropdown; }}>Most Popular</button>
                            </div>
                        {/if}
                    </div>
                </div>

                <!--list of statuses-->
<!--                <div>-->
<!--                    <div class="relative mt-2">-->
<!--                        <label for class="font-bold">Statuses:</label>-->
<!--                        <button on:click={()=>{ showStatusDropdown = !showStatusDropdown }} type="button" class="relative w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"-->
<!--                                aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label">-->
<!--                            <span class="block truncate">{@html updateStatusDropdownSelection}</span>-->
<!--                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">-->
<!--                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd"/></svg>-->
<!--                        </span>-->
<!--                        </button>-->

<!--                        {#if showStatusDropdown}-->
<!--                            <div class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" tabindex="-1" role="listbox" aria-labelledby="listbox-label">-->
<!--                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>All</button>-->
<!--                                {#each statuses as status}-->
<!--                                    <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: status, tag: filters.tag, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="font-normal block truncate text-gray-900 relative select-none py-2 pl-3 pr-9 w-full text-left" on:click={(event)=>{ updateStatusDropdownSelection = event.target.innerText; showStatusDropdown = !showStatusDropdown; }}>{status}</button>-->
<!--                                {/each}-->
<!--                            </div>-->
<!--                        {/if}-->
<!--                    </div>-->
<!--                </div>-->

                <!--list of tags-->
                <div class="mt-4">
                    <div class="bg-gray-300 p-6 min-w-[15.9375rem] max-w-[15.9375rem] rounded-[0.625rem]">
                        <div class="flex flex-wrap justify-evenly gap-x-2 gap-y-3.5 text-13">
                            <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">All</button>

                            {#each tags as tag (tag.id)}
                                <button type="button" use:inertia="{{ href: route, method: 'get', data: { search: filters.search, status: filters.status, tag: tag.name, sortby: filters.sortby, liked: filters.liked }, replace: true, preserveScroll: true }}" class="block bg-white hover:bg-gray-200 rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] cursor-pointer">{tag.name}</button>
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
                           placeholder="Search by Title or Tag or User" class="border px-2 rounded-lg"/>
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
                                <div class="flex items-center justify-between">
                                    <a use:inertia href="/dashboard/tasks/{task.slug}">
                                        <h2 class="text-2xl font-bold pb-3">{task.title}</h2>
                                        <p>{task.created_at}</p>
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



{#if showSearch}
    <div
        class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24"
        id="headlessui-dialog-530"
        role="dialog"
        aria-modal="true"
        data-headlessui-state="open"
        on:keydown={() => {}}
        on:click|self={() => (showSearch = false)}
    >
        <div
            class="fixed inset-0 bg-slate-900/25 backdrop-blur transition-opacity opacity-100"
            on:keydown={() => {}}
            on:click|self={() => (showSearch = false)}
        />
        <div
            class="relative w-full max-w-lg transform px-4 transition-all opacity-100 scale-100"
            on:keydown={() => {}}
            on:click|self={() => (showSearch = false)}
        >
            <div
                class="overflow-hidden rounded-lg bg-white shadow-md"
                id="headlessui-dialog-panel-531"
                data-headlessui-state="open"
            >
                <form on:submit|preventDefault class="relative flex items-center justify-between shadow-sm">
                    <input
                        class="block w-full appearance-none bg-transparent py-4 pl-4 pr-12 text-base text-slate-900 outline-none border-0 border-none placeholder:text-slate-600 focus:outline-none sm:text-sm sm:leading-6 focus-visible"
                        placeholder="Find anything..."
                        aria-label="Search components"
                        id="headlessui-combobox-input-532"
                        role="combobox"
                        type="text"
                        aria-expanded="true"
                        aria-autocomplete="list"
                        data-headlessui-state="open"
                        bind:value={searchInput}
                        tabindex="0"
                        style="caret-color: rgb(107, 114, 128);"
                        data-focus-visible-added=""
                        aria-controls="headlessui-combobox-options-533"
                        autofocus
                    />
                    <button type="submit"
                    ><svg
                        class="cursor-pointer absolute right-4 top-4 h-6 w-6 fill-slate-400"
                        xmlns="http://www.w3.org/2000/svg"
                    ><path
                        d="M20.47 21.53a.75.75 0 1 0 1.06-1.06l-1.06 1.06Zm-9.97-4.28a6.75 6.75 0 0 1-6.75-6.75h-1.5a8.25 8.25 0 0 0 8.25 8.25v-1.5ZM3.75 10.5a6.75 6.75 0 0 1 6.75-6.75v-1.5a8.25 8.25 0 0 0-8.25 8.25h1.5Zm6.75-6.75a6.75 6.75 0 0 1 6.75 6.75h1.5a8.25 8.25 0 0 0-8.25-8.25v1.5Zm11.03 16.72-5.196-5.197-1.061 1.06 5.197 5.197 1.06-1.06Zm-4.28-9.97c0 1.864-.755 3.55-1.977 4.773l1.06 1.06A8.226 8.226 0 0 0 18.75 10.5h-1.5Zm-1.977 4.773A6.727 6.727 0 0 1 10.5 17.25v1.5a8.226 8.226 0 0 0 5.834-2.416l-1.061-1.061Z"
                    /></svg
                    ></button
                    >
                </form>
            </div>
        </div>
    </div>
{/if}

