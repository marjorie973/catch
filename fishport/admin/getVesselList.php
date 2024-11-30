<?php
$id = $_SESSION["id"];

$query = "SELECT vessel_id, vessel_name FROM tbl_vessel WHERE owner_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<option value="" disabled selected>Select a Vessel</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['vessel_id'] . '">' . $row['vessel_name'] . '</option>';
    }
} else {
    echo '<option value="" disabled>No vessels found</option>';
}
?>
