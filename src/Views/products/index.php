<h1>Products</h1>

<?php if (!empty($flashError)): ?><div class="alert alert-error"><?= e($flashError) ?></div><?php endif; ?>
<?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>

<?php if ($isAdmin): ?>
    <p><a class="btn btn-primary" href="/products/create">Add Product</a></p>
<?php endif; ?>

<?php
    $toggleDir = strtolower($dir) === 'asc' ? 'desc' : 'asc';
    $sortLink = static function (string $column) use ($page, $toggleDir): string {
        return '/products?page=' . $page . '&sort=' . urlencode($column) . '&dir=' . $toggleDir;
    };
    $sortIcon = static function (string $column) use ($sort, $dir): string {
        if ($sort !== $column) {
            return '';
        }
        return strtolower($dir) === 'asc' ? ' ▲' : ' ▼';
    };
?>

<table>
    <thead>
    <tr>
        <th><a href="<?= e($sortLink('id')) ?>">ID<?= e($sortIcon('id')) ?></a></th>
        <th><a href="<?= e($sortLink('name')) ?>">Name<?= e($sortIcon('name')) ?></a></th>
        <th><a href="<?= e($sortLink('price')) ?>">Price<?= e($sortIcon('price')) ?></a></th>
        <th><a href="<?= e($sortLink('quantity_available')) ?>">Quantity<?= e($sortIcon('quantity_available')) ?></a></th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>   
        <tr>
            <td><?= (int) $product['id'] ?></td>
            <td><?= e($product['name']) ?></td>
            <td><?= number_format((float) $product['price'], 3) ?> USD</td>
            <td><?= (int) $product['quantity_available'] ?></td>
            <td class="actions">
                <a class="btn" href="/products/<?= (int) $product['id'] ?>-<?= urlencode(strtolower($product['name'])) ?>">View</a>
                <?php if (!$isAdmin): ?>
                    <a class="btn btn-primary" href="/products/<?= (int) $product['id'] ?>/purchase">Purchase</a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <a class="btn" href="/products/<?= (int) $product['id'] ?>/edit">Edit</a>
                    <form method="POST" action="/products/<?= (int) $product['id'] ?>/delete" onsubmit="return confirm('Delete this product?');">
                        <button class="btn" type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination" style="margin-top: 14px;">
    <?php for ($i = 1; $i <= max(1, $pages); $i++): ?>
        <?php if ($i === $page): ?>
            <strong><?= $i ?></strong>
        <?php else: ?>
            <a href="/products?page=<?= $i ?>&sort=<?= e($sort) ?>&dir=<?= e($dir) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
