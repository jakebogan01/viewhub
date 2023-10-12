<script>
    /* svelte-ignore unused-export-let */
    export let auth;
</script>

<div class="{auth.user.dark_mode ? 'dark' : ''} h-vh">
    <div class="sm:py-6 dark:bg-black h-vh">
        <div class="font-jost font-normal max-w-[1100px] mx-auto">
            <!--start page content-->
            <main>
                <slot/>
            </main>
            <!--end page content-->
        </div>
    </div>
</div>

<style global>
    :global(body) {
        background-color: #f2f2f2;
    }
</style>
