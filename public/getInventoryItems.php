<?php
require "connect.php"; // Assuming this file establishes your database connection

if (isset($_GET['id'])) {
    $inventoryId = $_GET['id'];
    $sql = "SELECT *, i.quantity as total_quantity, ii.quantity as item_quantity FROM `tblinventory` i JOIN tblinventoryitems ii ON i.inventory_id = ii.inventory_id WHERE i.inventory_id = ? ORDER BY expiration_date,item_quantity";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$inventoryId]);
    $inventoryItem = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($inventoryItem) {
        echo "<h2>" . $inventoryItem[0]['inventory_item'] . "</h2>";
        echo "<h3>Total Quantity: " . $inventoryItem[0]['total_quantity'] . " " . $inventoryItem[0]['unit'] . "</h3>";
        echo "<br>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Inventory Item</th>";
        echo "<th>Inventory Quantity</th>";
        echo "<th>Expiration Date</th>";
        echo "</tr>";
        foreach ($inventoryItem as $items) { // Corrected foreach syntax
            echo "<tr>";
            echo "<td>" . htmlspecialchars($items['inventory_item']) . "</td>";
            echo "<td>" . htmlspecialchars($items['item_quantity']) . " " . htmlspecialchars($items['unit']) . "</td>";
            echo "<td>" . htmlspecialchars(date_format(new DateTime($items['expiration_date']), 'F j, Y')) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No details found for this inventory ID.";
    }
}
