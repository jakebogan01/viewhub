<script>
    import BreezeButton from "@/Components/Button.svelte";
    import BreezeGuestLayout from "@/Layouts/Guest.svelte";
    import BreezeInput from "@/Components/Input.svelte";
    import BreezeLabel from "@/Components/Label.svelte";
    import BreezeValidationErrors from "@/Components/ValidationErrors.svelte";
    import { inertia, useForm } from "@inertiajs/inertia-svelte";
    export let company;
    export let errors = {};

    let form = useForm({
        company_id: company.id,
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || null,
    });

    let err = {};

    $: {
        err = errors;
    }

    const submit = () => {
        $form.post("/register/new-company", {
            onSuccess: () => $form.reset("password", "password_confirmation"),
        });
    };
</script>

<svelte:head>
    <title>Client Register</title>
</svelte:head>


<BreezeGuestLayout>
    <BreezeValidationErrors class="mb-4" errors={err} />

    <h1 class="text-center uppercase font-bold">{company.name}</h1>

    <form on:submit|preventDefault={submit}>
        <div>
            <BreezeLabel for="name" value="Name" />
            <BreezeInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                value={form.name}
                required
                autofocus
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
                href="/login/company/{company.slug}"
                use:inertia
                class="underline text-sm text-gray-600 hover:text-gray-900"
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
</BreezeGuestLayout>
