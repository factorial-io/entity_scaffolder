<?php

require_once "ScaffolderBase.php";
require_once "d7/ESEntityFPP.php";
require_once "d7/ESEntityParagraphs.php";
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

        case 'paragraphs':
          $paragraphs = new ESEntityParagraphs($this);
          $paragraphs->scaffold();
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
    $code = $this->getCode();
    // Populate header section of info file.
    if(!empty($code['fe_es'])) {
      $code = file_get_contents(__DIR__ . '/d7/templates/feature/fe_es/fe_es.info');
      $this->setCode('fe_es', 'fe_es.info', Self::HEADER, 0, $code);
      $code = "\nproject path = sites/all/modules/features\n";
      $this->setCode('fe_es', 'fe_es.info', Self::CONTENT, $code, $code);
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
        return 'sites/all/modules/custom';

      case 'fe_es':
        return 'sites/all/modules/features';

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

      // Copy fe_es_filters, only once.
      if (!file_exists('sites/all/modules/features/fe_es_filters/fe_es_filters.info')) {
        Utils::copyFolderContents(__DIR__ . '/d7/templates/feature/fe_es_filters', 'sites/all/modules/features/fe_es_filters');
      }
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
