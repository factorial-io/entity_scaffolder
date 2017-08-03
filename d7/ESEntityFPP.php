<?php

class ESEntityFPP {

  public static function featureCodeHeader($info) {
    return drush_entity_scaffolder_render_template('/entity/fpp/feature.header', $info);
  }

  public static function featureCodeFooter($info) {
    return drush_entity_scaffolder_render_template('/entity/fpp/feature.footer', $info);
  }

  public static function entityDefinition($info) {
    return drush_entity_scaffolder_render_template('/entity/fpp/feature.content', $info);
  }

  public static function appendEntityDefinitions(&$code, $info) {
    $code['fpp'][$info['machine_name']] = self::entityDefinition($info);
    $code['fe_es.info'][] = "features[fieldable_panels_pane_type][] = {$info['machine_name']}";

  }

  public static function addFeatureHeaderFooter(&$code, $info) {
    array_unshift($code, self::featureCodeHeader($info));
    $code[] = self::featureCodeFooter($info);
  }

  /**
   * Helper functions to create FPPS.
   */
  public static function scaffold($config_dir, &$code) {
    $config_files = drush_entity_scaffolder_get_config_files($config_dir . '/fpp');
    foreach ($config_files as $file) {
      $config = self::getConfig($file);
      self::appendEntityDefinitions($code, $config);
      if ($config['fields']) {
        ESFieldBase::scaffold($config, $code);
        ESFieldInstance::scaffold($config, $code);
        ESFieldPreprocess::scaffold($config, $code);
      }

    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public static function getConfig($file) {
    $config = Spyc::YAMLLoad($file);
    $config['entity_type'] = 'fieldable_panels_pane';
    $config['bundle'] = $config['machine_name'];
    $config['field_prefix'] = 'fpp_' . $config['machine_name'];
    return $config;
  }

}
