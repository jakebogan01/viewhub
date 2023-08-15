<script>
    import {inertia, page} from "@inertiajs/inertia-svelte";
    import NavLink from "./NavLink.svelte";
</script>

<nav>
    <div class="flex items-center">
        <ul class="flex space-x-6 mr-4">
            <li>
                <NavLink url="/" active={'Home'}>Home</NavLink>
            </li>
            <li>
                <NavLink url="/users" active={'Users'}>Users</NavLink>
            </li>
            <li>
                <NavLink url="/settings" active={'Settings'}>Settings</NavLink>
            </li>
            <li>
                <a use:inertia="{{ href: '/test', method: 'post', data: { foo: 'fee'} }}"
                   class="text-black hover:underline" href="/test">Test</a>
            </li>
            <li>
                Welcome back, {$page.props.auth.user.name}
            </li>
            <li>
                <button use:inertia="{{ href: '/logout', method: 'post' }}" class="text-black hover:underline" type="submit">Logout</button>
            </li>
        </ul>
    </div>
</nav>
