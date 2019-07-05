<?php

namespace Drush\EntityScaffolder;

use Symfony\Component\Finder\Finder;

class TranslationExtractor extends ScaffolderBase {
  const TEMPLATE_NAMESPACE = 'este';

  protected $include_patterns;
  protected $exclude_patterns;
  protected $output_dir;
  protected $ignore_csv_file;

  /**
   * TranslationExtractor constructor.
   */
  public function __construct() {
    parent::__construct(self::TEMPLATE_NAMESPACE);
    $config = $this->loadConfig();
    $this->setExcludePatterns($config['exclude']);
    $this->setIncludePatterns($config['include']);
    $this->setOutputDir($config['output_dir']);
    $this->setIgnoreCsvFile($config['ban_list']);
  }

  /**
   * Extract Translations.
   */
  public function extract() {
    $finder = new Finder();
    $finder->files()->name(['*.php', '*.inc', '*.module']);
    foreach ($this->getIncludePatterns() as $pattern) {
      $finder->in(getcwd() . $pattern);
    }
    foreach ($this->getExcludePatterns() as $pattern) {
      $finder->exclude($pattern);
    }

    $commands_array = [];

    $commands_array[] = 'php';
    $commands_array[] = __DIR__ . '/potx/potx-cli.php';

    if ($this->getIgnoreCsvFile()) {
      $commands_array[] = '--ban_list';
      $commands_array[] = getcwd() . $this->getIgnoreCsvFile();
    }

    $commands_array[] = '--files';
    foreach ($finder as $file) {
      $commands_array[] = $file;
    }

    $output = exec(implode(' ', $commands_array));

    rename(getcwd() . '/general.pot', getcwd() . $this->getOutputDir() . '/general.pot');
    rename(getcwd() . '/installer.pot', getcwd() . $this->getOutputDir() . '/installer.pot');
    Logger::log($output);
  }

  /**
   * Load configuration data.
   */
  protected function loadConfig() {
    $config = Utils::getConfig($this->getConfigDir() . '/config.yaml');
    return $config['translation_extractor'];
  }

  /**
   * Setter for $include_patterns.
   */
  public function setIncludePatterns(array $patterns) {
    $this->include_patterns = $patterns;
  }

  /**
   * Getter for $include_patterns.
   */
  public function getIncludePatterns() {
    return $this->include_patterns;
  }

  /**
   * Setter for $exclude_patterns.
   */
  public function setExcludePatterns(array $patterns) {
    $this->exclude_patterns = $patterns;
  }

  /**
   * Getter for $exclude_patterns.
   */
  public function getExcludePatterns() {
    return $this->exclude_patterns;
  }

  /**
   * Setter for $output_dir.
   */
  public function setOutputDir($dir) {
    $this->output_dir = $dir;
  }

  /**
   * Getter for $output_dir.
   */
  public function getOutputDir() {
    return $this->output_dir;
  }

  /**
   * Setter for $ignore_csv_file.
   */
  public function setIgnoreCsvFile($filename) {
    $this->ignore_csv_file = $filename;
  }

  /**
   * Getter for $ignore_csv_file.
   */
  public function getIgnoreCsvFile() {
    return $this->ignore_csv_file;
  }
}
