<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_POST['owner_id'];

    // Step 1: Delete records from tbl_sell_fish_list
    // Delete all records in tbl_sell_fish_list that are associated with a catched_fish_id 
    // linked to a catch report that was associated with a vessel belonging to the owner
    $delete_sell_fish_list_sql = "
        DELETE FROM tbl_sell_fish_list 
        WHERE catched_fish_id IN (
            SELECT cf.catched_fish_id
            FROM tbl_catched_fish cf
            JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id
            JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
            WHERE v.owner_id = ?
        )";
    
    $stmt_sell_fish_list = mysqli_prepare($conn, $delete_sell_fish_list_sql);
    mysqli_stmt_bind_param($stmt_sell_fish_list, "s", $owner_id);
    
    if (mysqli_stmt_execute($stmt_sell_fish_list)) {

        // Step 2: Delete records from tbl_sell
        // Now delete the records from tbl_sell which are linked to the vessel owned by the owner
        $delete_sell_sql = "
            DELETE FROM tbl_sell 
            WHERE sell_id IN (
                SELECT sf.sell_id
                FROM tbl_sell_fish_list sf
                JOIN tbl_catched_fish cf ON sf.catched_fish_id = cf.catched_fish_id
                JOIN tbl_catch_report cr ON cf.catch_id = cr.catch_id
                JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
                WHERE v.owner_id = ?
            )";
        
        $stmt_sell = mysqli_prepare($conn, $delete_sell_sql);
        mysqli_stmt_bind_param($stmt_sell, "s", $owner_id);
        
        if (mysqli_stmt_execute($stmt_sell)) {

            // Step 3: Delete records from tbl_catched_fish that are associated with the owner via vessels
            $delete_catched_fish_sql = "
                DELETE FROM tbl_catched_fish 
                WHERE catch_id IN (
                    SELECT cr.catch_id 
                    FROM tbl_catch_report cr
                    JOIN tbl_vessel v ON cr.vessel_id = v.vessel_id
                    WHERE v.owner_id = ?
                )";
            
            $stmt_catched_fish = mysqli_prepare($conn, $delete_catched_fish_sql);
            mysqli_stmt_bind_param($stmt_catched_fish, "s", $owner_id);
            
            if (mysqli_stmt_execute($stmt_catched_fish)) {

                // Step 4: Delete the associated catch reports
                $delete_catched_report_sql = "
                    DELETE FROM tbl_catch_report 
                    WHERE vessel_id IN (SELECT vessel_id FROM tbl_vessel WHERE owner_id = ?)";
                
                $stmt_catched_report = mysqli_prepare($conn, $delete_catched_report_sql);
                mysqli_stmt_bind_param($stmt_catched_report, "s", $owner_id);
                
                if (mysqli_stmt_execute($stmt_catched_report)) {

                    // Step 5: Delete the vessels from tbl_vessel
                    $delete_vessel_sql = "DELETE FROM tbl_vessel WHERE owner_id = ?";
                    $stmt_vessel = mysqli_prepare($conn, $delete_vessel_sql);
                    mysqli_stmt_bind_param($stmt_vessel, "s", $owner_id);

                    if (mysqli_stmt_execute($stmt_vessel)) {

                        // Step 6: Finally, delete the owner record
                        $delete_owner_sql = "DELETE FROM tbl_owner WHERE owner_id = ?";
                        $stmt_owner = mysqli_prepare($conn, $delete_owner_sql);
                        mysqli_stmt_bind_param($stmt_owner, "s", $owner_id);

                        if (mysqli_stmt_execute($stmt_owner)) {
                            $_SESSION['redirectTo'] = 'usermanager';
                            header("Location: index.php");
                            exit();
                        } else {
                            $alertMessage = "Error deleting owner: " . mysqli_error($conn);
                        }
                        mysqli_stmt_close($stmt_owner);
                    } else {
                        $alertMessage = "Error deleting vessels: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt_vessel);
                } else {
                    $alertMessage = "Error deleting catch reports: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_catched_report);
            } else {
                $alertMessage = "Error deleting catched fish: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt_catched_fish);
        } else {
            $alertMessage = "Error deleting sells: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_sell);
    } else {
        $alertMessage = "Error deleting sell fish list: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt_sell_fish_list);
}

mysqli_close($conn);
?>
