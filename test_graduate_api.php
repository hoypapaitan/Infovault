<?php
// Test file to check if GraduateAnalytics API is working
// Navigate to: http://localhost:8080/test_graduate_api.php

echo "<h1>Graduate Analytics API Test</h1>";

// Test database connection and basic query
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=infovault_db;charset=utf8mb4',
        'root', // default XAMPP/Laragon username
        '',     // default XAMPP/Laragon password (empty)
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "<h2>✅ Database Connection: Success</h2>";
    
    // Test basic graduate data query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tblgraduates WHERE deleted_at IS NULL");
    $result = $stmt->fetch();
    echo "<p><strong>Total graduates in database:</strong> " . $result['total'] . "</p>";
    
    // Test graduate data with details
    $stmt = $pdo->query("SELECT * FROM tblgraduates WHERE deleted_at IS NULL LIMIT 5");
    $graduates = $stmt->fetchAll();
    
    echo "<h3>Sample Graduate Records:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Course</th><th>Year Graduated</th><th>Batch</th></tr>";
    
    foreach ($graduates as $grad) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($grad['id']) . "</td>";
        echo "<td>" . htmlspecialchars($grad['name']) . "</td>";
        echo "<td>" . htmlspecialchars($grad['course']) . "</td>";
        echo "<td>" . htmlspecialchars($grad['yearGraduated']) . "</td>";
        echo "<td>" . htmlspecialchars($grad['batch']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test analytics queries that our API would use
    echo "<h3>Analytics Data Test:</h3>";
    
    // Test year range query
    $stmt = $pdo->query("
        SELECT 
            yearGraduated,
            COUNT(*) as total,
            SUM(CASE 
                WHEN name LIKE '%a' OR name LIKE '%e' OR name LIKE '%i' 
                THEN 1 ELSE 0 
            END) as estimated_female,
            SUM(CASE 
                WHEN name NOT LIKE '%a' AND name NOT LIKE '%e' AND name NOT LIKE '%i' 
                THEN 1 ELSE 0 
            END) as estimated_male
        FROM tblgraduates 
        WHERE deleted_at IS NULL 
        GROUP BY yearGraduated 
        ORDER BY yearGraduated ASC
    ");
    
    $yearlyData = $stmt->fetchAll();
    echo "<p><strong>Yearly breakdown:</strong></p>";
    echo "<ul>";
    foreach ($yearlyData as $year) {
        echo "<li>Year {$year['yearGraduated']}: {$year['total']} graduates (Est. {$year['estimated_male']} male, {$year['estimated_female']} female)</li>";
    }
    echo "</ul>";
    
    // Test course distribution
    $stmt = $pdo->query("
        SELECT 
            course,
            COUNT(*) as total
        FROM tblgraduates 
        WHERE deleted_at IS NULL 
        GROUP BY course 
        ORDER BY total DESC
    ");
    
    $courseData = $stmt->fetchAll();
    echo "<p><strong>Course distribution:</strong></p>";
    echo "<ul>";
    foreach ($courseData as $course) {
        echo "<li>{$course['course']}: {$course['total']} graduates</li>";
    }
    echo "</ul>";
    
    echo "<h2>✅ Database Queries: Success</h2>";
    echo "<p><em>Graduate Analytics API should be working with this data!</em></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Connection: Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration.</p>";
}

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>If this test passes, start the CodeIgniter server: <code>php spark serve --host=localhost --port=8080</code></li>";
echo "<li>Test the API endpoint: <code>http://localhost:8080/infovault/api/v1/graduate-analytics/dashboard/summary</code></li>";
echo "<li>Test filter options: <code>http://localhost:8080/infovault/api/v1/graduate-analytics/filter/options</code></li>";
echo "</ol>";
?>