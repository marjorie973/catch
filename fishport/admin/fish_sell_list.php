<?php
include '../connection.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sell_id = isset($_GET['sell_id']) ? $_GET['sell_id'] : '';

// Set items per page
$items_per_page = 20;
$offset = ($page - 1) * $items_per_page;

// The main query to retrieve fish items
$query = "SELECT cf.catched_fish_id, cf.fish_name, o.owner_lname, o.owner_fname, cf.unit, cf.price, cf.quantity  
          FROM tbl_catched_fish cf
          LEFT JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id 
          LEFT JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id 
          LEFT JOIN tbl_owner o ON v.owner_id = o.owner_id 
          WHERE cf.fish_name LIKE '%$search%' 
          AND cf.unit IS NOT NULL AND cf.unit != '' 
          AND cf.quantity IS NOT NULL AND cf.quantity > 0
          AND cf.price IS NOT NULL AND cf.price > 0 
          LIMIT $items_per_page OFFSET $offset";

$result = mysqli_query($conn, $query);
$fish_items = '';
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fish_name = htmlspecialchars($row['fish_name']);
        $cf_id = $row['catched_fish_id'];
        $owner_name = $row['owner_fname'] . " " . $row['owner_lname'];
        $quantity = $row['quantity'];
        $unit = $row['unit'];
        $price = $row['price'];

        $fish_items .= "<div class='col-md-3 mb-4'>
                            <div class='card'>
                                <div class='card-body'>
                                    <h5 class='card-title'>$fish_name</h5>
                                    <p class='card-text'>Owner: $owner_name</p>
                                    <p class='card-text'>Quantity: $quantity</p>
                                    <p class='card-text'>Unit: $unit</p>
                                    <br>
                                    <form method='post' action='addToCart.php'>
                                        <div class='d-flex justify-content-between align-items-center'>
                                            <div class='d-flex'>
                                                <input type='hidden' name='catched_fish_id' value='$cf_id'>
                                                <input type='hidden' name='sell_id' value='$sell_id'>

                                                <!-- Use data-* attributes for dynamic data -->
                                                <button class='btn btn-danger btn-sm decrease-btn' type='button' data-cf-id='$cf_id' data-quantity='$quantity'>-</button>

                                                <!-- Quantity input -->
                                                <input type='number' name='quantityBought' class='form-control d-inline w-50 quantityClass' value='1' id='quantity$cf_id' data-cf-id='$cf_id' data-quantity-max='$quantity' min='1' max='$quantity' required>

                                                <button class='btn btn-success btn-sm increase-btn' type='button' data-cf-id='$cf_id' data-quantity='$quantity'>+</button>
                                            </div>
                                            <span class='font-weight-bold'>₱$price</span>
                                        </div>
                                        <button class='btn btn-primary mt-3 w-100' type='submit'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>";
    }
} else {
    $fish_items = "<p>No fish items available for sale at the moment.</p>";
}

// Query to count the total number of matching fish items (based on search criteria)
$total_query = "SELECT COUNT(*) AS total 
                FROM tbl_catched_fish cf
                LEFT JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id 
                LEFT JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id 
                LEFT JOIN tbl_owner o ON v.owner_id = o.owner_id
                WHERE cf.fish_name LIKE '%$search%' 
                AND cf.unit IS NOT NULL AND cf.unit != '' 
                AND cf.quantity IS NOT NULL AND cf.quantity > 0
                AND cf.price IS NOT NULL AND cf.price > 0";

$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_items = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);
$pagination = '';

// Build the pagination
for ($i = 1; $i <= $total_pages; $i++) {
    $active_class = ($i == $page) ? 'active' : '';
    $pagination .= "<li class='page-item $active_class'>
                        <a class='page-link pagination-link-sellFish-page' href='#' data-page='$i'>$i</a>
                    </li>";
}

echo json_encode([
    'fish_items' => $fish_items,
    'pagination' => "<ul class='pagination'>$pagination</ul>"
]);
