<h1>Users</h1>

<?php if (!empty($flashError)): ?><div class="alert alert-error"><?= e($flashError) ?></div><?php endif; ?>
<?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>

<p><a class="btn btn-primary" href="/users/create">Add User</a></p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= (int) $user['id'] ?></td>
            <td><?= e($user['username']) ?></td>
            <td><?= e($user['email']) ?></td>
            <td><?= e($user['role']) ?></td>
            <td class="actions">
                <a class="btn" href="/users/<?= (int) $user['id'] ?>">View</a>
                <a class="btn" href="/users/<?= (int) $user['id'] ?>/edit">Edit</a>
                <?php if ($user['id'] !== auth_user_id()): ?>
                    <form method="POST" action="/users/<?= (int) $user['id'] ?>/delete" onsubmit="return confirm('Delete this user?');">
                        <button class="btn" type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>