<script>
    import {Inertia} from '@inertiajs/inertia';
    import {inertia, page} from "@inertiajs/inertia-svelte";

    let form;
    export let errors;

    $: form = {
        email: '',
        password: '',
    };

    let handleSubmit = () => {
        Inertia.post('/login', form)
    }
</script>

<svelte:head>
    <title>Login</title>
</svelte:head>

<main class="grid place-items-center min-h-screen">
    <section class="bg-white p-8 rounded-xl max-w-md mx-auto border w-full">
        <h1 class="text-3xl">Login</h1>

        <form on:submit|preventDefault|once={handleSubmit} class="max-w-md mx-auto mt-8">
            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="email"> Email </label>

                <input bind:value={form.email} class="border border-gray-400 p-2 w-full" type="email" name="email"
                       id="email"/>
                {#if errors.email}
                    <p class="text-red-500 text-xs mt-1"> {errors.email} </p>
                {/if}
            </div>

            <div class="mb-6">
                <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="password"> Password </label>

                <input bind:value={form.password} class="border border-gray-400 p-2 w-full" type="password"
                       name="password"
                       id="password"/>
                {#if errors.password}
                    <p class="text-red-500 text-xs mt-1"> {errors.password} </p>
                {/if}
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500">Login</button>
            </div>
        </form>
    </section>
</main>
