<?php
/**
 * @file
 * fe_es.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function fe_es_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "fieldable_panels_panes" && $api == "fieldable_panels_pane_type") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_image_default_styles().
 */
function fe_es_image_default_styles() {
  $styles = array();

  // Exported image style: original.
  $styles['original'] = array(
    'label' => 'Original',
    'effects' => array(),
  );

  return $styles;
}
