<h1><?= e($product['name']) ?></h1>

<p><strong>Price:</strong> <?= number_format((float) $product['price'], 3) ?> USD</p>
<p><strong>Quantity Available:</strong> <?= (int) $product['quantity_available'] ?></p>
<p>
    <?php if (!is_admin()): ?>
        <a class="btn btn-primary" href="/products/<?= (int) $product['id'] ?>/purchase">Purchase</a>
    <?php endif; ?>
    <a class="btn" href="/products">Back</a>
</p>
