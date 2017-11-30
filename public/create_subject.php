<?php
require_once('../includes/session.php');
require_once('../includes/db_connection.php');
require_once('../includes/functions.php');
require_once('../includes/validation_functions.php');

if (isset($_POST['submit'])) {
  //// process the form ////
  //grab form input stuff
  $menu_name = mysql_prep($_POST['menu_name']);
  $position = (int) $_POST['position'];
  $visible = isset($_POST['visible']) ? (int) $_POST['visible'] : '';

  //validation
  $required_fields = ['menu_name', 'position', 'visible'];
  $fields_with_max_lengths = ['menu_name' => 30];
  validate_presences($required_fields);
  validate_max_lengths($fields_with_max_lengths);

  if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect_to('new_subject.php');
  }

  //build query
  $query = "INSERT INTO subjects (";
  $query .= " menu_name, position, visible";
  $query .= ") VALUES (";
  $query .= " '{$menu_name}', {$position}, {$visible}";
  $query .= ")";
  //make query
  $result = mysqli_query($connection, $query);
  //check result
  if ($result) {
    //SUCCESS
    $_SESSION['message'] = "Subject created";
    redirect_to('manage_content.php');
  } else {
    //FAILURE
    $_SESSION['message'] = "Subject creation failed";
    redirect_to('new_subject.php');
  }
} else {
  //this is probably a GET request
  redirect_to('new_subject.php');
}

if (isset($connection)) { mysqli_close($connection); }
