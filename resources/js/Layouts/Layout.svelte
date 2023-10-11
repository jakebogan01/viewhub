<script>
    /* svelte-ignore unused-export-let */
    export let auth;
</script>

<div class="{auth.user.dark_mode ? 'dark' : ''}">
    <div class="py-6 dark:bg-[#444444]">
        <div class="font-jost font-normal max-w-[730px] mx-auto">
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
