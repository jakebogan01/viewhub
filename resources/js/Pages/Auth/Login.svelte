<script>
    import BreezeButton from "@/Components/Button.svelte";
    import BreezeCheckbox from "@/Components/Checkbox.svelte";
    import BreezeGuestLayout from "@/Layouts/Guest.svelte";
    import BreezeInput from "@/Components/Input.svelte";
    import BreezeLabel from "@/Components/Label.svelte";
    import BreezeValidationErrors from "@/Components/ValidationErrors.svelte";
    import { Link, useForm } from "@inertiajs/inertia-svelte";
    let err = {};
    export let errors = {};
    export let canResetPassword;
    export let status;

    console.log(Intl.DateTimeFormat().resolvedOptions().timeZone)

    const form = useForm({
        email: null,
        password: null,
        remember: false,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || null,
    });

    $: {
        err = errors;
    }

    const onSubmit = () => {
        $form.post("/login", {
            onSuccess: () => $form.reset(),
        });
    };
</script>

<svelte:head>
    <title>Log in</title>
</svelte:head>

<div class="grid min-h-full grid-cols-1 grid-rows-[1fr,auto,1fr] bg-black lg:grid-cols-[max(50%,36rem),1fr]">
    <header class="flex justify-center lg:block lg:justify-normal mx-auto w-full max-w-[1600px] px-6 pt-6 sm:pt-10 lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:px-8">
        <a href="#">
            <span class="sr-only">PNY Technology</span>
            <img class="w-20 md:w-24" src="/logo/logo.png" alt="">
        </a>
    </header>
    <section class="flex justify-center lg:block lg:justify-normal mx-auto w-full max-w-[1600px] px-6 py-24 sm:py-32 lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:px-8">
        <div class="max-w-lg w-full">
            <form on:submit|preventDefault={onSubmit}>
                <div>
                    <BreezeLabel for="email" value="Email" />
                    <BreezeInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        value={form.email}
                        required
                        autofocus
                        autocomplete="username"
                        on:input={(evt) => ($form.email = evt.detail)}
                    />
                </div>

                <div class="mt-4">
                    <BreezeLabel for="password" value="Password" />
                    <BreezeInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        value={form.password}
                        required
                        autocomplete="current-password"
                        on:input={(evt) => ($form.password = evt.detail)}
                    />
                </div>

                <div class="block mt-4">
                    <!-- svelte-ignore a11y-label-has-associated-control -->
                    <label class="flex items-center">
                        <BreezeCheckbox name="remember" bind:checked={form.remember} />
                        <span class="ml-2 text-sm text-white">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    {#if canResetPassword}
                        <Link
                            href="/password/reset"
                            class="underline text-sm text-white hover:text-gray-200"
                        >
                            Forgot your password?
                        </Link>
                    {/if}

                    <Link
                        href="/register"
                        class="underline text-sm text-white ml-3 hover:text-gray-200"
                    >
                        Register
                    </Link>

                    <BreezeButton
                        class="ml-4"
                        sclass:opacity-25={form.processing}
                        disabled={form.processing}
                    >
                        Log in
                    </BreezeButton>
                </div>
            </form>
        </div>
    </section>
    <footer class="self-end lg:col-span-2 lg:col-start-1 lg:row-start-3">
        <div class="border-t border-gray-600 bg-[#222222] py-10">
            <nav class="mx-auto flex w-full max-w-7xl items-center gap-x-4 px-6 text-sm leading-7 text-white lg:px-8">
                <a href="https://www.pny.com/">Our Site</a>
                <a href="https://www.facebook.com/PNYTechnologies?utm_source=homepage&utm_medium=facebook&utm_campaign=pny_consumer">Facebook</a>
                <a href="https://www.linkedin.com/company/pny-technologies?utm_source=homepage&utm_medium=linkedin&utm_campaign=pny_consumer">Linkedin</a>
                <a href="https://twitter.com/PNYTechnologies?utm_source=homepage&utm_medium=twitter&utm_campaign=pny_consumer">Twitter</a>
            </nav>
        </div>
    </footer>
    <div class="hidden lg:relative lg:col-start-2 lg:row-start-1 lg:row-end-4 lg:block">
        <iframe src='https://my.spline.design/clonercubesgenerativecopy-ba5d70ec6046fddc4566a0a52369aa2b/' frameborder='0' width='100%' height='100%'></iframe>
    </div>
</div>

<style>
    :global(body) {
        height: 100vh;
    }
    :global(html) {
        height: 100vh;
    }
    :global(#app) {
        height: 100vh;
    }
</style>
