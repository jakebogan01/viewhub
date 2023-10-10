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

<div class="py-6">
    <!--start notifications-->
    <div class="fixed right-5 top-5 z-50 {auth.user.unreadNotifications.length > 0 ? 'text-red-500' : 'text-black'}">
        <button type="button" on:click={()=>{showNotifications = !showNotifications}} aria-label="notifications" class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="{auth.user.unreadNotifications.length > 0 ? 'currentColor' : 'none'}" viewBox="0 0 24 24" stroke-width="1.5" stroke="{auth.user.unreadNotifications.length > 0 ? 'currentColor' : '#b8c2f5'}" class="w-6 h-6"><path fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" clip-rule="evenodd"/></svg>
        </button>

        {#if showNotifications}
            <div class="absolute right-0 w-[350px] mt-4 space-y-2" role="menu" aria-orientation="vertical"
                 aria-labelledby="menu-button" tabindex="-1">
                {#each auth.user.unreadNotifications.reverse() as notification}
                    <div
                        class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    {#if notification.type === 'App\\Notifications\\CommentReceived'}
                                        <p class="text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span>commented on your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.comment_id}" class="text-blue-500 font-bold">task</a></p>
                                    {:else if notification.type === 'App\\Notifications\\CommentReplyReceived'}
                                        <p class="text-sm text-gray-500"><span class="text-purple-500 font-bold">{notification.data.user}</span>replied to your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}/#{notification.data.reply_id}" class="text-blue-500 font-bold">comment</a></p>
                                    {:else}
                                        <p class="text-sm text-gray-500">Your <a use:inertia href="/dashboard/tasks/{notification.data.task_slug}" class="text-blue-500 font-bold">task</a>was liked by <span class="text-purple-500 font-bold">{notification.data.user}</span></p>
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
    </div>
    <!--end notifications-->

    <div class="font-jost font-normal max-w-[1100px] mx-auto {auth.user.dark_mode ? 'dark' : ''}">
        <!--start page content-->
        <main>
            <slot/>
        </main>
        <!--end page content-->
    </div>
</div>
