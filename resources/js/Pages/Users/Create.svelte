<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {Inertia} from '@inertiajs/inertia';
    import {inertia, page} from "@inertiajs/inertia-svelte";

    let form;
    export let errors;

    $: form = {
        name: '',
        email: '',
        password: '',
    };

    let handleSubmit = () => {
        Inertia.post('/users', form)
    }
</script>

<svelte:head>
    <title>Create User</title>
</svelte:head>

<h1 class="text-3xl">Create New User</h1>

<form on:submit|preventDefault|once={handleSubmit} class="max-w-md mx-auto mt-8">
    <div class="mb-6">
        <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="name"> Name </label>

        <input bind:value={form.name} class="border border-gray-400 p-2 w-full" type="text" name="name" id="name"/>
        {#if errors.name}
            <p class="text-red-500 text-xs mt-1"> {errors.name} </p>
        {/if}
    </div>

    <div class="mb-6">
        <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="email"> Email </label>

        <input bind:value={form.email} class="border border-gray-400 p-2 w-full" type="email" name="email" id="email"/>
        {#if errors.email}
            <p class="text-red-500 text-xs mt-1"> {errors.email} </p>
        {/if}
    </div>

    <div class="mb-6">
        <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="password"> Password </label>

        <input bind:value={form.password} class="border border-gray-400 p-2 w-full" type="password" name="password"
               id="password"/>
        {#if errors.password}
            <p class="text-red-500 text-xs mt-1"> {errors.password} </p>
        {/if}
    </div>

    <div class="mb-6">
        <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500">Submit</button>
    </div>
</form>
