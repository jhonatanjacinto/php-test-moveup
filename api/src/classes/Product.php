<?php

class Product implements JsonSerializable
{
    public function __construct(
        public int $id = 0,
        public ?string $name = null,
        public ?string $description = null,
        public float $price = 0.0,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {}

    public function validate(): void {
        if (empty(trim($this->name))) {
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
            price: (float) ($data?->price ?? 0.0),
            created_at: !empty($data?->created_at) ? new DateTime($data->created_at) : null,
            updated_at: !empty($data?->updated_at) ? new DateTime($data->updated_at) : null,
        );

        $p->validate();

        return $p;
    }

    public function jsonSerialize(): array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "created_at" => $this->created_at?->format("Y-m-d H:i:s"),
            "updated_at" => $this->updated_at?->format("Y-m-d H:i:s"),
        ];
    }
}