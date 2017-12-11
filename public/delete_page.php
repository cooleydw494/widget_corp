<?php require_once('../includes/session.php') ?>
<?php require_once('../includes/db_connection.php'); ?>
<?php require_once('../includes/functions.php'); ?>
<?php
//verify there is a current page
$current_page = find_page_by_id($_GET['page']);
if (!$current_page) {
  $_SESSION['message'] = 'Error: no page selected';
  redirect_to('manage_content.php');
}
else {

  //DELETE
  $id = $current_page['id'];
  $query = "DELETE FROM pages WHERE id = {$id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    //success
    $_SESSION['message'] = "Page Deleted";
    redirect_to('manage_content.php');
  } else {
    //failure
    $_SESSION['message'] = "Page Deletion Failed";
    redirect_to("manage_content.php?page={$id}");
  }
}
 ?>
