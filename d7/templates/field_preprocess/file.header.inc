<?php
/**
 * @file
 * Code for the ES Helper.
 */

/**
 * Helper function to extract image data from a field for frontend.
 */
function es_helper_get_image_data($file, $image_styles = array()) {
  $out = $file;
  $out['alt'] = 'image';
  if (!empty($file['field_file_image_alt_text'][LANGUAGE_NONE][0]['safe_value'])) {
    $out['alt'] = $file['field_file_image_alt_text'][LANGUAGE_NONE][0]['safe_value'];
  }
  foreach ($image_styles as $key => $style_name) {
    $out[$key] = image_style_url($style_name, $file['uri']);
  }
  return $out;
}
