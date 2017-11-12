<?php

namespace Drush\EntityScaffolder\d7_1;

interface ESBaseInterface {

  /**
   * Helper function to initialize code.
   */
  function initCode();

  /**
   * Set the directory from which the templates will be picked up.
   */
  function setTemplateDir($dir);

  /**
   * Gets the directory from which the templates will be picked up.
   */
  function getTemplateDir();

  /**
   * The main scaffolding action.
   */
  function scaffold();

  /**
   * Loads scaffold source files.
   */
  function loadScaffoldSourceConfigurations();

  /**
   * Helper function to load config and defaults.
   */
  function getConfig($file);

  /**
   * Validate the input files.
   */
  function variablesValidate($input, $defaults);

  /**
   * Validate the input files.
   */
  function variablesFillDefaults($input, $defaults);

}
