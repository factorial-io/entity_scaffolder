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
          ESEntityFPP::scaffold($this);
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
    $code = $this->code;
    foreach ($code as $key => $content) {
      switch ($key) {
        case 'fpp':
          ESEntityFPP::addFeatureHeaderFooter($content, array());
          $files['sites/all/modules/features/fe_es/fe_es.fieldable_panels_pane_type.inc'] = implode("\n", $content);
          break;

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
          array_unshift($content, file_get_contents(__DIR__ . '/d7/templates/feature/fe_es/fe_es.info'));
          $content[] = 'project path = sites/all/modules/features';
          $content[] = "";
          $files['sites/all/modules/features/fe_es/fe_es.info'] = implode("\n", $content);
          break;

        default:
          drush_log(dt('Error unidentified key'), 'error');
          break;
      }
    }
    return $files;
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