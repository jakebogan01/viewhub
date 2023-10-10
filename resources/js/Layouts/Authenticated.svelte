<script>
    import BreezeApplicationLogo from "@/Components/ApplicationLogo.svelte";
    import BreezeDropdown from "@/Components/Dropdown.svelte";
    import BreezeDropdownLink from "@/Components/DropdownLink.svelte";
    import BreezeNavLink from "@/Components/NavLink.svelte";
    import BreezeResponsiveNavLink from "@/Components/ResponsiveNavLink.svelte";
    import { inertia, page, Link } from "@inertiajs/inertia-svelte";
    let showingNavigationDropdown = false;

    /* svelte-ignore unused-export-let */
    export let auth;
    let showNotifications = false;
</script>

<div class="font-jost font-normal max-w-[1100px] mx-auto">
    <div class="min-h-screen bg-gray-100 {auth.user.dark_mode ? 'dark' : ''}">
        <nav class="bg-white border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link href="/dashboard">
                                <BreezeApplicationLogo class="block h-9 w-auto"/>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <BreezeNavLink
                                href="/dashboard"
                                active={$page.component === "Dashboard"}>
                                Dashboard
                            </BreezeNavLink>
                            <BreezeNavLink
                                href="/about"
                                active={$page.component === "About"}>
                                About
                            </BreezeNavLink>
                            <BreezeNavLink
                                href="/dashboard/all-tasks"
                                active={$page.component === "AllTasks"}>
                                All Tasks
                            </BreezeNavLink>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <BreezeDropdown class="w-48 origin-top-right right-0">
                                <span
                                    class="inline-flex rounded-md"
                                    slot="trigger">
                                    <button
                                        type="button"
                                        class="inline-flex items-center lowercase px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        {auth.user.username}
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                </span>

                                <BreezeDropdownLink
                                    slot="content"
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    type="button">
                                    Log Out
                                </BreezeDropdownLink>
                            </BreezeDropdown>
                        </div>

                        <div class="relative ml-6 {auth.user.unreadNotifications.length > 0 ? 'text-red-500' : 'text-black'}">
                            <button type="button" on:click={()=>{showNotifications = !showNotifications}} aria-label="notifications" class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="{auth.user.unreadNotifications.length > 0 ? 'currentColor' : 'none'}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" clip-rule="evenodd" /></svg>
                            </button>

                            {#if showNotifications}
                                <div class="absolute right-0  w-[350px] space-y-2 z-50" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                    {#each auth.user.unreadNotifications.reverse() as notification}
                                        <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                            <div class="p-4">
                                                <div class="flex items-start">
                                                    <div class="ml-3 w-0 flex-1 pt-0.5">
                                                        {#if notification.type === 'App\\Notifications\\CommentReceived'}
                                                            <p class="mt-1 text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span> commented on your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.comment_id}" class="text-blue-500 font-bold">task</a></p>
                                                        {:else if notification.type === 'App\\Notifications\\CommentReplyReceived'}
                                                            <p class="mt-1 text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span> replied to your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.reply_id}" class="text-blue-500 font-bold">comment</a></p>
                                                        {:else}
                                                            <p class="mt-1 text-sm text-gray-500">Your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}" class="text-blue-500 font-bold">task</a> was liked by <span class="text-purple-500 font-bold">{notification.data.user}</span></p>
                                                        {/if}
                                                    </div>
                                                    <div class="ml-4 flex flex-shrink-0">
                                                        <button type="button" use:inertia="{{ href: '/notification-mark-read', method: 'post', data: { id: notification.id }, replace: true, }}" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                            <span class="sr-only">Close</span>
                                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            {/if}
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            on:click={(evt) =>
                                (showingNavigationDropdown =
                                    !showingNavigationDropdown)}
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class:hidden={showingNavigationDropdown} class:inline-flex={!showingNavigationDropdown} stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path class:hidden={!showingNavigationDropdown} class:inline-flex={showingNavigationDropdown} stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                class:block={showingNavigationDropdown}
                class:hidden={!showingNavigationDropdown}
                class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <BreezeResponsiveNavLink
                        href="/dashboard"
                        active={$page.component === "Dashboard"}>
                        Dashboard
                    </BreezeResponsiveNavLink>
                    <BreezeResponsiveNavLink
                        href="/about"
                        active={$page.component === "About"}>
                        About
                    </BreezeResponsiveNavLink>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">
                            {auth.user.name}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {auth.user.email}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <BreezeResponsiveNavLink
                            href="/logout"
                            method="post"
                            as="button">
                            Log Out
                        </BreezeResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</div>
