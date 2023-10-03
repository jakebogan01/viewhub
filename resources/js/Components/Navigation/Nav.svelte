<script>
    import {inertia, Link, page} from "@inertiajs/inertia-svelte";
    import NavLink from "../../Components/Navigation/NavLink.svelte";
</script>

<nav>
    <div class="flex items-center">
        <ul class="flex space-x-6 mr-4">
            <li>
                <NavLink url="/" active={'Home'}>Home</NavLink>
            </li>
            <li>
                <a href="/dashboard" use:inertia class="text-black hover:underline">dashboard</a>
            </li>
        </ul>
    </div>
</nav>
