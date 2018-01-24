<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once('../includes/session.php'); ?>
<?php confirm_logged_in(); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php
$admin = find_admin_by_id($_GET['id']);
if (!$admin) {
  redirect_to('manage_admins.php');
}

$id = $admin['id'];
$query = "DELETE FROM admins WHERE id = {$id} LIMIT 1";
$result = mysqli_query($connection, $query);
if ($result && mysqli_affected_rows($connection) == 1) {
  $_SESSION['message'] = "Admin Deleted!";
  redirect_to('manage_admins.php');
} else {
  $_SESSION['message'] = "Admin Deletion Failed :(";
  redirect_to('manage_admins.php');
}
?>
