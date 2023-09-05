<script>
    import Nav from "../Components/Navigation/Nav.svelte";
</script>

<div>
    <section class="p-6 bg-gray-200">
        <header class="flex justify-between max-w-6xl mx-auto">
            <h1 class="text-lg font-bold">My App</h1>
            <Nav/>
        </header>
    </section>

    <section class="p-6">
        <div class="max-w-3xl mx-auto">
            <slot/>
        </div>
        <input type="text">
    </section>
</div>
