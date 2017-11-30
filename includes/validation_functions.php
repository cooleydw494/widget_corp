<?php
//initialize errors array
$errors = [];
// * presence
function has_presence($value) {
  return (isset($value) && $value !== "");
}
// validate presences
function validate_presences($required_fields) {
  global $errors;
  foreach($required_fields as $field) {
    $value = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    if (!has_presence($value)) {
      $errors[$field] = field_name_as_text($field) . " can't be blank";
    }
  }
}
// max length
function has_max_length($value, $max) {
  return (strlen($value) <= $max);
}
//validate max lengths
function validate_max_lengths($fields_with_max_lengths) {
  global $errors;
  foreach($fields_with_max_lengths as $field => $max) {
    $value = trim($_POST[$field]);
    if (!has_max_length($value, $max)) {
      $errors[$field] = field_name_as_text($field) . " is too long";
    }
  }
}
// * inclusion in a set
function has_inclusion_in($value, $set) {
  return in_array($value, $set);
}
//field name as text
function field_name_as_text($field_name) {
  return ucfirst(str_replace('_', ' ', $field_name));
}
