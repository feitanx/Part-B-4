function transform($data) {
    $result = [];
    $map = [];

    // First pass: map all items by their ID
    foreach ($data as $item) {
        $map[$item['id']] = [
            'id' => $item['id'],
            'level' => $item['attributes']['level'],
            'children' => []
        ];
    }

    // Second pass: build the hierarchy
    foreach ($map as $item) {
        if ($item['level'] === 1) {
            // Level 1 items go directly into the result
            $result[] = $item;
        } else {
            // Level 2 and Level 3 items find their parents
            for ($i = 1; $i < $item['level']; $i++) {
                foreach ($result as &$parent) {
                    if ($parent['level'] === $i) {
                        $parent['children'][] = $item;
                    }
                }
            }
        }
    }

    // Remove 'children' key if empty
    array_walk_recursive($result, function(&$item) {
        if (empty($item['children'])) {
            unset($item['children']);
        }
    });

    return $result;
}

// Example input JSON data
$jsonData = '[
    {"id": 1, "attributes": {"level": 1}},
    {"id": 2, "attributes": {"level": 2}},
    {"id": 3, "attributes": {"level": 3}},
    {"id": 4, "attributes": {"level": 2}},
    {"id": 5, "attributes": {"level": 1}},
    {"id": 6, "attributes": {"level": 2}},
    {"id": 7, "attributes": {"level": 3}}
]';

// Decode the JSON data into a PHP array
$inputData = json_decode($jsonData, true);

// Transform the input data
$outputData = transform($inputData);

// Output the result as JSON
header('Content-Type: application/json');
echo json_encode($outputData, JSON_PRETTY_PRINT);

