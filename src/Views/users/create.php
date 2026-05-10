<h1>Create User</h1>

<form method="POST" action="/users" onsubmit="return validateUserForm(this);">
    <?php require __DIR__ . '/_form.php'; ?>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn" href="/users">Cancel</a>
</form>