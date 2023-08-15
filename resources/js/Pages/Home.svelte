<script context="module">
    export {default as layout} from "../Layouts/Layout.svelte";
</script>

<script>
    export let frameworks;
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

<h1 class="text-3xl">Home</h1>

<p>The following are all the technologies used in this series</p>

<ul>
    {#each frameworks as framework}
        <li>{framework}</li>
    {/each}
</ul>
