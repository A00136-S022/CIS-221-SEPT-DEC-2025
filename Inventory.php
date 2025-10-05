<?php
require_once 'database.php';

// Calculate total sales
$stmt = $pdo->query("SELECT SUM(quantity_sold * price) as total_sales FROM inventory");
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

// Get all inventory items
$stmt = $pdo->query("SELECT * FROM inventory ORDER BY category, item_name");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary statistics
$stmt = $pdo->query("SELECT 
    SUM(quantity_bought) as total_bought,
    SUM(quantity_sold) as total_sold,
    SUM(quantity_in_stock) as total_stock
    FROM inventory");
$summary = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FashionStore - Inventory Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        
        .inventory-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .inventory-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inventory-table thead {
            background: #667eea;
            color: white;
        }
        
        .inventory-table th,
        .inventory-table td {
            padding: 15px;
            text-align: left;
        }
        
        .inventory-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .inventory-table tbody tr:hover {
            background: #e9ecef;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-low {
            background: #fee;
            color: #c00;
        }
        
        .badge-medium {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .badge-good {
            background: #dfe6e9;
            color: #2d3436;
        }
        
        .nav-links {
            margin-bottom: 20px;
        }
        
        .nav-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            margin-right: 20px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>FashionStore</h1>
        <p class="tagline">Inventory Management Dashboard</p>
    </header>
    
    <div class="dashboard-container">
        <div class="nav-links">
            <a href="index.html">← Back to Store</a>
            <a href="inventory.php">Refresh Data</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="value">$<?php echo number_format($totalSales, 2); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Total Items Bought</h3>
                <p class="value"><?php echo number_format($summary['total_bought']); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>Total Items Sold</h3>
                <p class="value"><?php echo number_format($summary['total_sold']); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>Items in Stock</h3>
                <p class="value"><?php echo number_format($summary['total_stock']); ?></p>
            </div>
        </div>
        
        <div class="inventory-table">
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Bought</th>
                        <th>Sold</th>
                        <th>In Stock</th>
                        <th>Revenue</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): 
                        $revenue = $item['quantity_sold'] * $item['price'];
                        $stockStatus = $item['quantity_in_stock'] < 20 ? 'low' : 
                                      ($item['quantity_in_stock'] < 40 ? 'medium' : 'good');
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['item_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity_bought']; ?></td>
                        <td><?php echo $item['quantity_sold']; ?></td>
                        <td><?php echo $item['quantity_in_stock']; ?></td>
                        <td><strong>$<?php echo number_format($revenue, 2); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo $stockStatus; ?>">
                                <?php echo ucfirst($stockStatus); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <footer>
        &copy; <span id="year"></span> FashionStore. All rights reserved.
    </footer>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>