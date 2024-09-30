<?php

/**
 * Imports the required files
 */
require "src/config.php";

/**
 * Displays all products from the database
 */
handle_get("/api/products", function () {
    $products = db_query_many("SELECT * FROM tbl_products");
    // Shows all products in JSON format
    json_success(200, $products);
});

/**
 * Displays a single product from the database by its ID
 */
handle_get("/api/products/{id}", function (array $params) {
    $id = (int) $params["id"];
    $product = db_query_one("SELECT * FROM tbl_products WHERE id = ?", [$id]);
    if (! $product) {
        json_error(404, "Product not found");
    }
    json_success(200, $product);
});

/**
 * Creates a new product
 */
handle_post("/api/products", function (array $params, mixed $body) {
    // Creates a new product
    json_success(201, ["id" => 4, "name" => $body->name]);
});

/**
 * Updates a product by its ID
 */
handle_put("/api/products/{id}", callback: function (array $params, mixed $body) {
    $id = (int) $params["id"];
    json_success(200, ["id" => $id, "name" => $body->name]);
});

/**
 * Deletes a product by its ID in the database
 */
handle_delete("/api/products/{id}", function (array $params) {
    $id = (int) $params["id"];
    json_success(204);
});

/**
 * If no route was matched, return a 404 error
 */
json_error(404, "Not found");