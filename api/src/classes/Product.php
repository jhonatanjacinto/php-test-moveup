<?php

class Product 
{
    public function __construct(
        public int $id = 0,
        public ?string $name = null,
        public ?string $description = null,
        public float $price = 0.0,
    ) {}

    public function validate(): void {
        if (empty($this->name)) {
            throw new Exception("Product name is required");
        }

        if ($this->price <= 0) {
            throw new Exception("Product price must be greater than zero");
        }
    }

    public static function validateParseDataType(mixed $data): void {
        if (is_scalar($data) || is_null($data)) {
            throw new Exception("Invalid data type for product parsing");
        }
    }

    public static function parse(mixed $data): Product {
        self::validateParseDataType($data);

        $data = (object) $data;

        $p = new Product(
            id: $data?->id ?? 0,
            name: $data?->name ?? null,
            description: $data?->description ?? null,
            price: (float) $data?->price ?? 0.0,
        );

        $p->validate();

        return $p;
    }
}