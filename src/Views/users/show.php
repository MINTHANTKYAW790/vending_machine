<h1><?= e($user['username']) ?></h1>

<p><strong>Email:</strong> <?= e($user['email']) ?></p>
<p><strong>Role:</strong> <?= e($user['role']) ?></p>
<p><strong>Created At:</strong> <?= e($user['created_at']) ?></p>
<p><strong>Updated At:</strong> <?= e($user['updated_at']) ?></p>
<p>
    <a class="btn" href="/users/<?= (int) $user['id'] ?>/edit">Edit</a>
    <a class="btn" href="/users">Back</a>
</p>