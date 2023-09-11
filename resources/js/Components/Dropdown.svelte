<script setup>
    import { inertia, page, Link } from "@inertiajs/inertia-svelte";
    import { onDestroy, onMount } from "svelte";

    let className;
    let open = false;

    export { className as class };
    export let contentClasses = "py-1 bg-white";

    function closeOnEscape(e) {
        if (open.value && e.key === "Escape") {
            open.value = false;
        }
    }

    onMount(() => document.addEventListener("keydown", closeOnEscape));
    onDestroy(() => document.removeEventListener("keydown", closeOnEscape));
</script>

<div class="relative">
    <div on:click={(evt) => (open = !open)}>
        <slot name="trigger" />
    </div>

    <!-- Full Screen Dropdown Overlay -->
    {#if open}
        <div class="fixed inset-0 z-40" on:click={(evt) => (open = false)} />
        <div
            class="absolute z-50 mt-2 rounded-md shadow-lg {className}"
            on:click={(evt) => (open = false)}
        >
            <div
                class="rounded-md ring-1 ring-black ring-opacity-5 {contentClasses}"
            >
                <slot name="content" />

                <button
                    use:inertia={{ href: '/dashboard/settings', method: 'get' }}
                    type="button"
                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    Settings
                </button>
            </div>
        </div>
    {/if}
</div>
