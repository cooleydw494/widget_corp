<?php

  function redirect_to($new_location) {
    header("Location: " . $new_location);
    exit;
  }

  function mysql_prep($string) {
    global $connection;
    return mysqli_real_escape_string($connection, $string);
  }

  // confirm a query got a result (empty set still counts)
  function confirm_query($result_set) {
    if (!$result_set)
    {
      die("Database query failed.");
    }
  }

  function find_all_admins() {
    global $connection;
    $query = "SELECT * ";
    $query .= "FROM admins ";
    $query .= "ORDER BY username ASC ";
    $admin_set = mysqli_query($connection, $query);
    confirm_query($admin_set);
    return $admin_set;
  }

  function find_admin_by_id($admin_id) {
    global $connection;
    $safe_admin_id = mysqli_real_escape_string($connection, $admin_id);

    $query = "SELECT * ";
    $query .= "FROM admins ";
    $query .= "WHERE id = {$safe_admin_id} ";
    $query .= "LIMIT 1";

    $selected_admin = mysqli_query($connection, $query);
    confirm_query($selected_admin);
    if ($admin = mysqli_fetch_assoc($selected_admin)) {
      return $admin;
    } else {
      return null;
    }
  }

  function find_admin_by_username($username) {
    global $connection;
    $safe_username = mysqli_real_escape_string($connection, $username);

    $query = "SELECT * ";
    $query .= "FROM admins ";
    $query .= "WHERE username = '{$safe_username}' ";
    $query .= "LIMIT 1";

    $selected_admin = mysqli_query($connection, $query);
    confirm_query($selected_admin);
    if ($admin = mysqli_fetch_assoc($selected_admin)) {
      return $admin;
    } else {
      return null;
    }
  }

  function find_all_subjects($public = true) {
    global $connection;
    $query = "SELECT * ";
    $query .= "FROM subjects ";
    if ($public) {
      $query .= "WHERE visible = 1 ";
    }
    $query .= "ORDER BY position ASC ";
    $subject_set = mysqli_query($connection, $query);
    confirm_query($subject_set); // in functions.php
    return $subject_set;
  }

  function find_pages_for_subject($subject_id, $public = true) {
    global $connection;
    $safe_subject_id = mysqli_real_escape_string($connection, $subject_id);
    $query = "SELECT * ";
    $query .= "FROM pages ";
    $query .= "WHERE subject_id = {$safe_subject_id} ";
    if ($public) {
      $query .= "AND visible = 1 ";
    }
    $query .= "ORDER BY position ASC ";
    $page_set = mysqli_query($connection, $query);
    confirm_query($page_set); // in functions.php
    return $page_set;
  }

  // takes two arguments, currently selected subject array or null
  // and currently selected page array or null
  function navigation($subject_array, $page_array) {
      $output = '<ul class="subjects">';

      $subject_set = find_all_subjects(false);
      while($subject = mysqli_fetch_assoc($subject_set))
      {
        $output .= '<li ';
        if ($subject && $subject_array['id'] == $subject['id']) {
          $output .= 'class="selected"';
        }
        $output .= '><a href="manage_content.php?subject=';
        $output .= urlencode($subject['id']);
        $output .='">';
        $output .= htmlentities($subject['menu_name']);
        $output .= '</a>';
        $output .= '<ul class="pages">';
        $page_set = find_pages_for_subject($subject['id'], false);
        while($page = mysqli_fetch_assoc($page_set))
        {
          $output .= '<li ';
          if ($page_array && $page_array['id'] == $page['id']) {
            $output .= 'class="selected"';
          }
          $output .= '><a href="manage_content.php?page=';
          $output .= urlencode($page['id']);
          $output .= '">';
          $output .= htmlentities($page['menu_name']);
          $output .= '</a></li>';
        }
        mysqli_free_result($page_set);
        $output .= '</ul></li>';
      }
      mysqli_free_result($subject_set);
    $output .= '</ul>';
    return $output;
  }

  // takes two arguments, currently selected subject array or null
  // and currently selected page array or null
  function public_navigation($subject_array, $page_array) {
      $output = '<ul class="subjects">';

      $subject_set = find_all_subjects();
      while($subject = mysqli_fetch_assoc($subject_set))
      {
        $output .= '<li ';
        if ($subject && $subject_array['id'] == $subject['id']) {
          $output .= 'class="selected"';
        }
        $output .= '><a href="index.php?subject=';
        $output .= urlencode($subject['id']);
        $output .='">';
        $output .= htmlentities($subject['menu_name']);
        $output .= '</a>';
        $output .= '<ul class="pages">';
        if ($subject_array['id'] == $subject['id'] || $page_array['subject_id'] == $subject['id']) {
          $page_set = find_pages_for_subject($subject['id']);
          while($page = mysqli_fetch_assoc($page_set))
          {
            $output .= '<li ';
            if ($page_array && $page_array['id'] == $page['id']) {
              $output .= 'class="selected"';
            }
            $output .= '><a href="index.php?page=';
            $output .= urlencode($page['id']);
            $output .= '">';
            $output .= htmlentities($page['menu_name']);
            $output .= '</a></li>';
          }
          $output .= '</li>';
          mysqli_free_result($page_set);
        }
        $output .= '</ul>';
      }
      mysqli_free_result($subject_set);
    $output .= '</ul>';
    return $output;
  }

  function find_default_page_for_subject($subject_id) {
    $page_set = find_pages_for_subject($subject_id);
    if ($first_page = mysqli_fetch_assoc($page_set)) {
      return $first_page;
    } else {
      return null;
    }
  }

  function find_selected_page($public = false) {
    global $current_subject;
    global $current_page;
    if (isset($_GET['subject'])) {
      $current_subject = find_subject_by_id($_GET['subject'], $public);
      if ($public && $current_subject) {
        $current_page = find_default_page_for_subject($current_subject['id']);
      } else {
        $current_page = null;
      }
    }
    else if (isset($_GET['page'])) {
      $current_page = find_page_by_id($_GET['page'], $public);
      $current_subject = null;
    }
    else {
      $current_page = null;
      $current_subject = null;
    }
  }

  function find_subject_by_id($subject_id, $public = true) {
    global $connection;
    $safe_subject_id = mysqli_real_escape_string($connection, $subject_id);
    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE id = {$safe_subject_id} ";
    if ($public) {
      $query .= "AND visible = 1 ";
    }
    $query .= "LIMIT 1";
    $subject_set = mysqli_query($connection, $query);
    confirm_query($subject_set); // in functions.php
    if ($subject = mysqli_fetch_assoc($subject_set)) {
      return $subject;
    } else {
      return null;
    }
  }

  function find_page_by_id($page_id, $public = true) {
    global $connection;
    $safe_page_id = mysqli_real_escape_string($connection, $page_id);
    $query = "SELECT * ";
    $query .= "FROM pages ";
    $query .= "WHERE id = {$safe_page_id} ";
    if ($public) {
      $query .= "AND visible = 1 ";
    }
    $query .= "LIMIT 1";
    $page_set = mysqli_query($connection, $query);
    confirm_query($page_set); // in functions.php
    if ($page = mysqli_fetch_assoc($page_set)) {
      return $page;
    } else {
      return null;
    }
  }
  // form errors
  function form_errors($errors = []) {
    $output = "";
    if (!empty($errors)) {
      $output = "<div class=\"error\">";
      $output .= "Please fix the following errors";
      $output .= "<ul>";
      foreach ($errors as $key => $error) {
        $output .= "<li>";
        $output .= htmlentities($error);
        $output .= "</li>";
      }
      $output .= "</ul>";
      $output .= "</div>";
    }
    return $output;
  }

  function password_encrypt($password) {
    $hash_format = "$2y$10$";
    $salt_length = 22;
    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
  }

  function generate_salt($length) {
    $unique_random_string = md5(uniqid(mt_rand(), true));
    $base64_string = base64_encode($unique_random_string);
    $modified_base64_string = str_replace('+', '.', $base64_string);
    $salt = substr($modified_base64_string, 0 , $length);
    return $salt;
  }

  function password_check($password, $existing_hash) {
    $hash = crypt($password, $existing_hash);
    if ($hash == $existing_hash) {
      return true;
    } else {
      return false;
    }
  }

  function attempt_login($username, $password) {
    $admin = find_admin_by_username($username);
    if ($admin) {
      if (password_check($password, $admin['hashed_password'])) {
        return $admin;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function confirm_logged_in() {
    if (!logged_in()) {
      redirect_to('login.php');
    }
  }

  function logged_in() {
    return isset($_SESSION['admin_id']);
  }
