<script>
function validateProductForm(form) {
    const name = form.querySelector('[name="name"]').value.trim();
    const price = parseFloat(form.querySelector('[name="price"]').value);
    const quantity = parseInt(form.querySelector('[name="quantity_available"]').value, 10);
    let ok = true;

    const setError = (field, message) => {
        const target = document.getElementById(field + '-client-error');
        if (target) target.textContent = message;
    };

    setError('name', '');
    setError('price', '');
    setError('quantity_available', '');

    if (!name) {
        setError('name', 'Name is required.');
        ok = false;
    }
    if (isNaN(price) || price <= 0) {
        setError('price', 'Price must be positive.');
        ok = false;
    }
    if (isNaN(quantity) || quantity < 0) {
        setError('quantity_available', 'Quantity must be non-negative.');
        ok = false;
    }
    return ok;
}
</script>

<div class="field">
    <label for="name">Name</label>
    <input id="name" type="text" name="name" value="<?= e((string) ($old['name'] ?? $product['name'] ?? '')) ?>" required>
    <div class="error"><?= e((string) ($errors['name'] ?? '')) ?></div>
    <div id="name-client-error" class="error"></div>
</div>

<div class="field">
    <label for="price">Price</label>
    <input id="price" type="number" step="0.001" min="0.001" name="price" value="<?= e((string) ($old['price'] ?? $product['price'] ?? '')) ?>" required>
    <div class="error"><?= e((string) ($errors['price'] ?? '')) ?></div>
    <div id="price-client-error" class="error"></div>
</div>

<div class="field">
    <label for="quantity_available">Quantity Available</label>
    <input id="quantity_available" type="number" min="0" name="quantity_available" value="<?= e((string) ($old['quantity_available'] ?? $product['quantity_available'] ?? '')) ?>" required>
    <div class="error"><?= e((string) ($errors['quantity_available'] ?? '')) ?></div>
    <div id="quantity_available-client-error" class="error"></div>
</div>
