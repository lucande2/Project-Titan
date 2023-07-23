<?php
/*
    engine/proccesses/search_food_process.php    VERSION 1.3
    Functionality for food searching on search_food.php
    Reviewed 7/12/2023
*/

include_once("../dbConnect.php");

if (isset($_POST["query"])) {
    $search = $_POST["query"];
    $stmt = $conn->prepare("SELECT id, name, brand, CONCAT(serving_size, ' ', serving_measurement) AS serving FROM food WHERE (name LIKE ? OR brand LIKE ?)");

    $searchParam = '%' . $search . '%';
    $stmt->bind_param('ss', $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table-custom'><tr><th>Name</th><th>Brand</th><th>Serving</th><th>Actions</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr data-id='" . $row["id"] . "'><td>".$row["name"]."</td><td>".$row["brand"]."</td><td>".$row["serving"]."</td>";
            echo "<td>
                    <div class='dropdown'>
                      <button class='dropbtn select'>Select</button>
                      <div class='dropdown-content'>
                        <a href='https://project.lucande.io/content/foods/view_food.php?id=".$row["id"]."' target='_blank'>View</a>
                        <a href='https://project.lucande.io/content/foods/manage_food.php?id=".$row["id"]."' target='_blank'>Modify</a>
                      </div>
                    </div>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>No food entries found...</h2>";
    }
    $stmt->close();
}
?>
