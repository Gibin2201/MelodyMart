<?php
include_once("../dboperation.php");
$obj = new dboperation();

if (isset($_POST['id'])) {
    $districtid = $_POST['id'];

    $sql="SELECT * FROM tbl_location where district_id = '$districtid'" ;
    $result = $obj->executequery($sql); // Changed from executequery to query

    echo "<option value='' selected disabled>----Choose location----</option>";

    while ($display = mysqli_fetch_array($result)) {
        echo "<option value='" . $display['location_id'] . "'>" . $display['location_name'] . "</option>";
    }
}
?>