<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Core\Request;
use App\Middleware\JwtMiddleware;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductValidator;
use App\Services\PurchaseService;
use RuntimeException;

final class ProductsApiController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ProductValidator $validator,
        private readonly PurchaseService $purchaseService,
        private readonly JwtMiddleware $jwtMiddleware
    ) {
    }

    private function auth(Request $request): ?array
    {
        $payload = $this->jwtMiddleware->authenticate($request);
        if (!$payload) {
            $this->json(['message' => 'Unauthorized'], 401);
            return null;
        }
        return $payload;
    }

    private function requireAdmin(array $payload): bool
    {
        if (($payload['role'] ?? null) !== 'Admin') {
            $this->json(['message' => 'Forbidden'], 403);
            return false;
        }
        return true;
    }

    public function index(Request $request): void
    {
        if (!$this->auth($request)) {
            return;
        }
        $page = max(1, (int) $request->query('page', 1));
        $products = $this->products->paginate($page, 20, (string) $request->query('sort', 'id'), (string) $request->query('dir', 'asc'));
        $this->json(['data' => $products, 'page' => $page]);
    }

    public function show(Request $request, string $id): void
    {
        if (!$this->auth($request)) {
            return;
        }
        $product = $this->products->findById((int) $id);
        if (!$product) {
            $this->json(['message' => 'Not found'], 404);
            return;
        }
        $this->json(['data' => $product]);
    }

    public function store(Request $request): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }

        $data = $request->all();
        $errors = $this->validator->validate($data);
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }
        $id = $this->products->create([
            'name' => trim((string) $data['name']),
            'price' => number_format((float) $data['price'], 3, '.', ''),
            'quantity_available' => (int) $data['quantity_available'],
        ]);
        $this->json(['id' => $id], 201);
    }

    public function update(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }

        $data = $request->all();
        $errors = $this->validator->validate($data);
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $this->products->update((int) $id, [
            'name' => trim((string) $data['name']),
            'price' => number_format((float) $data['price'], 3, '.', ''),
            'quantity_available' => (int) $data['quantity_available'],
        ]);
        $this->json(['message' => 'Updated']);
    }

    public function destroy(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }
        $this->products->delete((int) $id);
        $this->json(['message' => 'Deleted']);
    }

    public function purchase(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload) {
            return;
        }
        $quantity = (int) $request->input('quantity', 1);
        try {
            $result = $this->purchaseService->purchase((int) $payload['sub'], (int) $id, $quantity);
            $this->json(['data' => $result], 201);
        } catch (RuntimeException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
        }
    }
}
