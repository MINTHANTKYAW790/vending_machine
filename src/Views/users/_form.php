<script>
function validateUserForm(form) {
    const username = form.querySelector('[name="username"]').value.trim();
    const email = form.querySelector('[name="email"]').value.trim();
    const password = form.querySelector('[name="password"]').value;
    const role = form.querySelector('[name="role"]').value;
    let ok = true;

    const setError = (field, message) => {
        const target = document.getElementById(field + '-client-error');
        if (target) target.textContent = message;
    };

    setError('username', '');
    setError('email', '');
    setError('password', '');
    setError('role', '');

    if (!username || username.length < 3) {
        setError('username', 'Username is required and must be at least 3 characters.');
        ok = false;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setError('email', 'Valid email is required.');
        ok = false;
    }
    if (password && password.length < 6) {
        setError('password', 'Password must be at least 6 characters.');
        ok = false;
    }
    if (role !== 'User' && role !== 'Admin') {
        setError('role', 'Role must be User or Admin.');
        ok = false;
    }
    return ok;
}
</script>

<div class="field">
    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="<?= e((string) ($old['username'] ?? $user['username'] ?? '')) ?>" required>
    <div class="error"><?= e((string) ($errors['username'] ?? '')) ?></div>
    <div id="username-client-error" class="error"></div>
</div>

<div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="<?= e((string) ($old['email'] ?? $user['email'] ?? '')) ?>" required>
    <div class="error"><?= e((string) ($errors['email'] ?? '')) ?></div>
    <div id="email-client-error" class="error"></div>
</div>

<div class="field">
    <label for="password">Password<?= isset($user) ? ' (leave blank to keep current)' : '' ?></label>
    <input id="password" type="password" name="password"<?= !isset($user) ? ' required' : '' ?>>
    <div class="error"><?= e((string) ($errors['password'] ?? '')) ?></div>
    <div id="password-client-error" class="error"></div>
</div>

<div class="field">
    <label for="role">Role</label>
    <select id="role" name="role" required>
        <option value="User"<?= ((string) ($old['role'] ?? $user['role'] ?? 'User')) === 'User' ? ' selected' : '' ?>>User</option>
        <option value="Admin"<?= ((string) ($old['role'] ?? $user['role'] ?? 'User')) === 'Admin' ? ' selected' : '' ?>>Admin</option>
    </select>
    <div class="error"><?= e((string) ($errors['role'] ?? '')) ?></div>
    <div id="role-client-error" class="error"></div>
</div>