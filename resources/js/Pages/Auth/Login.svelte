<script>
    import BreezeButton from "@/Components/Button.svelte";
    import BreezeInput from "@/Components/Input.svelte";
    import BreezeLabel from "@/Components/Label.svelte";
    import { Link, useForm } from "@inertiajs/inertia-svelte";

    let err = {};
    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let canResetPassword;
    /* svelte-ignore unused-export-let */
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
    <title>Login / Viewhub</title>
</svelte:head>

<div class="registration min-h-full flex bg-[#111633]">
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                <div>
                    <header class="absolute top-16">
                        <div class="w-20 md:w-24 cursor-default">
                            <span class="sr-only">Company Logo</span>
                            <img class="w-20 md:w-24" src="/logo/logo.png" alt="logo">
                        </div>
                    </header>
                </div>
            </div>

            <section class="mt-8">
                <div class="mt-6">
                    <div class="pb-2">
                        <h2 data-aos="fade-up" data-aos-delay="250" class="mt-16 md:mt-6 text-3xl font-extrabold text-white cursor-default">
                            Login
                        </h2>
                    </div>
                    <form on:submit|preventDefault={onSubmit}>
                        <div>
                            <BreezeLabel for="email" value="Email" />
                            <BreezeInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                value={form.email}
                                required
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

                        <div class="flex items-center justify-end mt-4">
                            <Link href="/register" class="text-sm text-white hover:text-gray-200">
                                Register
                            </Link>
                            <BreezeButton class="ml-4" sclass:opacity-25={form.processing} disabled={form.processing}>
                                Log in
                            </BreezeButton>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div class="relativ hidden lg:block relative w-0 flex-1">
        <div class="absolute inset-0 h-full w-[200px] z-50 bg-gradient-to-r from-[#111633] to-transparent"></div>
        <img src="/logo/page.jpg" class="absolute inset-0 h-full w-full object-cover" loading="eager" alt="" role="presentation">
    </div>
</div>

<style>
    :global(body) {
        height: 100dvh;
    }
    :global(html) {
        height: 100dvh;
    }
    :global(#app) {
        height: 100dvh;
    }
</style>
