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

    public function testPurchaseInsufficientStockRedirectsWithError(): void
    {
        $_SESSION['role'] = 'User';
        $request = new Request('POST', '/products/1/purchase', [], ['quantity' => '1000'], []);

        $this->purchaseService->expects(self::once())
            ->method('purchase')
            ->willThrowException(new RuntimeException('Insufficient stock.'));

        try {
            $this->controller->purchase($request, '1');
            self::fail('Expected redirect exception');
        } catch (RedirectException $exception) {
            self::assertSame('/products/1/purchase', $exception->path);
            self::assertSame('Insufficient stock.', $_SESSION['_flash']['error'] ?? null);
        }
    }

    public function testCreateRequiresAdminRole(): void
    {
        $_SESSION['role'] = 'User';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Forbidden');
        $this->controller->create();
    }
}
