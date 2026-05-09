<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vending Machine</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f6f8fb; color: #222; }
        nav { background: #1f2937; color: #fff; padding: 12px 18px; display:flex; justify-content:space-between; }
        nav a { color: #fff; text-decoration: none; margin-right: 12px; }
        .container { max-width: 980px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .btn { border: 1px solid #4b5563; background: #fff; border-radius: 4px; padding: 6px 10px; text-decoration:none; color:#111; cursor:pointer; }
        .btn-primary { background:#2563eb; border-color:#2563eb; color:#fff; }
        .alert { padding:10px; border-radius:4px; margin: 10px 0; }
        .alert-error { background:#fee2e2; color:#991b1b; }
        .alert-success { background:#dcfce7; color:#166534; }
        .field { margin-bottom: 12px; }
        .field label { display:block; margin-bottom: 4px; }
        .field input { width: 100%; padding:8px; box-sizing:border-box; }
        .error { color:#b91c1c; font-size: 0.9rem; }
        .actions { display:flex; gap:8px; }
        .pagination a { margin-right: 8px; text-decoration: none; }
    </style>
</head>
<body>
<nav>
    <div>
        <a href="/products">Products</a>
        <?php if (auth_user_id()): ?>
            <a href="/transactions">Transactions</a>
        <?php endif; ?>
    </div>
    <div>
        <?php if (auth_user_id()): ?>
            <span><?= e((string) ($_SESSION['username'] ?? '')) ?> (<?= e((string) auth_role()) ?>)</span>
            <form action="/logout" method="POST" style="display:inline;">
                <button type="submit" class="btn">Logout</button>
            </form>
        <?php else: ?>
            <a href="/login">Login</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
