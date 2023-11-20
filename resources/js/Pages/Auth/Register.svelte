<script>
    import BreezeButton from "@/Components/Button.svelte";
    import BreezeGuestLayout from "@/Layouts/Guest.svelte";
    import BreezeInput from "@/Components/Input.svelte";
    import BreezeLabel from "@/Components/Label.svelte";
    import BreezeValidationErrors from "@/Components/ValidationErrors.svelte";
    import {inertia, Link, useForm} from "@inertiajs/inertia-svelte";
    import BreezeCheckbox from "@/Components/Checkbox.svelte";

    let form = useForm({
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || null,
        terms: false,
    });

    const submit = () => {
        $form.post("/register", {
            onSuccess: () => $form.reset("password", "password_confirmation"),
        });
    };
</script>

<svelte:head>
    <title>Register</title>
</svelte:head>

<div class="registration min-h-full flex bg-[#111633]">
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                    <div>
                        <header class="absolute top-16">
                            <div class="w-20 md:w-24 cursor-default">
                                <span class="sr-only">Company Logo</span>
                                <img class="w-20 md:w-24" src="/logo/logo.svg" alt="logo">
                            </div>
                        </header>
                    </div>
                </div>

                <section class="mt-8">
                    <div class="mt-6">
                        <div class="pb-2">
                            <h2 data-aos="fade-up" data-aos-delay="250" class="mt-16 md:mt-6 text-3xl font-extrabold text-white cursor-default">
                                Create an account
                            </h2>
                        </div>
                        <form on:submit|preventDefault={submit}>
                            <div>
                                <BreezeLabel for="name" value="Username" />
                                <BreezeInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value={form.name}
                                    required
                                    autocomplete="name"
                                    on:input={(evt) => ($form.name = evt.detail)}
                                />
                            </div>

                            <div class="mt-4">
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
                                    autocomplete="new-password"
                                    on:input={(evt) => ($form.password = evt.detail)}
                                />
                            </div>

                            <div class="mt-4">
                                <BreezeLabel for="password_confirmation" value="Confirm Password" />
                                <BreezeInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    value={form.password_confirmation}
                                    required
                                    autocomplete="new-password"
                                    on:input={(evt) => ($form.password_confirmation = evt.detail)}
                                />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a
                                    href="/login"
                                    use:inertia
                                    class="text-sm text-white hover:text-gray-200"
                                >
                                    Already registered?
                                </a>

                                <BreezeButton
                                    class="ml-4"
                                    xclass:opacity-25={form.processing}
                                    disabled={form.processing}
                                >
                                    Register
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
        height: 100vh;
    }
    :global(html) {
        height: 100vh;
    }
    :global(#app) {
        height: 100vh;
    }
</style>
