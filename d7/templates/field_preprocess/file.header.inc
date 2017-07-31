<?php

/**
 * @file
 * Code for the ES Helper.
 */

/**
 * Implements hook_preprocess_fieldable_panels_pane().
 */
function es_helper_preprocess_fieldable_panels_pane(&$vars) {

  $fn = 'es_helper_preprocess_fieldable_panels_pane_' . $vars['elements']['#bundle'];
  if (function_exists($fn)) {
    $fn($vars);
  }

  // Allow theme to preprocess variables after this module.
  global $theme;
  $fn = $theme . '_preprocess_fieldable_panels_pane_' . $vars['elements']['#bundle'];
  if (function_exists($fn)) {
    $fn($vars);
  }

  $view_mode = $vars['elements']['#view_mode'];
  if ($view_mode != 'full') {
    $vars['theme_hook_suggestions'][] = 'fieldable_panels_pane____' . $view_mode;
    $vars['theme_hook_suggestions'][] = 'fieldable_panels_pane__' . str_replace('-', '_', $vars['elements']['#bundle']) . '__' . str_replace('-', '_', $view_mode);
  }

  // Expose FPP title as title.
  $vars['title'] = '';
  if (!empty($vars['elements']['#fieldable_panels_pane']->title)) {
    $vars['title'] = $vars['elements']['#fieldable_panels_pane']->title;
  }
}

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
