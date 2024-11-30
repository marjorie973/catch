<?php
$query = "SELECT owner_id, owner_fname, owner_lname FROM tbl_owner";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    echo '<option value="" disabled selected>Select Owner</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['owner_id'] . '">' . $row['owner_fname'], $row['owner_lname'] . '</option>';
    }
} else {
    echo '<option value="" disabled>No vessels found</option>';
}
?>