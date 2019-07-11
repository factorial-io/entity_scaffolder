<?php

namespace Drush\EntityScaffolder;

use Symfony\Component\Finder\Finder;

class TranslationExtractor extends ScaffolderBase {
  const TEMPLATE_NAMESPACE = 'este';

  protected $include_patterns;
  protected $exclude_patterns;
  protected $twig_template_directories;
  protected $output_dir;
  protected $ignore_dir;

  /**
   * TranslationExtractor constructor.
   */
  public function __construct() {
    parent::__construct(self::TEMPLATE_NAMESPACE);
    $config = $this->loadConfig();
    if (empty($config)) {
      Logger::log('translation.extractor configuration missing in ' . $this->getConfigDir() . '/config.yaml', 'warning');
      return ;
    }
    $this->setExcludePatterns($config['drupal']['exclude']);
    $this->setIncludePatterns($config['drupal']['include']);
    $this->setOutputDir($config['output_dir']);
    $this->setIgnoreDir($config['ignore_dir']);
    $this->setTwigTemplateDirectories($config['twig']);
  }

  /**
   * Extract Translations.
   */
  public function extract() {
    $config = $this->loadConfig();
    if (empty($config)) {
      Logger::log('Extraction aborted due to missing configuration.', Logger::LEVEL_ERROR);
      return ;
    }
    $this->extractFromDrupal();
    $this->extractFromTwig();
    Logger::log('Extraction completed', Logger::LEVEL_SUCCESS);
  }

  /**
   * Extract Normal Drupal files.
   */
  private function extractFromDrupal() {
    $finder = new Finder();
    $finder->files()->name(['*.php', '*.inc', '*.module']);
    foreach ($this->getIncludePatterns() as $pattern) {
      $finder->in(getcwd() . $pattern);
    }
    foreach ($this->getExcludePatterns() as $pattern) {
      $finder->exclude($pattern);
    }
    $commands_array = [
      'php',
      __DIR__ . '/potx/potx-cli.php',

    ];
    if ($this->getIgnoreDir()) {
      $commands_array[] = '--ignore_dir';
      $commands_array[] = getcwd() . $this->getIgnoreDir();
    }

    $commands_array[] = '--files';
    foreach ($finder as $file) {
      $commands_array[] = $file;
    }
    $this->deleteFile('/general.pot');
    $this->deleteFile('/installer.pot');
    exec(implode(' ', $commands_array));
    $this->moveFile('/general.pot','/general.pot');
    $this->moveFile('/installer.pot','/installer.pot');
    Logger::log('Extracted strings from Drupal Files', Logger::LEVEL_SUCCESS);
  }

  /**
   * Extract Normal Twig files.
   */
  private function extractFromTwig() {
    if (empty($this->getTwigTemplateDirectories() )) {
      return ;
    }
    foreach ($this->getTwigTemplateDirectories() as $directory) {
      // @see https://twig-extensions.readthedocs.io/en/latest/i18n.html.
      // @TODO Compile all twig files to a temp directory.
      $tplDir = getcwd() . $directory;
      $tmpDir = '/tmp/cache/twig_cache';
      exec('rm -rf ' . $tmpDir);
      $loader = new \Twig_Loader_Filesystem($tplDir);
      $loader->loadFromExtension('twig', [
        'paths' => [
          '/sites/all/themes/bi_portal_frontend/source/_patterns/00-protons' => 'protons',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/01-atoms' => 'atoms',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/02-molecules' => 'molecules',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/03-organisms' => 'organisms',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/04-templates' => 'templates',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/05-pages' => 'pages',
        ],
      ]);
      // force auto-reload to always have the latest version of the template
      $twig = new \Twig_Environment($loader, array(
        'cache' => $tmpDir,
        'auto_reload' => true,
        'paths' => [
          '/sites/all/themes/bi_portal_frontend/source/_patterns/00-protons' => 'protons',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/01-atoms' => 'atoms',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/02-molecules' => 'molecules',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/03-organisms' => 'organisms',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/04-templates' => 'templates',
          '/sites/all/themes/bi_portal_frontend/source/_patterns/05-pages' => 'pages',
        ],
      ));

      $twig->addExtension(new \Twig_Extensions_Extension_I18n());

      $fake_filters = [
        't' => 't',
        'midip_t' => 'st',
        'form' => 'form',
        'without' => 'without',
      ];
      foreach ($fake_filters as $k => $v) {
        $filter = new \Twig\TwigFilter($k, $v);
        $twig->addFilter($filter);
      }

      $twig->addExtension(new \Twig_Extensions_Extension_I18n());
      // Configure Twig the way you want.
      // Iterate over all your templates.
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tplDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
        // Force compilation.
        $info = pathinfo($file);
        if ($file->isFile() && $info['extension'] == 'twig') {
          try {
            $twig->loadTemplate(str_replace($tplDir.'/', '', $file));
          }
          catch (exception $exception) {
            Logger::log('Unable to load template', Logger::LEVEL_ERROR);
          }
        }
      }
    }

    $commands_array = [
      'php',
      '~/.drush/entity_scaffolder/vendor/bin/gettext-extractor.php',
      '-f',
      $tmpDir,
      '-o',
      getcwd() . $this->getOutputDir() . '/theme.pot',
    ];

    $finder = new Finder();
    $finder->files()->name(['*.php', '*.inc', '*.module']);
    $finder->in($tmpDir);
    $commands_array = [];

    $commands_array[] = 'php';
    $commands_array[] = __DIR__ . '/potx/potx-cli.php';

    if ($this->getIgnoreDir()) {
      $commands_array[] = '--ban_list';
      $commands_array[] = getcwd() . $this->getIgnoreDir();
    }

    $commands_array[] = '--files';
    foreach ($finder as $file) {
      $commands_array[] = $file;
    }

    $this->deleteFile('/general.pot');
    $this->deleteFile('/installer.pot');
    exec(implode(' ', $commands_array));
    $this->moveFile('/general.pot','/theme-general.pot');
    $this->moveFile('/installer.pot','/theme-installer.pot');
    Logger::log('Extracted strings from Twig Files', Logger::LEVEL_SUCCESS);
  }

  /**
   * Helper method to remove a file.
   */
  private function deleteFile($filename) {
    $full_file_path = getcwd() . $filename;
    if (file_exists($full_file_path)) {
      unlink($full_file_path);
    }
  }

  /**
   * Helper method to remove a file.
   */
  private function moveFile($current, $target) {
    $current_file_path = getcwd() . $current;
    $target_file_path = getcwd() . $this->getOutputDir() . $target;
    if (file_exists($current_file_path)) {
      rename($current_file_path, $target_file_path);
    }
  }

  /**
   * Load configuration data.
   */
  protected function loadConfig() {
    $config = Utils::getConfig($this->getConfigDir() . '/config.yaml');
    if (empty($config['translation']['extractor'])) {
      return NULL;
    }
    return $config['translation']['extractor'];
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
  public function setIgnoreDir($filename) {
    $this->ignore_dir = $filename;
  }

  /**
   * Getter for $ignore_csv_file.
   */
  public function getIgnoreDir() {
    return $this->ignore_dir;
  }

  /**
   * Setter for $twig_template_directories.
   */
  public function setTwigTemplateDirectories($twig_template_directories) {
    $this->twig_template_directories = $twig_template_directories;
  }

  /**
   * Getter for $twig_template_directories.
   */
  public function getTwigTemplateDirectories() {
    return $this->twig_template_directories;
  }
}
