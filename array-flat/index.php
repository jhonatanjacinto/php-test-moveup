<?php

# Test Case 1: Categories
$categories = [
    'Electronics' => [
        'Phones' => ['Smartphones', 'Feature phones'],
        'Laptops' => [],
    ],
    'Furniture' => [
        'Beds' => [],
        'Chairs' => ['Office chairs', 'Dining chairs'],
    ],
];

# Test Case 2: Categories with empty sub-categories
$categoriesWithEmptySubcategories = [
    'Electronics' => [
        'Phones' => [],
        'Laptops' => [],
    ],
    'Furniture' => [
        'Beds' => [],
        'Chairs' => [],
    ],
];

# Test Case 3: Categories 3 levels deep
$categoriesThreeLevelsDeep = [
    'Electronics' => [
        'Phones' => [
            'Smartphones' => ['Samsung', 'Apple'],
            'Feature phones' => ['Nokia', 'Samsung'],
        ],
        'Laptops' => [],
    ],
    'Furniture' => [
        'Beds' => [],
        'Chairs' => ['Office chairs', 'Dining chairs'],
    ],
];

# Test Case 4: One level deep
$categoriesOneLevelDeep = [
    'Electronics' => ['Phones', 'Laptops'],
    'Furniture' => ['Beds', 'Chairs'],
];

/**
 * Check the test cases
 */
$result = array_flat_values_and_keys($categories);
$result2 = array_flat_values_and_keys($categoriesWithEmptySubcategories);
$result3 = array_flat_values_and_keys($categoriesThreeLevelsDeep);
$result4 = array_flat_values_and_keys($categoriesOneLevelDeep);

/**
 * If the script is run from the command line, use a line break character for new lines
 * Otherwise, use the HTML line break tag
 */
$lineBreak = php_sapi_name() === 'cli' ? "\n" : "<br>";

echo "Test Case 1: ";
print_r($result);
echo $lineBreak;
echo "Test Case 2: ";
print_r($result2);
echo $lineBreak;
echo "Test Case 3: ";
print_r($result3);
echo $lineBreak;
echo "Test Case 4: ";
print_r($result4);
echo $lineBreak;

/**
 * Flattens a multi-dimensional array and returns a single dimensional array with values and keys used as values
 * @param array $arr        The array to flatten
 * @return array
 */
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