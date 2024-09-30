<?php

/**
 * Starts a database connection
 * @return PDO|null
 */
function db_connect(): ?PDO {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        return $pdo;
    } catch (PDOException $e) {
        echo "DB Connection failed: " . $e->getMessage();
    }
    return null;
}

/**
 * Queries the database and returns a single object/result
 * @param string $query             The SQL query to execute
 * @param array $params             The parameters to bind
 * @return bool|object|null
 */
function db_query_one(string $query, array $params = []): null|object|bool {
    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchObject();
    } catch (PDOException $e) {
        echo "db_query_one: " . $e->getMessage();
    } finally {
        $pdo = null;
    }
    return null;
}

/**
 * Queries the database and returns multiple objects/results
 * @param string $query         The SQL query to execute
 * @param array $params         The parameters to bind
 * @return array
 */
function db_query_many(string $query, array $params = []): array {
    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "db_query_many: " . $e->getMessage();
    } finally {
        $pdo = null;
    }
    return [];
}

/**
 * Executes non-query SQL statements like INSERT, UPDATE, DELETE
 * @param string $query             The SQL query to execute
 * @param array $params             The parameters to bind
 * @param bool $return_id           Whether to return the last inserted ID
 * @return bool|string
 */
function db_execute(string $query, array $params = [], bool $return_id = false): bool|string {
    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute($params);
        $last_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        echo "db_execute: ". $e->getMessage();
    } finally {
        $pdo = null;
    }
    return $return_id ? $last_id : $result;
}