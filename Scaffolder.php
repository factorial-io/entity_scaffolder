<?php

require_once "ScaffolderBase.php";
require_once "d7/ESEntityFPP.php";
require_once "d7/ESFieldBase.php";
require_once "d7/ESFieldInstance.php";
require_once "d7/ESFieldPreprocess.php";

class Scaffolder extends ScaffolderBase {

  public function __construct() {
    parent::__construct();
    $this->setTemplateDir(__DIR__ . '/d7/templates');
  }

  /**
   * Start scaffolding.
   */
  public function scaffold() {
    if (empty($this->getEntityTypes())) {
      drush_log(dt('Entity Scaffolder didn\'t find any definitions'), 'error');
      return;
    }
    foreach ($this->getEntityTypes() as $entity_type) {
      switch ($entity_type) {
        case 'fpp':
          $fpp = new ESEntityFPP($this);
          $fpp->scaffold();
          break;

        default:
          drush_log(dt('Entity Scaffolder does not support %type', array('%type' => $entity_type)), 'error');
          break;
      }
    }
  }

  /**
   * Prepare files
   */
  public function processFiles() {
    $code = array();
    foreach ($code as $key => $content) {
      switch ($key) {
        case 'field_base':
          ESFieldBase::addFeatureHeaderFooter($content, array());
          $files['sites/all/modules/features/fe_es/fe_es.features.field_base.inc'] = implode("\n", $content);
          break;

        case 'field_instance':
          ESFieldInstance::addFeatureHeaderFooter($content, array());
          $files['sites/all/modules/features/fe_es/fe_es.features.field_instance.inc'] = implode("\n", $content);
          break;

        case 'field_preprocess':
          ESFieldPreprocess::addCodeHeaderFooter($content, array());
          $files['sites/all/modules/custom/es_helper/es_helper.preprocess.inc'] = implode("\n", $content);
          break;

        case 'fe_es.info':
          $files['sites/all/modules/features/fe_es/fe_es.info'] = implode("\n", $content);
          break;

        default:
          drush_log(dt('Error unidentified key'), 'error');
          break;
      }
    }
    $code = $this->getCode();
    // Populate header section of info file.
    if(!empty($code['fe_es'])) {
      $code = file_get_contents(__DIR__ . '/d7/templates/feature/fe_es/fe_es.info');
      $this->setCode('fe_es', 'fe_es.info', Self::HEADER, 0, $code);
      $code = "project path = sites/all/modules/features\n";
      $this->setCode('fe_es', 'fe_es.info', Self::HEADER, 1, $code);
    }
    return $this->flattenFiles();
  }

  /**
   * Prepare files
   */
  public function flattenFiles() {
    $code = $this->getCode();
    $files = array();
    foreach ($code as $module_name => $module_data) {
      foreach ($module_data as $filename => $file_data) {
        $file_path = $this->getModulePath($module_name) . "/{$module_name}/{$filename}";
        $blocks = array();
        ksort($file_data);
        $code = '';
        foreach($file_data as $code_blocks) {
          if(is_array($code_blocks)) {
            ksort($code_blocks);
            $code_blocks = implode('', $code_blocks);
          }
          $code .= $code_blocks;
        }
        $files[$file_path] = $code;
      }
    }
    return $files;
  }

  /**
   * Helper function to retrieve module path.
   */
  public function getModulePath($module_name) {
    switch($module_name) {
      case 'es_helper':
        return 'sites/all/modules/custom/';

      case 'fe_es':
        return 'sites/all/modules/features/';

    }
    return NULL;
  }

  /**
   * write code to file system.
   */
  public function exportCode() {
    $files = $this->processFiles($this->code);

    $debug = !!drush_get_option('debug');

    // Prepare module directories.
    if (!$debug) {
      Utils::copyFolderContents(__DIR__ . '/d7/templates/feature/fe_es', 'sites/all/modules/features/fe_es');
      Utils::copyFolderContents(__DIR__ . '/d7/templates/preprocess/es_helper', 'sites/all/modules/custom/es_helper');
    }
    // Write dynamic code to files.
    foreach ($files as $extention => $file_contents) {
      if ($debug) {
        echo "----------------------------------------------------------------\n";
        echo $extention . "\n";
        echo "----------------------------------------------------------------\n";
        echo $file_contents;
        echo "================================================================\n";
      }
      if (file_put_contents($extention, $file_contents) === FALSE) {
        drush_log(dt('Error while writing to file @file', array('@file' => $extention)), 'error');
      }
      else {
        drush_log(dt('Updated @file', array('@file' => $extention)), 'success');
      }
    }
  }
}
