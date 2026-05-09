<h1>Purchase <?= e($product['name']) ?></h1>

<?php if (!empty($error)): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if (!empty($success)): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<p><strong>Unit price:</strong> <?= number_format((float) $product['price'], 3) ?> USD</p>
<p><strong>Available:</strong> <?= (int) $product['quantity_available'] ?></p>

<form method="POST" action="/products/<?= (int) $product['id'] ?>/purchase">
    <div class="field">
        <label for="quantity">Quantity</label>
        <input id="quantity" type="number" name="quantity" min="1" max="<?= (int) $product['quantity_available'] ?>" required>
    </div>
    <button class="btn btn-primary" type="submit">Confirm Purchase</button>
    <a class="btn" href="/products">Back</a>
</form>
