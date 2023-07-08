<?php
include_once("../../engine/dbConnect.php");
include("../../engine/header.php");
?>
<h1>Search Food Database</h1>
<p>This search will let you find items by the brand or food name.  You can then view or manage the meal entry from the
actions menu.</p>
<h2>Search:</h2>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $("#search_bar").keyup(function() {
            var query = $(this).val();
            if (query != "") {
                $.ajax({
                    url: '../../engine/processes/search_food_process.php',
                    method: 'POST',
                    data: {query: query},
                    success: function(data) {
                        $('#output_list').html(data);
                    }
                });
            } else {
                $('#output_list').html("");
            }
        });

        $(document).on('click', 'td', function() {
            $('#search_bar').val($(this).text());
            $('#output_list').html("");
        });
    });
</script>

<form action="" method="post" style="display: flex;">
    <input type="text" id="search_bar" name="search_bar" placeholder="Search for food..." autocomplete="off">
    <input type="submit" value="Search" style="height: 45px;">
</form>
<div id="output_list"></div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search_bar'];
    $stmt = $conn->prepare("SELECT * FROM food WHERE name LIKE ? OR brand LIKE ?");
    $searchParam = '%' . $search . '%';
    $stmt->bind_param('ss', $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table class='table-custom'>";
        echo "<tr><th>Name</th><th>Brand</th><th>Actions</th></tr>";
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["name"]."</td><td>".$row["brand"]."</td>";
            echo "<td>
                <div class='dropdown' style='width: 100%'>
                  <button class='dropbtn'>Actions</button>
                  <div class='dropdown-content'>
                    <a href='/view_food.php?id=".$row["id"]."'>View</a>
                    <a href='/manage_food.php?id=".$row["id"]."'>Manage</a>
                  </div>
                </div></>
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

<?php
include_once("../../engine/footer.php");
?>
