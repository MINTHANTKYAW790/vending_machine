<h1>Login</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST" action="/login">
    <div class="field">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" required>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
    </div>
    <button class="btn btn-primary" type="submit">Login</button>
</form>

<p>Need an account? <a href="/register">Register here</a></p>
<p>Default admin user: <code>admin / admin123</code></p>
