<x-layouts.guest title="login">
    <h1 class="fs-1 text-center">Sign in</h1>
    <p class="lead text-center text-body-secondary">Access our dashboard and start tracking your tasks.</p>

    <form class="mb-5">
        <div class="mb-4">
            <label class="visually-hidden" for="email">Email Address</label>
            <input class="form-control" id="email" type="email" placeholder="Enter your email address..." />
        </div>
        <button class="btn btn-secondary w-100" type="submit">Sign in</button>
    </form>

    <p class="text-center text-body-secondary mb-0">
        Don't have an account yet? <a href="sign-up.html">Sign up</a>.
    </p>
</x-layouts.guest>
