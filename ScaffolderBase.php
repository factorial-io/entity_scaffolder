<?php

require_once "Utils.php";

class ScaffolderBase {

  const HEADER = '_00_';
  const CONTENT = '_50_';
  const FOOTER = '_99_';

  protected $config_dir;
  protected $entity_types;
  protected $template_dir;
  protected $code;

  public function __construct() {
    $this->setConfigDir($this->findConfigDir());
    $this->setEntityTypes($this->findEntityTypes());
  }

  protected function findConfigDir() {
    if (!($config_dir = drush_get_option('config-dir'))) {
      $config_dir = '_tools/es';
    }
    return $config_dir;
  }

  protected function findEntityTypes() {
    return Utils::getFolders($this->getConfigDir());
  }

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setEntityTypes($types) {
    $this->entity_types = $types;
  }

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getEntityTypes() {
    return $this->entity_types;
  }

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setConfigDir($dir) {
    $this->config_dir = $dir;
  }

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getConfigDir() {
    return $this->config_dir;
  }

  /**
   * Sets the directory from which the templates has to be picked up.
   */
  public function setTemplateDir($dir) {
    $this->template_dir = $dir;
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return $this->template_dir;
  }


  /**
   * Add to $code.
   */
  public function setCode($module, $filename, $block, $key, $code) {
    $this->code[$module][$filename][$block][$key] = $code;
  }

  /**
   * Gets the $code.
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Start scaffolding.
   */
  public function scaffold() {
    return ;
  }

  /**
   * Helper function to perform text replacement.
   */
  function render($template, $replacements) {
    return drush_entity_scaffolder_twig_render($this->getTemplateDir(), $template, $replacements);
  }

}
