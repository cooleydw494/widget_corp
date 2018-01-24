<?php require_once('../includes/db_connection.php'); ?>
<?php require_once('../includes/functions.php'); ?>
<?php require_once('../includes/session.php'); ?>
<?php confirm_logged_in(); ?>
<?php require_once('../includes/validation_functions.php'); ?>
<?php find_selected_page(); ?>
<?php
// can't add a new page unless we have a subject as a parent!
if (!$current_subject) {
  // subject id was missing or invalid or couldn't be found in DB
  redirect_to('manage_content.php');
}
 ?>
<?php if (isset($_POST['submit'])) {
  //// process the form ////
  //grab form input stuff
  $menu_name = mysql_prep($_POST['menu_name']);
  $position = (int) $_POST['position'];
  $visible = isset($_POST['visible']) ? (int) $_POST['visible'] : '';
  $content = mysql_prep($_POST['content']);
  $subject_id = $current_subject['id'];

  //validation
  $required_fields = ['menu_name', 'position', 'visible', 'content'];
  $fields_with_max_lengths = ['menu_name' => 30];
  validate_presences($required_fields);
  validate_max_lengths($fields_with_max_lengths);

  if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect_to("new_page.php?subject={$subject_id}");
  }

  //build query
  $query = "INSERT INTO pages (";
  $query .= " subject_id, menu_name, position, visible, content";
  $query .= ") VALUES (";
  $query .= " {$subject_id},'{$menu_name}', {$position}, {$visible}, '{$content}'";
  $query .= ")";
  //make query
  $result = mysqli_query($connection, $query);
  //check result
  if ($result) {
    //SUCCESS
    $_SESSION['message'] = "Page created";
    redirect_to('manage_content.php');
  } else {
    //FAILURE
    $_SESSION['message'] = "Page creation failed";
    redirect_to("new_page.php?subject={$subject_id}");
  }
} else { ?>
  <?php $layout_context = "admin"; ?>
<?php include('../includes/layouts/header.php'); ?>
<div id="main">
  <div id="navigation">
    <?php echo navigation($current_subject, $current_page); ?>
  </div>
  <div id="page">
    <?php echo message(); ?>
    <?php echo form_errors(errors()); ?>
    <h2>Create Page</h2>
    <form action="new_page.php?subject=<?php echo urlencode($current_subject['id']); ?>" method="post">
      <p>Menu name:
        <input type="text" name="menu_name" value="" />
      </p>
      <p>Position:
        <select name="position">
          <?php
          $page_set = find_pages_for_subject($current_subject['id'], false);
          $page_count = mysqli_num_rows($page_set);
          for($count = 1; $count <= ($page_count + 1); $count++) {
            echo "<option value=\"{$count}\">{$count}</option>";
          }
          ?>
        </select>
      </p>
      <p>Visible:
        <input type="radio" name="visible" value="0" /> No
        &nbsp;
        <input type="radio" name="visible" value="1" /> Yes
      </p>
      <p>Content:<br />
        <textarea name="content" rows="20" cols="80"></textarea>
      </p>
      <input type="submit" name="submit" value="Create Page" />
    </form>
    <br />
    <a href="manage_content.php?subject=<?php echo urlencode($current_subject['id']); ?>">Cancel</a>
  </div>
</div>
<?php } ?>
<?php include('../includes/layouts/footer.php'); ?>
