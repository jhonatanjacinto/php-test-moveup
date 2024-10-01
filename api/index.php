<?php

/**
 * Imports the required files
 */
require "src/config.php";

/**
 * Displays all products from the database
 */
handle_get("/api/products", function () {
    try {
        $products = array_map(fn ($product) => Product::parse($product), db_query_many("SELECT * FROM tbl_products"));
        json_success(200, $products);
    } catch (Exception $e) {
        json_error(500, $e->getMessage());
    }
});

/**
 * Displays a single product from the database by its ID
 */
handle_get("/api/products/{id}", function (array $params) {
    try {
        $id = (int) $params["id"];
        $product = db_query_one("SELECT * FROM tbl_products WHERE id = ?", [$id]);
        if (! $product) {
            json_error(404, "Product not found");
        }
        $product = Product::parse($product);
        json_success(200, $product);
    } catch (Exception $e) {
        json_error(500, $e->getMessage());
    }
});

/**
 * Creates a new product
 */
handle_post("/api/products", function (array $params, mixed $body) {
    try {
        $product = Product::parse($body);
        $product_id = db_execute("INSERT INTO tbl_products (name, description, price) VALUES (?, ?, ?)", [$product->name, $product->description, $product->price], true);
        
        if (! $product_id) {
            json_error(500, "Failed to create product");
        }

        $product->id = $product_id;
        $product->created_at = new DateTime();
        $product->updated_at = new DateTime();
        json_success(201, $product);
    } catch (Exception $e) {
        json_error(400, $e->getMessage());
    }
});

/**
 * Updates a product by its ID
 */
handle_put("/api/products/{id}", callback: function (array $params, mixed $body) {
    try {
        $id = (int) $params["id"];
        if ($id <= 0) {
            json_error(400, "Invalid product ID");
        }

        $current_product = db_query_one("SELECT * FROM tbl_products WHERE id = ?", [$id]);
        if (! $current_product) {
            json_error(404, "Product not found");
        }

        Product::validateParseDataType($body);
        $merged_product = (object) array_merge((array) $current_product, (array) $body);
        $product = Product::parse($merged_product);
        $status = db_execute("UPDATE tbl_products SET name = ?, description = ?, price = ? WHERE id = ?", [$product->name, $product->description, $product->price, $product->id]);
        
        if (! $status) {
            json_error(500, "Failed to update product");
        }

        $product->id = $id;
        $product->updated_at = new DateTime();
        json_success(200, $product);
    } catch (Exception $e) {
        json_error(500, $e->getMessage());
    }
});

/**
 * Deletes a product by its ID in the database
 */
handle_delete("/api/products/{id}", function (array $params) {
    try {
        $id = (int) $params["id"];
        if ($id <= 0) {
            json_error(400, "Invalid product ID");
        }

        $status = db_execute("DELETE FROM tbl_products WHERE id = ?", [$id]);
        
        if (! $status) {
            json_error(500, "Failed to delete product");
        }

        json_success(204);
    } catch (Exception $e) {
        json_error(500, $e->getMessage());
    }
});

/**
 * If no route was matched, return a 404 error
 */
json_error(404, "Not found");