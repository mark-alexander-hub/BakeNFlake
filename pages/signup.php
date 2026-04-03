<?php $pageTitle = 'Sign Up'; require_once __DIR__ . '/../includes/header.php'; ?>

<section class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h1>Join Us</h1>
            <p>Create your Bake'n Flake account</p>
        </div>
        <form action="<?= SITE_URL ?>/api/auth_actions.php" method="POST" class="auth-form" id="signupForm">
            <input type="hidden" name="action" value="signup">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">

            <div class="form-group">
                <input type="text" name="name" id="name" required placeholder=" ">
                <label for="name">Full Name</label>
            </div>

            <div class="form-group">
                <input type="email" name="email" id="email" required placeholder=" ">
                <label for="email">Email Address</label>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" required minlength="8" placeholder=" ">
                <label for="password">Password (min 8 characters)</label>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" required placeholder=" ">
                <label for="confirm_password">Confirm Password</label>
            </div>

            <button type="submit" class="btn btn-gold btn-full">Create Account</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="<?= SITE_URL ?>/pages/login.php">Sign in</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
