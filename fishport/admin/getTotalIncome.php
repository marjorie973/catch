<?php
include '../connection.php';

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    $sql = "
        SELECT 
            MONTH(date_bought) AS month, 
            SUM(total_price) AS total_income
        FROM tbl_sell
        WHERE YEAR(date_bought) = ? AND status = 'Paid'
        GROUP BY MONTH(date_bought)
        ORDER BY MONTH(date_bought)
    ";

    // Prepare and execute the SQL query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $year);  // 'i' means the parameter is an integer
        $stmt->execute();
        $stmt->bind_result($month, $total_income);

        // Initialize an array to store the total incomes for each month
        $incomeData = array_fill(0, 12, 0); // Default all months to 0

        // Fetch the results and populate the array
        while ($stmt->fetch()) {
            $incomeData[$month - 1] = $total_income; // Store income for the correct month
        }

        // Close the statement and connection
        $stmt->close();

        // Output the results as a simple comma-separated string
        echo implode(',', $incomeData);
    } else {
        echo 'Error: Unable to prepare the query.';
    }
} else {
    echo 'Error: Year not specified.';
}

?>
