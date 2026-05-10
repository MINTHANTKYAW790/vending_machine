<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Route;
use App\Middleware\RoleMiddleware;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductValidator;
use App\Services\PurchaseService;
use RuntimeException;

final class ProductsController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly PurchaseService $purchaseService,
        private readonly ProductValidator $validator
    ) {
    }

    public function publicIndex(Request $request): void
    {
        $this->index($request);
    }

    public function index(Request $request): void
    {
        RoleMiddleware::ensureAnyRole(['Admin', 'User']);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;
        $sort = (string) $request->query('sort', 'id');
        $dir = (string) $request->query('dir', 'asc');
        $rows = $this->products->paginate($page, $perPage, $sort, $dir);
        $total = $this->products->countAll();
        $pages = (int) ceil($total / $perPage);

        $this->view('products.index', [
            'products' => $rows,
            'page' => $page,
            'pages' => $pages,
            'sort' => $sort,
            'dir' => strtolower($dir),
            'isAdmin' => is_admin(),
            'flashSuccess' => flash('success'),
            'flashError' => flash('error'),
        ]);
    }

    public function show(Request $request, string $id): void
    {
        RoleMiddleware::ensureAnyRole(['Admin', 'User']);
        $product = $this->products->findById((int) $id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        $this->view('products.show', ['product' => $product]);
    }

    public function create(): void
    {
        RoleMiddleware::ensureRole('Admin');
        $this->view('products.create', [
            'errors' => [],
            'old' => ['name' => '', 'price' => '', 'quantity_available' => ''],
        ]);
    }

    public function store(Request $request): void
    {
        RoleMiddleware::ensureRole('Admin');
        $input = [
            'name' => trim((string) $request->input('name')),
            'price' => (string) $request->input('price'),
            'quantity_available' => (string) $request->input('quantity_available'),
        ];

        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            $this->view('products.create', ['errors' => $errors, 'old' => $input]);
            return;
        }

        $this->products->create([
            'name' => $input['name'],
            'price' => number_format((float) $input['price'], 3, '.', ''),
            'quantity_available' => (int) $input['quantity_available'],
        ]);
        flash('success', 'Product created successfully.');
        redirect('/products');
    }

    public function edit(Request $request, string $id): void
    {
        RoleMiddleware::ensureRole('Admin');
        $product = $this->products->findById((int) $id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }
        $this->view('products.edit', ['product' => $product, 'errors' => []]);
    }

    public function update(Request $request, string $id): void
    {
        RoleMiddleware::ensureRole('Admin');
        $input = [
            'name' => trim((string) $request->input('name')),
            'price' => (string) $request->input('price'),
            'quantity_available' => (string) $request->input('quantity_available'),
        ];

        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            $product = array_merge($input, ['id' => (int) $id]);
            $this->view('products.edit', ['product' => $product, 'errors' => $errors]);
            return;
        }

        $this->products->update((int) $id, [
            'name' => $input['name'],
            'price' => number_format((float) $input['price'], 3, '.', ''),
            'quantity_available' => (int) $input['quantity_available'],
        ]);

        flash('success', 'Product updated successfully.');
        redirect('/products');
    }

    public function destroy(Request $request, string $id): void
    {
        RoleMiddleware::ensureRole('Admin');
        $this->products->delete((int) $id);
        flash('success', 'Product deleted successfully.');
        redirect('/products');
    }

    public function showPurchase(Request $request, string $id): void
    {
        RoleMiddleware::ensureAnyRole(['Admin', 'User']);
        $product = $this->products->findById((int) $id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }
        $this->view('products.purchase', [
            'product' => $product,
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    #[Route('POST', '/products/{id}/purchase', name: 'products.purchase')]
    public function purchase(Request $request, string $id): void
    {
        RoleMiddleware::ensureRole('User');
        $quantity = (int) $request->input('quantity', 1);
        $productId = (int) $id;
        try {
            $result = $this->purchaseService->purchase((int) auth_user_id(), $productId, $quantity);
            flash('success', sprintf(
                'Purchase successful. Ref: %s, total: %s USD',
                $result['transaction_ref'],
                $result['total_price']
            ));
        } catch (RuntimeException $exception) {
            flash('error', $exception->getMessage());
        }

        redirect('/products/' . $productId . '/purchase');
    }
}
