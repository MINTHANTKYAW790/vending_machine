<h1>Register</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<form method="POST" action="/register">
    <div class="field">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" required>
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" minlength="6" required>
    </div>
    <div class="field">
        <label for="password_confirm">Confirm Password</label>
        <input id="password_confirm" type="password" name="password_confirm" minlength="6" required>
    </div>
    <button class="btn btn-primary" type="submit">Register</button>
</form>

<p>Already have an account? <a href="/login">Login</a></p>
