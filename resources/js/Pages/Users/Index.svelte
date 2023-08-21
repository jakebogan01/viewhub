<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import Pagination from "../../Components/Pagination.svelte";
    import {Inertia} from "@inertiajs/inertia";

    export let time;
    export let users;
    export let filters;
    // export let can;

    let search = filters.search;

    let timer;

    const debounce = v => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            Inertia.get('/users', {search: v}, {
                preserveState: true,
                replace: true
            });
        }, 300);
    }
</script>

<svelte:head>
    <title>Users</title>
</svelte:head>

<div class="flex justify-between mb-6">
    <div class="flex items-center">
        <h1 class="text-3xl">Users</h1>

        <!--{#if can.createUser}-->
        <!--    <a use:inertia={{ replace: true }} href="/users/create" class="text-blue-500 text-sm ml-3">New User</a>-->
        <!--{/if}-->
    </div>

    <input on:keyup={({ target: { value } }) => debounce(value)} value={search} type="text" placeholder="Search..." class="border px-2 rounded-lg"/>
</div>

<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200">
                    {#each users.data as user (user.id)}
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            { user.name }
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!--{#if user.can.edit}-->
                            <!--    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">-->
                            <!--        <a use:inertia href="/users/${user.id}/edit"-->
                            <!--           class="text-indigo-600 hover:text-indigo-900">Edit</a>-->
                            <!--    </td>-->
                            <!--{/if}-->
                        </tr>
                    {/each}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<Pagination links={users.links} class="mt-6"/>

<div class="mt-[1500px]">
    The current time is { time }.

    <a href="/users" class="text-blue-500" use:inertia="{{ preserveScroll: true }}">Refresh</a>
</div>
