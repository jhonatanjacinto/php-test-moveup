<?php

/**
 * Configures the headers for the API
 */
config_headers();
function config_headers()
{
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");
}

/**
 * Handles a GET request
 * @param string $path              The path to handle
 * @param callable $callback        The callback to execute
 * @return void
 */
function handle_get(string $path, callable $callback = null)
{
    handle_request("GET", $path, $callback);
}

/**
 * Handles a POST request
 * @param string $path              The path to handle
 * @param callable $callback        The callback to execute
 * @return void
 */
function handle_post(string $path, callable $callback = null)
{
    handle_request("POST", $path, $callback);
}

/**
 * Handles a PUT request
 * @param string $path              The path to handle
 * @param callable $callback        The callback to execute
 * @return void
 */
function handle_put(string $path, callable $callback = null)
{
    handle_request("PUT", $path, $callback);
}

/**
 * Handles a DELETE request
 * @param string $path              The path to handle
 * @param callable $callback        The callback to execute
 * @return void
 */
function handle_delete(string $path, callable $callback = null)
{
    handle_request("DELETE", $path, $callback);
}

/**
 * Handles any request and processes it
 * @param string $method            The request method
 * @param string $path              The path to handle
 * @param callable $callback        The callback to execute
 * @return void
 */
function handle_request(string $method, string $path, callable $callback = null)
{
    // if the request method does not match, return
    if ($_SERVER["REQUEST_METHOD"] !== $method) {
        return;
    }

    $path = trim($path, "/");
    $requestPath = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
    $pathParts = explode("/", $path);
    $requestPathParts = explode("/", $requestPath);

    // if the number of path parts do not match, return
    if (count($pathParts) !== count($requestPathParts)) {
        return;
    }

    $params = [];
    foreach ($pathParts as $key => $part) {
        if ($part[0] === "{") {
            $params[substr($part, 1, -1)] = $requestPathParts[$key];
        } else if ($part !== $requestPathParts[$key]) {
            return;
        }
    }

    // if the method is POST or PUT, get the body to pass to the callback
    $body = null;
    if ($method === "POST" || $method === "PUT") {
        $body = json_decode(file_get_contents("php://input"));
    }

    if ($callback) {
        $callback($params, $body);
    }

    exit;
}

/**
 * Sends a JSON response with a success status code
 * @param int $status_code          The status code
 * @param mixed $data               The data to send
 * @return never
 */
function json_success(int $status_code = 200, mixed $data = null)
{
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

/**
 * Sends a JSON response with an error status code
 * @param int $status_code          The status code
 * @param string $message           The error message
 * @return never
 */
function json_error(int $status_code, string $message = "")
{
    http_response_code($status_code);
    echo json_encode(["error" => $message]);
    exit;
}