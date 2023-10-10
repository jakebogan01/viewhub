<script>
    import BreezeApplicationLogo from "@/Components/ApplicationLogo.svelte";
    import BreezeDropdown from "@/Components/Dropdown.svelte";
    import BreezeDropdownLink from "@/Components/DropdownLink.svelte";
    import BreezeNavLink from "@/Components/NavLink.svelte";
    import BreezeResponsiveNavLink from "@/Components/ResponsiveNavLink.svelte";
    import { inertia, page, Link } from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let auth;
</script>

<div class="py-6">
    <div class="font-jost font-normal max-w-[1100px] mx-auto {auth.user.dark_mode ? 'dark' : ''}">
        <!--start page content-->
        <main>
            <slot/>
        </main>
        <!--end page content-->
    </div>
</div>
