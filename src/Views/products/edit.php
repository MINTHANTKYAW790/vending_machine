<h1>Edit Product</h1>

<form method="POST" action="/products/<?= (int) $product['id'] ?>/update" onsubmit="return validateProductForm(this);">
    <?php require __DIR__ . '/_form.php'; ?>
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn" href="/products">Cancel</a>
</form>
