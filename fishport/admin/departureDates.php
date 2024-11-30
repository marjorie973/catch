<?php
$departureDate = "";
$returnDate = "";
$disableDepartureButton = false;
$disableReturnButton = false;

if (isset($_SESSION['id'])) {
    $owner_id = $_SESSION['id'];
    
    $sql = "
        SELECT cr.depart_date, cr.return_date, cr.catch_id 
        FROM tbl_catch_report cr
        JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
        JOIN tbl_owner o ON v.owner_id = o.owner_id
        WHERE o.owner_id = '$owner_id' AND (o.status = 'Departure' OR o.status = 'Return') AND catch_id = '$catch_id'
    ";
    
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $departureDate = $row['depart_date'];
        $returnDate = $row['return_date'];

        if ($departureDate) {
            $disableDepartureButton = true;
        }
        if ($returnDate) {
            $disableReturnButton = true;
        }
    }
}
?>

<div class="form-group mr-2">
    <label for="departureDate">Departure Date</label>
    <div id="departureDateText" class="form-control-plaintext">
        <?php echo $departureDate ? date('F j, Y g:i A', strtotime($departureDate)) : ''; ?>
    </div>
</div>
<div class="form-group">
    <label for="returnDate">Return Date</label>
    <div id="returnDateText" class="form-control-plaintext">
        <?php echo $returnDate ? date('F j, Y g:i A', strtotime($returnDate)) : ''; ?>
    </div>
</div>

<script>
    <?php if ($disableDepartureButton): ?>
        document.querySelector('[name="departure"]').disabled = true;
    <?php endif; ?>

    <?php if ($disableReturnButton): ?>
        document.querySelector('[name="return"]').disabled = true;
    <?php endif; ?>
</script>
