<?php

namespace Drush\EntityScaffolder;

class TemplateManager {

  const TEMPLATE_DEFAULT = 'DEFAULT';
  const TEMPLATE_EXTEND = 'EXTEND';
  const TEMPLATE_FALLBACK = 'FALLBACK';
  const WEIGHT_KEY_DEFAULT = 0;

  protected $templateDir;
  protected $extendedTemplateDirs;
  protected $nameSpace;
  protected $configDir;
  protected $config;

  /**
   * TemplateManager Constructor.
   */
  public function __construct($nameSpace) {
    $this->setNameSpace($nameSpace);
  }

  /**
   * Sets the directory from which the templates has to be picked up.
   */
  public function setTemplateDir($dir, $skipNamespaceAddition = FALSE) {
    if (!$skipNamespaceAddition) {
      $dir = $this->getNameSpacedTemplateDir($dir);
    }
    $message_opts = [
      '@namespace' => $this->getNameSpace(),
      '@dir' => $dir,
    ];
    $this->templateDir = $dir;
  }

  /**
   * NameSpace setter.
   */
  public function setNameSpace($nameSpace) {
    $this->nameSpace = $nameSpace;
  }

  /**
   * NameSpace getter.
   */
  public function getNameSpace() {
    return($this->nameSpace);
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return $this->templateDir;
  }

  /**
   * Gets all directories from which the templates could be picked up.
   */
  public function setExtendedTemplateDirs($weight, $dir, $skipNamespaceAddition = FALSE) {
    if (!$skipNamespaceAddition) {
      $dir = $this->getNameSpacedTemplateDir($dir);
    }
    $message_opts = [
      '@namespace' => $this->getNameSpace(),
      '@dir' => $dir,
    ];
    $this->extendedTemplateDirs[$weight] = $dir;
    uksort($this->extendedTemplateDirs, function ($a, $b) {
      return $a < $b ? -1 : 1;
    });
    return $this->extendedTemplateDirs;
  }

  /**
   * Gets all directories from which the templates could be picked up.
   */
  public function getExtendedTemplateDirs() {
    return $this->extendedTemplateDirs;
  }

  /**
   * Return the directory based on namespace.
   */
  private function getNameSpacedTemplateDir($dir) {
    return $dir . '/' . $this->getNameSpace();
  }

  /**
   *
   */
  protected function loadTemplateDirs() {
    $config = $this->getConfig();
    $fallback_templates = [];
    $extend_templates = [];
    if (!empty($config['templates'])) {
      foreach ($config['templates'] as $key => $value) {
        $dir = getcwd() . '/' . $this->getConfigDir() . $value['dir'];
        switch ($value['type']) {
          case $this::TEMPLATE_DEFAULT:
            $this->setTemplateDir($dir);
            $this->setExtendedTemplateDirs(self::WEIGHT_KEY_DEFAULT, $dir);
            break;

          case $this::TEMPLATE_EXTEND:
            $extend_templates[] = $dir;
            break;

          case $this::TEMPLATE_FALLBACK:
            $fallback_templates[] = $dir;
            break;
        }
      }
      // Extended templates should always have weight less than
      // self::WEIGHT_KEY_DEFAULT.
      if ($extend_templates) {
        $weight = self::WEIGHT_KEY_DEFAULT - count($extend_templates) - 1;
        foreach ($extend_templates as $key => $value) {
          $weight++;
          $this->setExtendedTemplateDirs($weight, $dir);
        }
      }

      // Fallback templates should always have weight more than
      // self::WEIGHT_KEY_DEFAULT.
      if ($fallback_templates) {
        $weight = self::WEIGHT_KEY_DEFAULT;
        foreach ($fallback_templates as $key => $value) {
          $weight++;
          $this->setExtendedTemplateDirs($weight, $dir);
        }
      }
    }
  }

  /**
   * Add to $config.
   */
  public function setConfig($config) {
    $this->config = $config;
  }

  /**
   * Gets the $config.
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Sets the directory from which the scaffold data will be picked up.
   */
  public function setConfigDir($dir) {
    $this->configDir = $dir;
  }

  /**
   * Gets the directory from which the scaffold data will be picked up.
   */
  public function getConfigDir() {
    return $this->configDir;
  }

}
