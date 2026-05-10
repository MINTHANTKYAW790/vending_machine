<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\ProductsController;
use App\Core\Request;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductValidator;
use App\Services\PurchaseService;
use App\Support\RedirectException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ProductsControllerTest extends TestCase
{
    private ProductRepositoryInterface $products;
    private PurchaseService $purchaseService;
    private ProductValidator $validator;
    private ProductsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        if (!defined('APP_TESTING')) {
            define('APP_TESTING', true);
        }

        $_SESSION = [
            'user_id' => 1,
            'role' => 'Admin',
            'username' => 'admin',
        ];

        $this->products = $this->createMock(ProductRepositoryInterface::class);
        $this->purchaseService = $this->createMock(PurchaseService::class);
        $this->validator = $this->createMock(ProductValidator::class);
        $this->controller = new ProductsController($this->products, $this->purchaseService, $this->validator);
    }

    public function testStoreCreatesProductAndRedirects(): void
    {
        $input = ['name' => 'Sprite', 'price' => '2.500', 'quantity_available' => '7'];
        $request = new Request('POST', '/products', [], $input, []);

        $this->validator->expects(self::once())->method('validate')->with($input)->willReturn([]);
        $this->products->expects(self::once())->method('create')->with([
            'name' => 'Sprite',
            'price' => '2.500',
            'quantity_available' => 7,
        ])->willReturn(10);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /products');
        $this->controller->store($request);
    }

    public function testStoreValidationFailureDoesNotCreate(): void
    {
        $input = ['name' => '', 'price' => '-1', 'quantity_available' => '-2'];
        $request = new Request('POST', '/products', [], $input, []);

        $this->validator->expects(self::once())->method('validate')->willReturn([
            'name' => 'Name is required.',
        ]);
        $this->products->expects(self::never())->method('create');

        ob_start();
        $this->controller->store($request);
        ob_end_clean();
        self::assertTrue(true);
    }

    public function testPurchaseSuccessRedirects(): void
    {
        $_SESSION['role'] = 'User';
        $request = new Request('POST', '/products/1/purchase', [], ['quantity' => '2'], []);

        $this->purchaseService->expects(self::once())->method('purchase')->with(1, 1, 2)->willReturn([
            'transaction_ref' => 'TX-001',
            'total_price' => '7.980',
        ]);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /products/1/purchase');
        $this->controller->purchase($request, '1');
    }

    public function testPurchaseWithZeroQuantity(): void
    {
        $_SESSION['role'] = 'User';
        $request = new Request('POST', '/products/1/purchase', [], ['quantity' => '0'], []);

        $this->purchaseService->expects(self::once())->method('purchase')->with(1, 1, 0)->willReturn([
            'transaction_ref' => 'TX-001',
            'total_price' => '0.000',
        ]);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /products/1/purchase');
        $this->controller->purchase($request, '1');
    }

    public function testCreateRequiresAdminRole(): void
    {
        $_SESSION['role'] = 'User';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Forbidden');
        $this->controller->create();
    }

    public function testIndexWithPagination(): void
    {
        $_SESSION['role'] = 'User';
        $request = new Request('GET', '/products', ['page' => '2', 'sort' => 'name', 'dir' => 'desc'], [], []);

        $this->products->expects(self::once())->method('paginate')->with(2, 10, 'name', 'desc')->willReturn([]);
        $this->products->expects(self::once())->method('countAll')->willReturn(25);

        ob_start();
        $this->controller->index($request);
        $output = ob_get_clean();
        self::assertStringContainsString('Products', $output);
    }

    public function testShowDisplaysProduct(): void
    {
        $_SESSION['role'] = 'User';
        $product = ['id' => 1, 'name' => 'Coke', 'price' => '1.500', 'quantity_available' => 10];

        $this->products->expects(self::once())->method('findById')->with(1)->willReturn($product);

        ob_start();
        $this->controller->show(new Request('GET', '/products/1', [], [], []), '1');
        $output = ob_get_clean();
        self::assertStringContainsString('Coke', $output);
    }

    public function testShowWithInvalidId(): void
    {
        $_SESSION['role'] = 'User';

        $this->products->expects(self::once())->method('findById')->with(0)->willReturn(null);

        ob_start();
        $this->controller->show(new Request('GET', '/products/abc', [], [], []), 'abc');
        $output = ob_get_clean();
        self::assertStringContainsString('Product not found', $output);
    }

    public function testEditRequiresAdmin(): void
    {
        $_SESSION['role'] = 'User';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Forbidden');
        $this->controller->edit(new Request('GET', '/products/1/edit', [], [], []), '1');
    }

    public function testEditNotFound(): void
    {
        $this->products->expects(self::once())->method('findById')->with(999)->willReturn(null);

        ob_start();
        $this->controller->edit(new Request('GET', '/products/999/edit', [], [], []), '999');
        $output = ob_get_clean();
        self::assertStringContainsString('Product not found', $output);
    }

    public function testUpdateSuccess(): void
    {
        $input = ['name' => 'Updated Coke', 'price' => '2.000', 'quantity_available' => '15'];
        $request = new Request('POST', '/products/1/update', [], $input, []);

        $this->validator->expects(self::once())->method('validate')->with($input)->willReturn([]);
        $this->products->expects(self::once())->method('update')->with(1, [
            'name' => 'Updated Coke',
            'price' => '2.000',
            'quantity_available' => 15,
        ]);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /products');
        $this->controller->update($request, '1');
    }

    public function testUpdateValidationFailure(): void
    {
        $input = ['name' => '', 'price' => 'invalid', 'quantity_available' => '-1'];
        $request = new Request('POST', '/products/1/update', [], $input, []);

        $this->validator->expects(self::once())->method('validate')->willReturn(['name' => 'Required']);
        $this->products->expects(self::never())->method('update');

        ob_start();
        $this->controller->update($request, '1');
        ob_end_clean();
        self::assertTrue(true);
    }

    public function testDestroyRequiresAdmin(): void
    {
        $_SESSION['role'] = 'User';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Forbidden');
        $this->controller->destroy(new Request('POST', '/products/1/delete', [], [], []), '1');
    }

    public function testDestroyDeletesAndRedirects(): void
    {
        $this->products->expects(self::once())->method('delete')->with(1);

        $this->expectException(RedirectException::class);
        $this->expectExceptionMessage('Redirect to /products');
        $this->controller->destroy(new Request('POST', '/products/1/delete', [], [], []), '1');
    }

    public function testShowPurchaseNotFound(): void
    {
        $_SESSION['role'] = 'User';

        $this->products->expects(self::once())->method('findById')->with(999)->willReturn(null);

        ob_start();
        $this->controller->showPurchase(new Request('GET', '/products/999/purchase', [], [], []), '999');
        $output = ob_get_clean();
        self::assertStringContainsString('Product not found', $output);
    }

    public function testPublicIndexSameAsIndex(): void
    {
        $_SESSION['role'] = 'User';
        $request = new Request('GET', '/', [], [], []);

        $this->products->expects(self::once())->method('paginate')->with(1, 10, 'id', 'asc')->willReturn([]);
        $this->products->expects(self::once())->method('countAll')->willReturn(0);

        ob_start();
        $this->controller->publicIndex(new Request('GET', '/', [], [], []));
        $output = ob_get_clean();
        self::assertStringContainsString('Products', $output);
    }
}
