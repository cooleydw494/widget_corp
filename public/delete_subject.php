<?php require_once('../includes/session.php') ?>
<?php require_once('../includes/db_connection.php'); ?>
<?php require_once('../includes/functions.php'); ?>
<?php
//verify there is a current subject
$current_subject = find_subject_by_id($_GET['subject']);
if (!$current_subject) {
  $_SESSION['message'] = 'Error: no subject selected';
  redirect_to('manage_content.php');
}
else {

  $pages_set = find_pages_for_subject($current_subject['id']);
  if (mysqli_num_rows($pages_set) > 0) {
    //failure
    $_SESSION['message'] = "You cannot delete a subject with pages";
    redirect_to("manage_content.php?subject={$current_subject['id']}");
  }

  //DELETE
  $id = $current_subject['id'];
  $query = "DELETE FROM subjects WHERE id = {$id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    //success
    $_SESSION['message'] = "Subject Deleted";
    redirect_to('manage_content.php');
  } else {
    //failure
    $_SESSION['message'] = "Subject Deletion Failed";
    redirect_to("manage_content.php?subject={$id}");
  }
}
 ?>
