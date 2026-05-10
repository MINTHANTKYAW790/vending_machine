<h1>Edit User</h1>

<form method="POST" action="/users/<?= (int) $user['id'] ?>/update" onsubmit="return validateUserForm(this);">
    <?php require __DIR__ . '/_form.php'; ?>
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn" href="/users">Cancel</a>
</form>