<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import {useForm, inertia} from "@inertiajs/inertia-svelte";

    export let task;
    /* svelte-ignore unused-export-let */
    export let flash = {};
    console.log(task);
    let editTask = false;
    let editReplyTask = false;
    let replyNow = false;
    let replyToComment = false;
    let comment_id, user_id, recipient_id;
    $: comment_id = null;
    $: user_id = null;
    $: recipient_id = null;

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

    function submitReplyEdit(comment_id, comment_user_id, reply_id) {
        if ($editReplyForm.body === '') {
            return;
        }

        $editReplyForm.comment_id = comment_id;
        $editReplyForm.recipient_id = comment_user_id;
        $editReplyForm.patch(`/dashboard/comment/reply/${reply_id}`, {
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
        $submitReplyForm.post(`/dashboard/comment/reply/create`, {
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

    const back = () =>
    {
        window.history.back();
    }
</script>

<svelte:head>
    <title>Task</title>
</svelte:head>

<section class="p-6">
    <div class="max-w-3xl mx-auto">
        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <div class="flex justify-end">
            <a use:inertia href="/dashboard/task/{task.slug}/edit" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">Edit Task</a>
            <button type="button" use:inertia="{{ href: `/dashboard/task/${task.id}`, method: 'delete', replace: true, }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>
        </div>
        <div class="space-y-4 mt-5">
            <div class="relative border border-gray-200 rounded p-4 my-2">
                <div class="flex justify-between items-center">
                    {#if task.priority}
                        <span class="absolute right-1 top-1 text-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" /></svg>
                        </span>
                    {/if}
                    <h2 class="text-2xl font-bold pb-3">{task.title}</h2>

                    <span>Created by: {task.user}</span>
                </div>
                <p>{@html task.description}</p>

                {#if task.images.length > 0}
                    <ul role="list" class="mx-auto my-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none">
                        {#each task.images as image}
                            <li on:click={()=>{inspectImage(image)}} on:keydown>
                                <img src="/images/tasks/{image.path}" class="aspect-[3/2] w-full rounded-md object-cover" alt={image.name}>
                            </li>
                        {/each}
                    </ul>
                {/if}

                <div>
                    <a use:inertia={{ replace: true }} href="/dashboard?tag={task.tag}" class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white">{task.tag}</a>
                    <button type="button" use:inertia="{{ href: `/dashboard/tasks/${task.id}/like`, method: 'post', data: { user: task.owner_id }, replace: true, preserveScroll: true, }}">Like</button>
                    <span>{task.likes}</span>
                    {#if task.due_date}
                        <span class="ml-4 font-bold text-blue-500">Due on: {task.due_date}</span>
                    {/if}
                </div>
            </div>
        </div>

        {#if viewImage}
            <div class="absolute flex justify-center items-center inset-0 bg-black bg-opacity-20 z-50 p-20">
                <div class="relative bg-black w-full h-full overflow-hidden p-4">
                    <span on:click={()=>{viewImage = false;}} on:keydown class="absolute right-2 top-2 text-white cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                    </span>
                    <img src="/images/tasks/{imageSrc.path}" class="w-full h-full object-contain" alt={imageSrc.name}>
                </div>
            </div>
        {/if}

        <a use:inertia={{ replace: true }} href="#" on:click={back}>Go Back</a>
    </div>

    <div class="border border-gray-200 p-6 rounded-xl max-w-3xl mx-auto">
        <form on:submit|preventDefault={submit}>
            <div class="mt-6">
                <textarea bind:value={$form.body} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring" placeholder="Quick, think of something to say!" ></textarea>
            </div>
            {#if $form.errors.body}
                <p class="text-red-500 text-xs mt-1">{$form.errors.body}</p>
            {/if}

            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600 transition ease-in-out duration-150" disabled={$form.processing}>Post</button>
            </div>
        </form>
    </div>

    {#each task.comments.data as comment (comment.id)}
        <div class="border border-gray-200 p-6 mt-4 rounded-xl bg-gray-50 max-w-3xl mx-auto">
            <div class="flex space-x-4">
                <div class="flex-shrink-0">
                    <img src="https://i.pravatar.cc/60?u=2" alt="" width="60" height="60" class="rounded-xl">
                </div>
                <div class="flex-1">
                    <header class="mb-4">
                        <h3 class="font-bold">{comment.user.name}</h3>
                        <p class="text-xs">Posted <time>{comment.created_at}</time></p>
                    </header>
                    {#if editTask}
                        <form on:submit|preventDefault={()=>{submitEdit(comment.id)}}>
                            <textarea on:input={(e)=>{$editForm.body = e.target.value}} name="body" cols="30" rows="5" class="w-full text-sm focus:outline-none focus:ring">{comment.body}</textarea>
                            {#if $editForm.errors.body}
                                <p class="text-red-500 text-xs mt-1">{$editForm.errors.body}</p>
                            {/if}

                            <div class="flex justify-end">
                                <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{editTask = false}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Cancel</button>
                                <button type="submit" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-blue-500 ml-4" disabled={$editForm.processing}>Update</button>
                            </div>
                        </form>
                    {:else}
                        <p>
                            {comment.body}
                        </p>
                    {/if}
                </div>
            </div>
            {#if !editTask}
                <div class="flex justify-end">
                    <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; replyToComment = true; comment_id = comment.id; user_id = comment.user.id}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-purple-400 ml-4">Reply</button>
                    <button type="button" use:inertia={{ href: '#', replace: true, preserveScroll: true }} on:click={()=>{editTask = true}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white ml-4">Edit</button>
                    <button type="button" use:inertia="{{ href: `/dashboard/comment/${comment.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>
                </div>
            {/if}
        </div>

        {#if comment.replies.length > 0}
            {#each comment.replies as reply (reply.id)}
                <div class="max-w-3xl mx-auto border-l-2 border-dashed border-blue-200">
                    <div class="ml-20 rounded-xl bg-gray-50 p-6 mt-4 border border-l-4 border-gray-200">
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <img src="https://i.pravatar.cc/60?u=2" alt="" width="60" height="60" class="rounded-xl">
                            </div>
                            <div class="flex-1">
                                <header class="mb-4">
                                    <h3 class="font-bold">{reply.user}</h3>
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
                                <button type="button" use:inertia="{{ href: '#', replace: true, preserveScroll: true }}" on:click={()=>{replyNow = true; comment_id = comment.id; user_id = comment.user.id}} class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-purple-400 ml-4">Reply</button>
                                <button type="button" use:inertia={{ href: '#', replace: true, preserveScroll: true }} on:click={()=>{editReplyTask = true}} class="inline-block mt-2 text-blue-500 border border-gray-200 px-4 py-1 rounded-lg bg-white ml-4">Edit</button>
                                <button type="button" use:inertia="{{ href: `/dashboard/comment/reply/${reply.id}`, method: 'delete', replace: true, preserveScroll: true }}" class="inline-block mt-2 text-white border border-gray-200 px-4 py-1 rounded-lg bg-red-400 ml-4">Delete</button>
                            </div>
                        {/if}
                    </div>
                </div>
            {/each}
        {/if}
    {/each}

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
                <a use:inertia={{ replace: true }} href="{task.comments.prev_page_url}" class="px-1">&laquo; Prev</a>
            {/if}

            {#if task.comments.next_page_url}
                <a use:inertia={{ replace: true }} href="{task.comments.next_page_url}" class="px-1">Next &raquo;</a>
            {/if}
        </div>
    </div>
</section>
