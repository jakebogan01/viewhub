<script>
    import BreezeButton from "@/Components/Button.svelte";
    import BreezeGuestLayout from "@/Layouts/Guest.svelte";
    import BreezeInput from "@/Components/Input.svelte";
    import BreezeLabel from "@/Components/Label.svelte";
    import BreezeValidationErrors from "@/Components/ValidationErrors.svelte";
    import { inertia, useForm } from "@inertiajs/inertia-svelte";
    export let errors = {};

    let form = useForm({
        name: null,
        team_name: null,
        is_solo: false,
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
        $form.post("/register", {
            onSuccess: () => $form.reset("password", "password_confirmation"),
        });
    };

    let showSoloForm, showTeamForm = false;
</script>

<svelte:head>
    <title>Register</title>
</svelte:head>

{#if !showSoloForm && !showTeamForm}
    <div>
        <button type="button" on:click={()=>{showSoloForm = true; $form.is_solo = true}}>Solo</button> or <button type="button" on:click={()=>{showTeamForm = true}}>Team</button>
    </div>
{/if}

{#if showSoloForm || showTeamForm}
    <BreezeGuestLayout>
        <BreezeValidationErrors class="mb-4" errors={err} />

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

            {#if showTeamForm}
                <div class="mt-4">
                    <BreezeLabel for="team_name" value="Team Name" />
                    <BreezeInput
                        id="team_name"
                        type="text"
                        class="mt-1 block w-full"
                        value={form.team_name}
                        required
                        autocomplete="name"
                        on:input={(evt) => ($form.team_name = evt.detail)}
                    />
                </div>
            {/if}

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
{/if}
