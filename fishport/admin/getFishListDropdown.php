<?php 
$query = "
    SELECT f.fish_id, f.fish_name, CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name
    FROM tbl_fish f
    INNER JOIN tbl_catched_fish cf ON f.catched_fish_id = cf.catched_fish_id
    INNER JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id
    INNER JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
    INNER JOIN tbl_owner o ON v.owner_id = o.owner_id
";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fish_id = $row['fish_id'];
        $fish_name = $row['fish_name'];
        $owner_name = $row['owner_name'];
        
        echo "<option value='$fish_id'>$fish_name ($owner_name)</option>";
    }
} else {
    echo "<option value=''>No fish found</option>";
}
?>
