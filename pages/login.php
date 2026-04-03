<?php $pageTitle = 'Login'; require_once __DIR__ . '/../includes/header.php'; ?>

<section class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account</p>
        </div>
        <form action="<?= SITE_URL ?>/api/auth_actions.php" method="POST" class="auth-form">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">

            <div class="form-group">
                <input type="email" name="email" id="email" required placeholder=" ">
                <label for="email">Email Address</label>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>

            <button type="submit" class="btn btn-gold btn-full">Sign In</button>
        </form>
        <p class="auth-switch">Don't have an account? <a href="<?= SITE_URL ?>/pages/signup.php">Sign up</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
