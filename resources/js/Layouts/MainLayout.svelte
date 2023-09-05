<script>
    import { inertia } from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let auth;
</script>

<main class="max-w-4xl m-auto h-screen p-14 {auth.user.dark_mode ? 'dark' : ''}">
    <header>
        <a use:inertia href="/">Home</a>
        <a use:inertia href="/about">About</a>
    </header>
    <article>
        <slot />
    </article>
</main>
