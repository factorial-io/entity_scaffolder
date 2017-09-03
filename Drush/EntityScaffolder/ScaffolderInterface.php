<?php

namespace Drush\EntityScaffolder;

interface ScaffolderInterface {

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setEntityTypes($types);

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getEntityTypes();

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setConfigDir($dir);

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getConfigDir();

  /**
   * Sets the directory from which the templates has to be picked up.
   */
  public function setTemplateDir($dir);

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir();

  /**
   * Add to $code.
   */
  public function setCode($module, $filename, $block, $key, $code);

  /**
   * Gets the $code.
   */
  public function getCode();

  /**
   * Start scaffolding.
   */
  public function scaffold();

  /**
   * Helper function to perform text replacement.
   */
  public function render($template, $replacements);

}
