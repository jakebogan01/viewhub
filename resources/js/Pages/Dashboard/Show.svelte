<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    export let task;
    export let client_d;
    let editTask = false;
    let editReplyTask = false;
    let replyNow = false;
    let replyToComment = false;
    let comment_id, user_id;
    $: comment_id = null;
    $: user_id = null;

    console.log(auth)
    let form = useForm({
        task_id: task.id,
        body: '',
    });

    let editForm = useForm({
        task_id: task.id,
        body: '',
    });

    let replyForm = useForm({
        comment_id: null,
        recipient_id: null,
        body: '',
    });

    let editReplyForm = useForm({
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
            preserveScroll: true,
        })

        $form.body = '';
    }

    function submitEdit(id) {
        if ($editForm.body === '') {
            return;
        }

        $editForm.patch(`/dashboard/comment/${id}`, {
            replace: true,
            preserveScroll: true,
        })

        editTask = false;
        $editForm.body = '';
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

    function submitReplyEdit(comment, comment_user, replied_user) {
        if ($editReplyForm.body === '') {
            return;
        }

        $editReplyForm.comment_id = comment;
        $editReplyForm.recipient_id = comment_user;
        $editReplyForm.patch(`/dashboard/comment/reply/${replied_user}`, {
            replace: true,
            preserveScroll: true,
        })

        editReplyTask = false;
        $editReplyForm.body = '';
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
    <div class="text-green-500 font-bold">{flash.message}</div>
{/if}

<section class="">
    <div class="flex justify-between mb-6">
        <a use:inertia={{ replace: true }} href="#" on:click={()=>{window.history.back()}} class="flex items-center space-x-2">
            <svg width="7" height="10" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L2 5l4-4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
            <span class="font-bold text-13 md:text-sm text-[#647196]">Go Back</span>
        </a>

        {#if task.user_id === client_d}
            <div class="space-x-2">
                <button type="button" use:inertia="{{ href: `/dashboard/task/${task.id}`, method: 'delete', replace: true, }}" class="bg-[#F87171] hover:bg-[#7C91F9] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">Delete</button>
                <button type="button" use:inertia="{{ href: `/dashboard/task/${task.slug}/edit`, method: 'get' }}" class="bg-[#4761E6] hover:bg-[#7C91F9] font-bold text-13 text-[#F2F4FE] md:text-sm whitespace-nowrap py-2.5 px-4 rounded-[0.625rem]">Edit Feedback</button>
            </div>
        {/if}
    </div>

    <div class="flex flex-col md:flex-row bg-white dark:bg-[#1E283A] hover:shadow-lg p-6 md:py-7 md:px-8 rounded-[0.625rem] text-13 group" data-sveltekit-preload-data="hover">
        <div class="hidden md:block pr-10">
            <button type="button" use:inertia="{{ href: `/notification/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}" class="flex flex-row md:flex-col items-center md:justify-center space-x-2 md:space-x-0 bg-[#F2F4FF] hover:bg-[#CED7FF] dark:bg-[#17202F] dark:hover:bg-[#3A4374] dark:hover:text-white rounded-[0.625rem] px-4 py-1 md:p-0 md:w-10 md:h-[3.3125rem] font-bold text-[#3A4374] dark:text-white">
                <svg width="10" height="7" xmlns="http://www.w3.org/2000/svg"><path d="M1 6l4-4 4 4" stroke="#4661E6" stroke-width="2" fill="none" fill-rule="evenodd"/></svg>
                <span class="mt-1">{task.likes}</span>
            </button>
        </div>
        <div class="flex-1 md:pr-[1.5625rem]">
            <a use:inertia href="/dashboard/tasks/{task.slug}">
                <h2 class="font-bold text-[#3A4374] group-hover:text-[#238AB6] dark:text-white md:text-lg leading-3">{task.title}</h2>
            </a>
            <p class="text-[#647196] dark:text-[#D1D7E9] md:text-base my-2 md:mb-4">{@html task.description}</p>

            {#if task.images.length > 0}
                <ul role="list" class="mx-auto my-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none">
                    {#each task.images as image}
                        <li on:click={()=>{inspectImage(image)}} on:keydown>
                            <img src="/images/user{task.user_id}/{image.path}" class="aspect-[3/2] w-full rounded-md object-cover" alt={image.name}>
                        </li>
                    {/each}
                </ul>
            {/if}

            <span class="hidden md:inline-block bg-[#F2F4FF] dark:bg-[#17202F] rounded-[0.625rem] px-4 py-1 font-semibold text-[#4661E6] text-base cursor-default">{task.tag}</span>
        </div>
        <div class="flex items-end md:items-center justify-between">
            <a use:inertia href="/dashboard/tasks/{task.slug}" class="flex items-center space-x-2 font-bold text-[#3A4374] md:text-base">
                <svg width="18" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.62 16H1.346l.902-.91c.486-.491.79-1.13.872-1.823C1.036 11.887 0 9.89 0 7.794 0 3.928 3.52 0 9.03 0 14.87 0 18 3.615 18 7.455c0 3.866-3.164 7.478-8.97 7.478-1.017 0-2.078-.137-3.025-.388A4.705 4.705 0 012.62 16z" fill="#CDD2EE" fill-rule="nonzero"/></svg>
                <span class="dark:text-[#D1D7E9]">{task.comment_count}</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1E293B] px-8 py-6 rounded-[0.625rem] text-13 space-y-6 my-6">
        <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6 md:mb-0">{task.comments.data.length} Comments</h2>
        {#each task.comments.data as comment, i (comment.id)}
            <div id={comment.id}>
                <div class="flex justify-between mb-4">
                    <div class="flex items-start">
                        <div class="mr-4 rounded-full w-10 h-10 overflow-hidden">
                            {#if !comment.user.avatar || comment.user.avatar.includes('placeholder.com')}
                                <img src={comment.default_avatar} class="object-cover" alt="">
                            {:else}
                                <img src="/images/user{comment.user.id}/{comment.user.avatar}" class="object-cover" alt="">
                            {/if}
                        </div>
                        <div>
                            <div class="text-13 md:text-sm">
                                <span class="block font-bold text-[#3A4374] dark:text-[#4761E6]">{comment.user.username}</span>
                                <span class="block text-[#647196]"><time>{comment.created_at}</time></span>
                            </div>
                            <p class="hidden md:inline-block text-15 mt-4 text-[#647196] dark:text-[#D1D7E9]">{comment?.body}</p>
                        </div>
                    </div>
                    <div class="mt-5">
                        {#if comment.user.id === client_d}
                            <button type="button" use:inertia="{{ href: `/dashboard/comment/${comment.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="font-semibold text-13 text-red-500 hover:text-red-400 hover:underline mr-2">Delete</button>
                        {:else}
                            <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; replyToComment = true; comment_id = comment.id; user_id = comment.user.id}} class="font-semibold text-13 text-[#4661E6] hover:text-[#7389f5] hover:underline">Reply</button>
                        {/if}
                    </div>
                </div>
                <p class="md:hidden text-[#647196]">{comment?.body}</p>
            </div>

            {#if (task.comments.data.length - 1) > i}
                <hr class="dark:border-gray-700">
            {/if}

            {#if comment.replies.length > 0}
                {#each comment.replies.sort() as reply (reply.id)}
                    <div class="max-w-3xl mx-auto border-l-2 border-dashed border-blue-200" id={reply.id}>
                        <div class="ml-20 rounded-xl bg-gray-50 p-6 mt-4 border border-l-4 border-gray-200">
                            <div class="flex space-x-4">
                                <div class="flex-shrink-0">
                                    {#if !reply.user_avatar || reply.user_avatar.includes('placeholder.com')}
                                        <img src={reply.defaut_avatar} class="h-[60px] w-[60px] rounded-xl" alt="">
                                    {:else}
                                        <img src="/images/user{reply.user_id}/{reply.user_avatar}" class="h-[60px] w-[60px] rounded-xl object-cover" alt="">
                                    {/if}
                                </div>
                                <div class="flex-1">
                                    <header class="mb-4">
                                        <h3 class="font-bold lowercase">{reply.username}</h3>
                                        <p class="text-xs">Posted
                                            <time>{reply.created_at}</time>
                                        </p>
                                    </header>
                                    {#if editReplyTask}
                                        <form on:submit|preventDefault={()=>{submitReplyEdit(comment.id, comment.user.id, reply.id)}}>
                                            <textarea on:input={(e)=>{$editReplyForm.body = e.target.value}} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring">{reply.body}</textarea>
                                            {#if $editReplyForm.errors.body}
                                                <p class="text-red-500 text-xs mt-1">{$editReplyForm.errors.body}</p>
                                            {/if}

                                            <div class="flex justify-end">
                                                <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{editReplyTask = false}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>
                                                <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$editReplyForm.processing}>Update</button>
                                            </div>
                                        </form>
                                    {:else}
                                        <p>
                                            <span class="text-blue-600">@{reply.recipient}&#xa0;</span>
                                            {reply.body}
                                        </p>
                                    {/if}
                                </div>
                            </div>
                            {#if !editReplyTask}
                                <div class="flex justify-end">
                                    <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; comment_id = comment.id; user_id = reply.user_id}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-purple-400 ml-4">Reply</button>
                                    <button type="button" use:inertia={{ href: '#', replace: true, preserveScroll: true }} on:click={()=>{editReplyTask = true}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white ml-4">Edit</button>
                                    <button type="button" use:inertia="{{ href: `/dashboard/comment/reply/${reply.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>
                                </div>
                            {/if}
                        </div>
                    </div>
                {/each}
            {/if}
        {/each}
    </div>

    <form on:submit|preventDefault={submit} class="bg-white dark:bg-[#1E293B] mt-6 p-6 hover:shadow-lg rounded-[0.625rem] text-13">
        <h2 class="font-bold text-lg text-[#3A4374] dark:text-white mb-6">Add Comment</h2>
        <div>
            <label for="body" class="block font-bold text-13 text-[#3A4374] sr-only">Create Comment</label>
            <div class="mt-3">
               <textarea bind:value={$form.body} maxlength="255" spellcheck="true" rows="3" cols="50" name="body" id="body" class="block w-full bg-[#F7F8FE] dark:bg-[#151E2C] text-13 md:text-15 text-[#3A4374] dark:text-[#8C92B4] p-3 rounded-[0.3125rem] border-0 ring-1 ring-inset ring-transparent placeholder:text-[#8C92B4] focus:ring-2 focus:ring-inset focus:ring-indigo-600" placeholder="Type your comment here" style="resize: none;" required></textarea>
            </div>
        </div>
        <div class="flex items-center justify-between mt-4">
            <div class="text-13 md:text-15 text-[#647196]">
                {#if $form.errors.body}
                    <span class="block text-red-500">{$form.errors.body}</span>
                {/if}
                <span>{255 - $form.body.length } Characters left</span>
            </div>
            <button type="submit" class="bg-[#AD1FE9] hover:bg-[#C75AF6] font-bold text-white text-13 md:text-sm rounded-[0.625rem] px-4 py-2.5 whitespace-nowrap" disabled={$form.processing}>Post Comment</button>
        </div>
    </form>
</section>

{#if viewImage}
    <div class="absolute flex justify-center items-center inset-0 bg-black bg-opacity-20 z-50 p-20">
        <div class="relative bg-black w-full h-full overflow-hidden p-4">
                    <span on:click={()=>{viewImage = false;}} on:keydown class="absolute right-2 top-2 text-white cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                    </span>
            <img src="/images/user{task.user_id}/{imageSrc.path}" class="w-full h-full object-contain" alt={imageSrc.name}>
        </div>
    </div>
{/if}

<section class="p-6">

    <!--{#each task.comments.data as comment (comment.id)}-->
    <!--    <div class="border border-gray-200 p-6 mt-4 rounded-xl bg-gray-50 max-w-3xl mx-auto" id={comment.id}>-->
    <!--        <div class="flex space-x-4">-->
    <!--            <div class="flex-shrink-0">-->
    <!--                {#if !comment.user.avatar || comment.user.avatar.includes('placeholder.com')}-->
    <!--                    <img src={comment.default_avatar} class="h-[60px] w-[60px] rounded-xl" alt="">-->
    <!--                {:else}-->
    <!--                    <img src="/images/user{comment.user.id}/{comment.user.avatar}" class="h-[60px] w-[60px] rounded-xl object-cover" alt="">-->
    <!--                {/if}-->
    <!--            </div>-->
    <!--            <div class="flex-1">-->
    <!--                <header class="mb-4">-->
    <!--                    <h3 class="font-bold lowercase">{comment.user.username}</h3>-->
    <!--                    <p class="text-xs">Posted <time>{comment.created_at}</time></p>-->
    <!--                </header>-->
    <!--                {#if editTask}-->
    <!--                    <form on:submit|preventDefault={()=>{submitEdit(comment.id)}}>-->
    <!--                        <textarea on:input={(e)=>{$editForm.body = e.target.value}} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring">{comment.body}</textarea>-->
    <!--                        {#if $editForm.errors.body}-->
    <!--                            <p class="text-red-500 text-xs mt-1">{$editForm.errors.body}</p>-->
    <!--                        {/if}-->

    <!--                        <div class="flex justify-end">-->
    <!--                            <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{editTask = false}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>-->
    <!--                            <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$editForm.processing}>Update</button>-->
    <!--                        </div>-->
    <!--                    </form>-->
    <!--                {:else}-->
    <!--                    <p>-->
    <!--                        {comment.body}-->
    <!--                    </p>-->
    <!--                {/if}-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        {#if !editTask}-->
    <!--            <div class="flex justify-end">-->
    <!--                <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; replyToComment = true; comment_id = comment.id; user_id = comment.user.id}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-purple-400 ml-4">Reply</button>-->
    <!--                <button type="button" use:inertia={{ href: '#', replace: true, preserveScroll: true }} on:click={()=>{editTask = true}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white ml-4">Edit</button>-->
    <!--                <button type="button" use:inertia="{{ href: `/dashboard/comment/${comment.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>-->
    <!--            </div>-->
    <!--        {/if}-->
    <!--    </div>-->

    <!--    {#if comment.replies.length > 0}-->
    <!--        {#each comment.replies.sort() as reply (reply.id)}-->
    <!--            <div class="max-w-3xl mx-auto border-l-2 border-dashed border-blue-200" id={reply.id}>-->
    <!--                <div class="ml-20 rounded-xl bg-gray-50 p-6 mt-4 border border-l-4 border-gray-200">-->
    <!--                    <div class="flex space-x-4">-->
    <!--                        <div class="flex-shrink-0">-->
    <!--                            {#if !reply.user_avatar || reply.user_avatar.includes('placeholder.com')}-->
    <!--                                <img src={reply.defaut_avatar} class="h-[60px] w-[60px] rounded-xl" alt="">-->
    <!--                            {:else}-->
    <!--                                <img src="/images/user{reply.user_id}/{reply.user_avatar}" class="h-[60px] w-[60px] rounded-xl object-cover" alt="">-->
    <!--                            {/if}-->
    <!--                        </div>-->
    <!--                        <div class="flex-1">-->
    <!--                            <header class="mb-4">-->
    <!--                                <h3 class="font-bold lowercase">{reply.username}</h3>-->
    <!--                                <p class="text-xs">Posted-->
    <!--                                    <time>{reply.created_at}</time>-->
    <!--                                </p>-->
    <!--                            </header>-->
    <!--                            {#if editReplyTask}-->
    <!--                                <form on:submit|preventDefault={()=>{submitReplyEdit(comment.id, comment.user.id, reply.id)}}>-->
    <!--                                    <textarea on:input={(e)=>{$editReplyForm.body = e.target.value}} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring">{reply.body}</textarea>-->
    <!--                                    {#if $editReplyForm.errors.body}-->
    <!--                                        <p class="text-red-500 text-xs mt-1">{$editReplyForm.errors.body}</p>-->
    <!--                                    {/if}-->

    <!--                                    <div class="flex justify-end">-->
    <!--                                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{editReplyTask = false}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>-->
    <!--                                        <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$editReplyForm.processing}>Update</button>-->
    <!--                                    </div>-->
    <!--                                </form>-->
    <!--                            {:else}-->
    <!--                                <p>-->
    <!--                                    <span class="text-blue-600">@{reply.recipient}&#xa0;</span>-->
    <!--                                    {reply.body}-->
    <!--                                </p>-->
    <!--                            {/if}-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    {#if !editReplyTask}-->
    <!--                        <div class="flex justify-end">-->
    <!--                            <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; comment_id = comment.id; user_id = reply.user_id}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-purple-400 ml-4">Reply</button>-->
    <!--                            <button type="button" use:inertia={{ href: '#', replace: true, preserveScroll: true }} on:click={()=>{editReplyTask = true}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white ml-4">Edit</button>-->
    <!--                            <button type="button" use:inertia="{{ href: `/dashboard/comment/reply/${reply.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>-->
    <!--                        </div>-->
    <!--                    {/if}-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        {/each}-->
    <!--    {/if}-->
    <!--{/each}-->

    {#if replyNow}
        <div class="fixed bottom-0 left-0 w-full border border-gray-200 p-6 mt-4 rounded-xl bg-gray-50 z-50">
            {#if replyToComment}
                <form on:submit|preventDefault={submitReply}>
                    <textarea bind:value={$replyForm.body} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring" autofocus></textarea>
                    {#if $replyForm.errors.body}
                        <p class="text-red-500 text-xs mt-1">{$replyForm.errors.body}</p>
                    {/if}

                    <div class="flex justify-end">
                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = false; $replyForm.body = ''; replyToComment = false;}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>
                        <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$replyForm.processing}>Post</button>
                    </div>
                </form>
            {:else}
                <form on:submit|preventDefault={submitCommentReply}>
                    <textarea bind:value={$submitReplyForm.body} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring" autofocus></textarea>
                    {#if $submitReplyForm.errors.body}
                        <p class="text-red-500 text-xs mt-1">{$submitReplyForm.errors.body}</p>
                    {/if}

                    <div class="flex justify-end">
                        <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = false; $submitReplyForm.body = '';}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>
                        <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$submitReplyForm.processing}>Post</button>
                    </div>
                </form>
            {/if}
        </div>
    {/if}

    <!--pagination-->
    <div>
        <div class="mt-6">
            {#if task.comments.prev_page_url}
                <a use:inertia href="{task.comments.prev_page_url}" class="px-1">&laquo; Prev</a>
            {/if}

            {#if task.comments.next_page_url}
                <a use:inertia href="{task.comments.next_page_url}" class="px-1">Next &raquo;</a>
            {/if}
        </div>
    </div>
</section>
