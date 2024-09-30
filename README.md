# PHP Test

## Overview
The current application is dockerized, so it requires Docker Engine to run. To set everything up, download this repository and inside the repo main directory run `docker-compose up` in the command line.

After docker install and configure the necessary containers, just access http://localhost:8000/api/products to see the API running, for example.

## Task 1: PHP RESTful API
- All API endpoints are under `http://localhost:8000/api`;
- You can use Postman to make requests to the mentioned URL. For example, to see all products, access `http://localhost:8000/api/products`;
- To UPDATE or INSERT a new product, the JSON object that must be sent has to have the following shape:
```json
{
    "name": "Product Name",
    "description": "Description of the product",
    "price": 100.00
}
```
- ID of the product (for all the operations that requires it) should be provided via the URL path. Ex: `http://localhost:8000/api/products/1`

## Task 2: Handle Complex Data Structures
Solution can be spotted in `http://localhost:8000/array-flat/index.php` file inside this repository with 4 test cases. This is the resulting function to flatten the sample array:
```php
function array_flat_values_and_keys(array $arr): array
{
    $arr_values = [];

    foreach ($arr as $key => $value) {
        if (!array_is_list($arr)) {
            $arr_values[] = $key;
        }

        if (is_array($value)) {
            $arr_values = array_merge($arr_values, array_flat_values_and_keys($value));
            continue;
        }

        $arr_values[] = $value;
    }

    return $arr_values;
}
```

## Task 3: WordPress Plugin Development
(UNDER DEVELOPMENT)

## Task 4: Optimize WordPress Performance
Given the proposed scenario, I would go with the following approaches:
- Write and execute a custom optimized SQL statement using `$wpdb->get_results()` to retrieve the necessary data in a more efficient way;
- As a simple/isolated caching solution, save these 10 most recent posts to a file in the file system (example a .json) every time posts are modified (inserted, updated or deleted) and then, instead of reading from the database, the application would read from this file in the file system;
- If possible/viable, use an overall efficient caching policy (via a plugin or another type of resource) application-wide to avoid unnecessary access to the database improving performance as a whole.

## Task 5: Bonus Task
The refactored function based on the example provided should look like the following:
```php
function calculateDiscountRefactored(User $user, float|int $totalAmount): float|int
{
    if (!$user->isMember())
        return 0;

    return match ($user->membershipLevel) {
        'gold' => $totalAmount > 100 ? $totalAmount * 0.2 : $totalAmount * 0.1,
        'silver' => $totalAmount * 0.1,
        default => 0,
    };
}
```
- PHP 8 introduced the `match` expression which simplifies conditional checking in the language (when compared to `switch` statements, for example);
- Having what was stated above in mind, the refactored function shows clearly its intention. If the user is NOT a member, we early return the value ZERO and the function execution stops there;
- Now, if the user IS a member, then we must evaluate its `membershipLevel` applying specific rules depending on the value it contains: if it's `gold` and the `$totalAmount` is higher than 100, then applies 20% of discount, otherwhise applies 10%;
- For `silver` members, it's always 10%;
- Invalid membership levels would return ZERO (stated in the `default` case).
- Additionally, specifying the types explicitly in the arguments and the function return signature, helps even more in the clarity of the code's goal and to catch common type errors early (improving code quality).