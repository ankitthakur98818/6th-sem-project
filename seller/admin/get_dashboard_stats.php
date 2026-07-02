<?php
header('Content-Type: application/json');
require_once "../../TheSpiceNepal/database/dbpgadmin.php";

$response = [];

try {
    // 1. Basic Stats
    $revenue = $conn->query("SELECT SUM(total) FROM orders")->fetchColumn() ?: 0;
    $orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $users = $conn->query("SELECT COUNT(*) FROM register")->fetchColumn();

    $response['stats'] = [
        'revenue' => number_format($revenue, 2),
        'orders' => $orders,
        'products' => $products,
        'users' => $users
    ];

    // 2. Sales Trend (Last 7 Days)
    $salesQuery = $conn->query("
        SELECT TO_CHAR(created_at, 'Mon DD') as date, SUM(total) as amount 
        FROM orders 
        WHERE created_at > CURRENT_DATE - INTERVAL '7 days'
        GROUP BY TO_CHAR(created_at, 'Mon DD'), created_at
        ORDER BY created_at ASC
    ");
    $response['sales_chart'] = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

    // 3. Category Distribution
    $categoryQuery = $conn->query("
        SELECT category as label, COUNT(*) as value FROM products GROUP BY category
    ");
    $response['category_chart'] = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);

    // 4. Recent Orders
    $recentQuery = $conn->query("
        SELECT id, first_name || ' ' || last_name as customer, total, status, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI') as date
        FROM orders ORDER BY created_at DESC LIMIT 5
    ");
    $response['recent_orders'] = $recentQuery->fetchAll(PDO::FETCH_ASSOC);

    // 5. Python AI Engine Integration Sync
    try {
        $ai_sync = $conn->query("SELECT COUNT(*) FROM product_similarities")->fetchColumn();
        $response['ai_insights'] = [
            'status' => $ai_sync > 0 ? "Active" : "Offline",
            'paired_items' => $ai_sync,
            'alg_version' => '1.0 TF-IDF'
        ];
    } catch(PDOException $e) {
        $response['ai_insights'] = [
            'status' => "Pending Schema",
            'paired_items' => 0,
            'alg_version' => '1.0'
        ];
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>