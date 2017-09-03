<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\ScaffolderInterface;
use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;

class Scaffolder extends ScaffolderBase {

  protected $plugins;

  public function __construct() {
    parent::__construct();
    $this->setTemplateDir(__DIR__ . '/templates');
    $this->plugins['fpp'] = new ESEntityFPP($this);
    $this->plugins['paragraphs'] = new ESEntityParagraphs($this);
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
      if (isset($this->plugins[$entity_type])) {
        $this->plugins[$entity_type]->scaffold();
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
      $code = file_get_contents($this->getTemplateDir() . '/feature/fe_es/fe_es.info');
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
        $file_path = $this->getDirectory($module_name) . "/{$filename}";
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
  public function getDirectory($module_name) {
    return isset($this->getConfig()['directories'][$module_name]) ? $this->getConfig()['directories'][$module_name] : NULL;
  }

  /**
   * Helper function to retrieve file write scheme based on path.
   */
  public function getWriteScheme($path) {
    // Twig template files.
    if (Utils::endsWith($path, '.twig')) {
      return Utils::TWIG_COMMENT;
    }
    return Utils::FILE_EXISTS_OVERWRITE;
  }

  /**
   * write code to file system.
   */
  public function exportCode() {
    $files = $this->processFiles($this->code);

    $debug = !!drush_get_option('debug');

    // Prepare module directories.
    if (!$debug) {
      Utils::copyFolderContents($this->getTemplateDir() . '/feature/fe_es', $this->getDirectory('fe_es'));
      Utils::copyFolderContents($this->getTemplateDir() . '/preprocess/es_helper', $this->getDirectory('es_helper'));

      // Copy fe_es_filters, only once.
      if (!file_exists($this->getDirectory('fe_es_filters') . '/fe_es_filters.info')) {
        Utils::copyFolderContents($this->getTemplateDir() . '/feature/fe_es_filters', $this->getDirectory('fe_es_filters'));
      }
    }
    // Write dynamic code to files.
    foreach ($files as $filepath => $file_contents) {
      if ($debug) {
        echo "----------------------------------------------------------------\n";
        echo $filepath . "\n";
        echo "----------------------------------------------------------------\n";
        echo $file_contents;
        echo "================================================================\n";
      }
      Utils::write($filepath, $file_contents, $this->getWriteScheme($filepath));
    }
  }
}
