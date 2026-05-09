<h1>Transactions</h1>

<p>
    <?= $isAdmin ? 'Showing all transactions (Admin view).' : 'Showing your transactions.' ?>
</p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <?php if ($isAdmin): ?>
            <th>User</th>
        <?php endif; ?>
        <th>Product</th>
        <th>Qty</th>
        <th>Unit Price</th>
        <th>Total</th>
        <th>Reference</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($transactions)): ?>
        <tr>
            <td colspan="<?= $isAdmin ? 8 : 7 ?>">No transactions found.</td>
        </tr>
    <?php endif; ?>
    <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= (int) $transaction['id'] ?></td>
            <?php if ($isAdmin): ?>
                <td><?= e((string) ($transaction['username'] ?? '')) ?></td>
            <?php endif; ?>
            <td><?= e((string) ($transaction['product_name'] ?? $transaction['product_id'])) ?></td>
            <td><?= (int) $transaction['quantity'] ?></td>
            <td><?= number_format((float) $transaction['unit_price'], 3) ?> USD</td>
            <td><?= number_format((float) $transaction['total_price'], 3) ?> USD</td>
            <td><?= e((string) $transaction['transaction_ref']) ?></td>
            <td><?= e((string) $transaction['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
