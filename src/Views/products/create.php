<h1>Create Product</h1>

<form method="POST" action="/products" onsubmit="return validateProductForm(this);">
    <?php require __DIR__ . '/_form.php'; ?>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn" href="/products">Cancel</a>
</form>
