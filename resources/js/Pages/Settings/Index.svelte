<script context="module">
    export {default as layout} from "../../Layouts/Authenticated.svelte";
</script>

<script>
    import { useForm, inertia } from "@inertiajs/inertia-svelte";
    import { Inertia } from "@inertiajs/inertia";
    import { router } from '@inertiajs/svelte'
    import FilePond, { registerPlugin, supported } from 'svelte-filepond';
    import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
    import 'filepond/dist/filepond.min.css';
    registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

    /* svelte-ignore unused-export-let */
    export let errors = {};
    /* svelte-ignore unused-export-let */
    export let auth = {};
    /* svelte-ignore unused-export-let */
    export let flash = {};
    /* svelte-ignore unused-export-let */
    export let csrf_token;
    /* svelte-ignore unused-export-let */
    export let user;

    let options = {
        url: '',
        process: {
            url: '/dashboard/image/upload',
            method: 'POST',
            onload: handleFilePondLoad,
        },
        revert: handleFilePondRevert,
        headers: {
            'X-CSRF-TOKEN': csrf_token
        }
    }

    let conf = {
        height: 300,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'preview',
            'anchor', 'code'
        ],
        toolbar: 'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | code |  bullist numlist outdent indent | ',
        menubar: '',
        statusbar: false
    }

    let personalInformationForm = useForm({
        image: null,
        name: user.name,
        email: user.email,
        username: user.username,
    });

    let passwordForm = useForm({
        current_password : '',
        new_password : '',
        confirm_password : '',
    });

    let reviewForm = useForm({
        rating : null,
        review : null,
    });

    function submitPersonalInformation() {
        $personalInformationForm.patch(`/dashboard/settings/update/information`, {
            replace: true,
        });
        router.reload({ only: ['users'] })
    }

    function submitPasswordChange() {
        if ($passwordForm.new_password !== $passwordForm.confirm_password) {
            $passwordForm.errors.confirm_password = 'The new password and confirm password must match.';
            return;
        }

        $passwordForm.patch(`/dashboard/settings/update/password`, {
            replace: true,
        });

        $passwordForm.current_password = '';
        $passwordForm.new_password = '';
        $passwordForm.confirm_password = '';
    }

    function submitReview() {
        $reviewForm.post(`/dashboard/settings/create/review`, {
            replace: true,
            preserveScroll: true,
        });

        $reviewForm.rating = null;
        $reviewForm.review = null;
    }

    function handleFilePondLoad(response) {
        $personalInformationForm.image = response;
        return response;
    }

    function handleFilePondRevert(uniqueId, load) {
        $personalInformationForm.image = null;

        Inertia.delete(`/dashboard/image/revert/${uniqueId}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('image deleted');
            }
        });

        load();
    }

    const confirmPasswords = () => {
        if ($passwordForm.new_password !== $passwordForm.confirm_password) {
            $passwordForm.errors.confirm_password = 'The new password and confirm password must match.';
        } else {
            $passwordForm.errors.confirm_password = '';
        }
    }
</script>

<svelte:head>
    <title>Settings</title>
</svelte:head>

<!--
  This example requires updating your template:

  ```
  <html class="h-full bg-gray-900">
  <body class="h-full">
  ```
-->
<section class="p-6 bg-gray-900">
    <div class="max-w-7xl mx-auto">

        {#if flash.message}
            <div class="text-green-500 font-bold">{flash.message}</div>
        {/if}

        <main>
            <!-- Settings forms -->
            <div class="divide-y divide-white/5">
                <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-white">Personal Information</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-400">Update your information such as profile image and username.</p>
                    </div>

                    <form on:submit|preventDefault={submitPersonalInformation} class="md:col-span-2" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                            <div class="col-span-full flex">
                                {#if !user.avatar || user.avatar.includes('placeholder.com')}
                                    <img src={user.default_avatar} class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover mr-8" alt="">
                                {:else}
                                    <div class="relative mr-8">
                                        <button type="button" use:inertia="{{ href: '/dashboard/settings/delete/avatar', method: 'delete', replace: true, preserveScroll: true }}" class="absolute -top-2 -right-2 text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <img src="/images/user{user.id}/{user.avatar}" class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover" alt="">
                                    </div>
                                {/if}
                                <div class="flex-1 app">
                                    <FilePond
                                        name="image"
                                        server={options}
                                        allowMultiple={false}
                                        allowRevert={true}
                                        acceptedFileTypes={['image/png', 'image/jpeg', 'image/jpg']}
                                        labelIdle='Drag & Drop your image or <span class="filepond--label-action">Browse</span>'
                                    />
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium leading-6 text-white">Name</label>
                                <div class="mt-2">
                                    <input type="text" value={user.name} on:input={(e)=>{$personalInformationForm.name = e.target.value}} name="name" id="name" autocomplete="given-name" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    {#if $personalInformationForm.errors.name}
                                        <p class="text-red-500 text-xs mt-1"> {$personalInformationForm.errors.name} </p>
                                    {/if}
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium leading-6 text-white">Email address</label>
                                <div class="mt-2">
                                    <input id="email" value={user.email} on:input={(e)=>{$personalInformationForm.email = e.target.value}} name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    {#if $personalInformationForm.errors.email}
                                        <p class="text-red-500 text-xs mt-1"> {$personalInformationForm.errors.email} </p>
                                    {/if}
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label for="username" class="block text-sm font-medium leading-6 text-white">Username</label>
                                <div class="mt-2">
                                    <div class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                                        <span class="flex select-none items-center pl-3 text-gray-400 sm:text-sm">example/</span>
                                        <input type="text" value={user.username} on:input={(e)=>{$personalInformationForm.username = e.target.value}} name="username" id="username" autocomplete="username" class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6" placeholder="janesmith">
                                        {#if $personalInformationForm.errors.username}
                                            <p class="text-red-500 text-xs mt-1"> {$personalInformationForm.errors.username} </p>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex">
                            <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500" disabled={$personalInformationForm.processing}>Save</button>
                        </div>
                    </form>
                </div>

                <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-white">Change password</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-400">Update your password associated with your account.</p>
                    </div>

                    <form on:submit|preventDefault={submitPasswordChange} class="md:col-span-2">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                            <div class="col-span-full">
                                <label for="current_password" class="block text-sm font-medium leading-6 text-white">Current password</label>
                                <div class="mt-2">
                                    <input bind:value={$passwordForm.current_password} id="current_password" name="current_password" type="password" autocomplete="current-password" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    {#if $passwordForm.errors.current_password}
                                        <p class="text-red-500 text-xs mt-1"> {$passwordForm.errors.current_password} </p>
                                    {/if}
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label for="new_password" class="block text-sm font-medium leading-6 text-white">New password</label>
                                <div class="mt-2">
                                    <input bind:value={$passwordForm.new_password} on:input={confirmPasswords} id="new_password" name="new_password" type="password" autocomplete="new-password" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    {#if $passwordForm.errors.new_password}
                                        <p class="text-red-500 text-xs mt-1"> {$passwordForm.errors.new_password} </p>
                                    {/if}
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label for="confirm_password" class="block text-sm font-medium leading-6 text-white">Confirm password</label>
                                <div class="mt-2">
                                    <input bind:value={$passwordForm.confirm_password} on:input={confirmPasswords} id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    {#if $passwordForm.errors.confirm_password}
                                        <p class="text-red-500 text-xs mt-1"> {$passwordForm.errors.confirm_password} </p>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex">
                            <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500" disabled={$passwordForm.processing}>Save</button>
                        </div>
                    </form>
                </div>

                <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-white">Delete account</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-400">No longer want to use our service? You can delete your account here. This action is not reversible. All information related to this account will be deleted permanently.</p>
                    </div>

                    <div class="flex items-start md:col-span-2">
                        <button type="submit" use:inertia="{{ href: '/dashboard/settings/delete/account', method: 'delete', data: { user: user.id }, replace: true, preserveScroll: true }}" class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-400">Yes, delete my account</button>
                    </div>
                </div>

                <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-white">Leave a review</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-400">We value your feedback. Please rate your experience and comment on your experience.</p>
                    </div>

                    <form on:submit|preventDefault={submitReview} class="md:col-span-2">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                            <div class="star-rating">
                                <input type="radio" checked={$reviewForm.rating===5} on:change={(e)=>{$reviewForm.rating = e.currentTarget.value;}} id="5-stars" name="rating" value="5" />
                                <label for="5-stars" class="star">&#9733;</label>
                                <input type="radio" checked={$reviewForm.rating===4} on:change={(e)=>{$reviewForm.rating = e.currentTarget.value;}} id="4-stars" name="rating" value="4" />
                                <label for="4-stars" class="star">&#9733;</label>
                                <input type="radio" checked={$reviewForm.rating===3} on:change={(e)=>{$reviewForm.rating = e.currentTarget.value;}} id="3-stars" name="rating" value="3" />
                                <label for="3-stars" class="star">&#9733;</label>
                                <input type="radio" checked={$reviewForm.rating===2} on:change={(e)=>{$reviewForm.rating = e.currentTarget.value;}} id="2-stars" name="rating" value="2" />
                                <label for="2-stars" class="star">&#9733;</label>
                                <input type="radio" checked={$reviewForm.rating===1} on:change={(e)=>{$reviewForm.rating = e.currentTarget.value;}} id="1-star" name="rating" value="1" />
                                <label for="1-star" class="star">&#9733;</label>
                            </div>

                            <div class="col-span-full">
                                <label for="review" class="block text-sm font-medium leading-6 text-white">Your review</label>
                                <div class="mt-2">
                                    <textarea id="review" bind:value={$reviewForm.review} name="review" cols="30" rows="4" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex">
                            <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500" disabled={$reviewForm.processing}>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</section>

<style global>
    @import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

    .star-rating {
        display:flex;
        flex-direction: row-reverse;
        font-size:1.3rem;
        justify-content:space-around;
        padding:0 .2em;
        text-align:center;
        width:5em;
    }

    .star-rating input {
        display:none;
    }

    .star-rating label {
        color:#ccc;
        cursor:pointer;
    }

    .star-rating :checked ~ label {
        color:#f90;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color:#fc0;
    }
</style>
