<script context="module">
    export {default as layout} from "../../Layouts/Layout.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    export let task;
    export let is_admin;
    export let client_d;
    let replyNow = false;
    let replyToComment = false;
    let comment_id, user_id, replyTo;
    $: comment_id = null;
    $: user_id = null;
    $: replyTo = '';

    console.log(auth)
    let form = useForm({
        task_id: task.id,
        body: '',
    });

    let replyForm = useForm({
        comment_id: null,
        recipient_id: null,
        body: '',
    });

    let submitReplyForm = useForm({
        comment_id: null,
        recipient_id: null,
        body: '',
    });

    function submit() {
        $form.post(`/dashboard/comment/create`, {
            replace: true,
            preserveScroll: true
        })

        $form.body = '';
    }

    function submitReply() {
        if ($replyForm.body === '') {
            return;
        }

        $replyForm.comment_id = comment_id;
        $replyForm.recipient_id = user_id;
        $replyForm.post(`/dashboard/comment/reply/create`, {
            replace: true,
            preserveScroll: true,
        })

        replyNow = false;
        $replyForm.body = '';
    }

    function submitCommentReply() {
        if ($submitReplyForm.body === '') {
            return;
        }

        $submitReplyForm.comment_id = comment_id;
        $submitReplyForm.recipient_id = user_id;
        $submitReplyForm.post(`/dashboard/comment/comment-reply/create`, {
            replace: true,
            preserveScroll: true,
        })

        replyNow = false;
        $submitReplyForm.body = '';
    }

    let viewImage = false;
    let imageSrc = '';

    const inspectImage = (image) => {
        viewImage = true;
        imageSrc = image;
    }
</script>

<svelte:head>
    <title>Task</title>
</svelte:head>

{#if flash.message}
    <div class="text-center text-blue-500 dark:text-blue-400 font-bold pb-4">{flash.message}</div>
{/if}

<section class="px-4 md:px-0">
    <div class="flex justify-between mb-6">
        <a use:inertia={{ replace: true }} href="/dashboard" class="flex items-center space-x-2">
            {#if auth.user.dark_mode}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {:else}
                <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            {/if}
            <span class="font-bold text-13 md:text-sm text-[#647196] dark:text-white">Go Home</span>
        </a>

        {#if task.user_id === client_d || is_admin}
            <div class="space-x-2">
                <button type="button" use:inertia="{{ href: `/dashboard/task/${task.id}`, method: 'delete', replace: true, }}" class="bg-red-500 hover:bg-red-400 font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">Delete</button>
                <button type="button" use:inertia="{{ href: `/dashboard/task/${task.slug}/edit`, method: 'get' }}" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">Edit Feedback</button>
            </div>
        {/if}
    </div>

    <div class="flex flex-col md:flex-row bg-white dark:bg-[#222222] hover:shadow-lg p-6 md:py-7 md:px-8 mt-12 md:mt-16 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
        <div class="hidden md:block pr-10">
            <button type="button" use:inertia="{{ href: `/notification/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}" class="flex flex-row md:flex-col items-center md:justify-center space-x-2 md:space-x-0 bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#444444] dark:hover:bg-[#238AB6] text-[#3A4374] dark:text-white rounded-[0.625rem] px-4 py-1 md:p-0 md:w-10 md:h-[3.3125rem] font-bold">
                {#if auth.user.dark_mode}
                    <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#ffffff" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                {:else}
                    <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                {/if}
                <span class="mt-1">{task.likes}</span>
            </button>
        </div>
        <div class="flex-1 md:pr-[1.5625rem]">
            <h2 class="font-bold text-[#3A4374] dark:text-white md:text-lg leading-3">{task.title}</h2>
            <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>

            {#if task.images.length > 0}
                <ul role="list" class="mx-auto my-10 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none">
                    {#each task.images as image}
                        <li on:click={()=>{inspectImage(image)}} on:keydown>
                            <img src="/images/user{task.user_id}/{image.path}" class="aspect-[3/2] w-full rounded-md object-cover" alt={image.name}>
                        </li>
                    {/each}
                </ul>
            {/if}

            <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#444444] dark:text-white rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] text-base cursor-default">{task.tag}</span>
        </div>
    </div>

    {#if task.comments.data.length > 0}
        <div class="bg-white dark:bg-[#222222] hover:shadow-lg px-8 py-6 rounded-[0.625rem] text-13 space-y-10 my-6">
            <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6 md:mb-0">{task.comments.data.length} Comments</h2>
            {#each task.comments.data as comment, i (comment.id)}
                <div id={comment.id}>
                    <div class="flex justify-between mb-4">
                        <div class="flex items-start">
                            <div class="mr-4 rounded-full min-w-[40px] max-w-[40px] min-h-[40px] max-h-[40px] overflow-hidden">
                                {#if !comment.user.avatar || comment.user.avatar.includes('placeholder.com')}
                                    <img src={comment.default_avatar} class="object-cover" alt="user">
                                {:else}
                                    <img src="/images/user{comment.user.id}/{comment.user.avatar}" class="object-cover" alt="user">
                                {/if}
                            </div>
                            <div>
                                <div class="text-13 md:text-sm">
                                    <span class="block font-bold text-[#3A4374] dark:text-white">{comment.user.username}</span>
                                    <span class="block text-[#647196] dark:text-gray-400"><time>{comment.created_at}</time></span>
                                </div>
                                <p class="hidden md:inline-block text-15 mt-4 text-[#647196] dark:text-[#D1D7E9]">{comment?.body}</p>
                            </div>
                        </div>
                        <div>
                            {#if comment.user.id === client_d || is_admin}
                                <button type="button" use:inertia="{{ href: `/dashboard/comment/${comment.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="font-semibold text-13 text-red-500 dark:text-red-400 hover:text-red-400 hover:underline mr-2">Delete</button>
                                {#if is_admin}
                                    <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyTo = comment.user.username; replyNow = true; replyToComment = true; comment_id = comment.id; user_id = comment.user.id}} class="font-semibold text-13 text-[#4661E6] dark:text-[#3bb0e3] hover:text-[#7389f5] dark:hover:text-white hover:underline">Reply</button>
                                {/if}
                            {:else}
                                <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyTo = comment.user.username; replyNow = true; replyToComment = true; comment_id = comment.id; user_id = comment.user.id}} class="font-semibold text-13 text-[#4661E6] dark:text-[#3bb0e3] hover:text-[#7389f5] dark:hover:text-white hover:underline">Reply</button>
                            {/if}
                        </div>
                    </div>
                    <p class="md:hidden text-[#647196]">{comment?.body}</p>
                </div>

                {#if (task.comments.data.length - 1) > i}
                    <hr class="dark:border-gray-700 dark:border-[#444444]">
                {/if}

                {#if comment.replies.length > 0}
                    {#each comment.replies.sort() as reply (reply.id)}
                        <div id={reply.id} class="pl-10 ml-5 pt-4 border-dotted border-l border-[#E5E7EB] dark:border-[#444444]">
                            <div class="flex justify-between mb-4">
                                <div class="flex items-start">
                                    <div class="mr-4 rounded-full min-w-[40px] max-w-[40px] min-h-[40px] max-h-[40px] overflow-hidden">
                                        {#if !reply.user_avatar || reply.user_avatar.includes('placeholder.com')}
                                            <img src={reply.default_avatar} class="object-cover" alt="user">
                                        {:else}
                                            <img src="/images/user{reply.user_id}/{reply.user_avatar}" class="object-cover" alt="user">
                                        {/if}
                                    </div>
                                    <div>
                                        <div class="text-13 md:text-sm">
                                            <span class="block font-bold text-[#3A4374] dark:text-white">{reply.username}</span>
                                            <span class="block text-[#647196] dark:text-gray-400"><time>{reply.created_at}</time></span>
                                        </div>
                                        <p class="hidden md:inline-block text-15 mt-4 text-[#647196] dark:text-[#D1D7E9]">
                                            <span class="text-blue-600 dark:text-[#3bb0e3]">@{reply.recipient}&#xa0;</span>
                                            {reply.body}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    {#if reply.user_id === client_d || is_admin}
                                        <button type="button" use:inertia="{{ href: `/dashboard/comment/reply/${reply.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="font-semibold text-13 text-red-500 dark:text-red-400 hover:text-red-400 hover:underline">Delete</button>
                                        {#if is_admin}
                                            <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyTo = reply.username; replyNow = true; comment_id = comment.id; user_id = reply.user_id}} class="font-semibold text-13 text-[#4661E6] dark:text-[#3bb0e3] dark:hover:text-white hover:text-[#7389f5] hover:underline">Reply</button>
                                        {/if}
                                    {:else}
                                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyTo = reply.username; replyNow = true; comment_id = comment.id; user_id = reply.user_id}} class="font-semibold text-13 text-[#4661E6] dark:text-[#3bb0e3] dark:hover:text-white hover:text-[#7389f5] hover:underline">Reply</button>
                                    {/if}
                                </div>
                            </div>
                            <p class="md:hidden text-[#647196]">
                                <span class="text-blue-600 dark:text-[#3bb0e3]">@{reply.recipient}&#xa0;</span>
                                {reply.body}
                            </p>
                        </div>
                    {/each}
                {/if}
            {/each}
        </div>
    {/if}

    <!--pagination-->
    <div class="flex justify-between my-6 px-6 sm:px-0">
        {#if task.comments.prev_page_url}
            <a use:inertia={{replace: true, preserveScroll: true}} href="{task.comments.prev_page_url}" class="text-[#238AB6] hover:opacity-40"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></a>
        {:else}
            <button type="button" class="text-[#238AB6] opacity-40" disabled><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg></button>
        {/if}

        {#if task.comments.next_page_url}
            <a use:inertia={{replace: true, preserveScroll: true}} href="{task.comments.next_page_url}" class="text-[#238AB6] hover:opacity-40"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></a>
        {:else}
            <button type="button" class="text-[#238AB6] opacity-40" disabled><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg></button>
        {/if}
    </div>

    <form on:submit|preventDefault={submit} class="bg-white dark:bg-[#222222] mt-6 p-6 hover:shadow-lg rounded-[0.625rem] text-13">
        <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6">Add Comment</h2>
        <div>
            <label for="body" class="block font-bold text-13 text-[#3A4374] sr-only">Create Comment</label>
            <div class="mt-3">
               <textarea bind:value={$form.body} maxlength="255" spellcheck="true" rows="3" cols="50" name="body" id="body" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 ring-inset ring-transparent placeholder:text-[#8C92B4] dark:placeholder:text-white/40 focus:ring-2 focus:ring-inset focus:ring-indigo-600" placeholder="Type your comment here" style="resize: none;" required></textarea>
            </div>
        </div>
        <div class="flex items-center justify-between mt-4">
            <div class="text-13 md:text-15 text-[#647196] dark:text-gray-500">
                {#if $form.errors.body}
                    <span class="block text-red-500">{$form.errors.body}</span>
                {/if}
                <span>{255 - $form.body.length } Characters left</span>
            </div>
            <button type="submit" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap" disabled={$form.processing}>Post Comment</button>
        </div>
    </form>
</section>

{#if viewImage}
    <div class="fixed flex justify-center items-center inset-0 bg-black bg-opacity-50 z-50 p-20">
        <div class="relative bg-black w-full h-full overflow-hidden p-4">
                    <span on:click={()=>{viewImage = false;}} on:keydown class="absolute right-2 top-2 text-white cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                    </span>
            <img src="/images/user{task.user_id}/{imageSrc.path}" class="w-full h-full object-contain" alt={imageSrc.name}>
        </div>
    </div>
{/if}

{#if replyNow}
    <div class="fixed bottom-0 left-0 w-full bg-white dark:bg-[#222222] mt-6 p-6 hover:shadow-lg z-50">
        {#if replyToComment}
            <form on:submit|preventDefault={submitReply} class="max-w-[730px] mx-auto rounded-[0.625rem] text-13">
                <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6">Replying to {replyTo}</h2>
                <div>
                    <label for="body" class="block font-bold text-13 text-[#3A4374] sr-only">Create Comment</label>
                    <div class="mt-3">
                        <textarea bind:value={$replyForm.body} maxlength="255" spellcheck="true" rows="3" cols="50" name="body" id="body" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-white p-3 rounded-[0.3125rem] border-0 ring-1 ring-inset ring-transparent placeholder:text-[#8C92B4] dark:placeholder:text-white/40 focus:ring-2 focus:ring-inset focus:ring-indigo-600" placeholder="Type your comment here" style="resize: none;" required autofocus></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <div class="text-13 md:text-15 text-[#647196] dark:text-gray-500">
                        {#if $replyForm.errors.body}
                            <span class="block text-red-500">{$replyForm.errors.body}</span>
                        {/if}
                        <span>{255 - $form.body.length } Characters left</span>
                    </div>
                    <div>
                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = false; $replyForm.body = ''; replyToComment = false;}} class="bg-red-500 hover:bg-red-400 font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap">Cancel</button>
                        <button type="submit" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap" disabled={$replyForm.processing}>Post Comment</button>
                    </div>
                </div>
            </form>
        {:else}
            <form on:submit|preventDefault={submitCommentReply} class="max-w-[730px] mx-auto rounded-[0.625rem] text-13">
                <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6">Replying to {replyTo}</h2>
                <div>
                    <label for="body" class="block font-bold text-13 text-[#3A4374] sr-only">Create Comment</label>
                    <div class="mt-3">
                        <textarea bind:value={$submitReplyForm.body} maxlength="255" spellcheck="true" rows="3" cols="50" name="body" id="body" class="block w-full bg-[#F7F8FE] dark:bg-white/20 text-13 md:text-15 text-[#3A4374] dark:text-[#8C92B4] p-3 rounded-[0.3125rem] border-0 ring-1 ring-inset ring-transparent placeholder:text-[#8C92B4] dark:placeholder:text-white/40 focus:ring-2 focus:ring-inset focus:ring-indigo-600" placeholder="Type your comment here" style="resize: none;" required autofocus></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <div class="text-13 md:text-15 text-[#647196] dark:text-gray-500">
                        {#if $submitReplyForm.errors.body}
                            <span class="block text-red-500">{$submitReplyForm.errors.body}</span>
                        {/if}
                        <span>{255 - $form.body.length } Characters left</span>
                    </div>
                    <div>
                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = false; $submitReplyForm.body = '';}} class="bg-red-500 hover:bg-red-400 font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap">Cancel</button>
                        <button type="submit" class="bg-[#238AB6] hover:bg-[#186489] font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap" disabled={$submitReplyForm.processing}>Post Comment</button>
                    </div>
                </div>
            </form>
        {/if}
    </div>
{/if}
