<?php

class ESEntityBase {

  protected $scaffolder;

  public function __construct(ScaffolderBase $scaffolder) {
    $this->scaffolder = $scaffolder;
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    return ;
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    return Utils::getConfig($file);
  }

}
