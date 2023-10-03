<script context="module">
    export {default as layout} from "../Layouts/Layout.svelte";
</script>

<script>
    import {inertia} from "@inertiajs/inertia-svelte";
    import {Inertia} from "@inertiajs/inertia";
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                Home page. Freelanceers join here
                <a use:inertia href="/register" class="bg-blue-500 p-3">Try now</a>
            </div>
        </div>
    </div>
</div>
