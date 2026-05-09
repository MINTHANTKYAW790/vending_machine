<?php

declare(strict_types=1);

namespace App\Services;

class ProductValidator
{
    public function validate(array $input): array
    {
        $errors = [];

        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        $price = $input['price'] ?? null;
        if ($price === null || $price === '') {
            $errors['price'] = 'Price is required.';
        } elseif (!is_numeric((string) $price) || (float) $price <= 0) {
            $errors['price'] = 'Price must be a positive number.';
        }

        $quantity = $input['quantity_available'] ?? null;
        if ($quantity === null || $quantity === '') {
            $errors['quantity_available'] = 'Quantity is required.';
        } elseif (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 0) {
            $errors['quantity_available'] = 'Quantity must be a non-negative integer.';
        }

        return $errors;
    }
}
