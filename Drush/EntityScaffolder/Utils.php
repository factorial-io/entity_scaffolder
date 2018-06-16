<?php

namespace Drush\EntityScaffolder;

use Symfony\Component\Yaml\Parser;

class Utils {

  const FILE_EXISTS_OVERWRITE = 'OVERWRITE';
  const FILE_EXISTS_SKIP = 'SKIP';
  const TWIG_COMMENT = 'TWIG';


  /**
   * Helper function to get list of entities to create.
   */
  public static function getFolders($config_dir) {
    $folders = array();
    foreach (glob($config_dir . '/*', GLOB_ONLYDIR) as $dir) {
      $dirname = basename($dir);
      $folders[] = $dirname;
    }
    return $folders;
  }


  /**
   * Helper function to get list of yaml files.
   */
  function getConfigFiles($config_dir) {
    $files = array();
    foreach (glob($config_dir . '/*.yaml') as $file) {
      $files[] = $file;
    }
    return $files;
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config = array();
    if (file_exists($file)) {
      $yaml = new Parser();
      $config = $yaml->parse(file_get_contents($file));
    }
    return $config;
  }

  /**
   * Helper functions to copy folder contents to another location.
   */
  public function copyFolderContents($source, $destination) {
    if (!file_exists($destination) && !is_dir($destination)) {
      if (mkdir($destination)) {
        Logger::log(dt('Created directory : @dir', array('@dir' => $destination)), 'success');
      }
      else {
        Logger::log(dt('Error while creating directory : @dir', array('@dir' => $destination)), 'error');
      }
    }
    $files = array();
    foreach (glob($source . '/*.*') as $file) {
      $file_name = basename($file);
      if (copy($file, $destination . '/' . $file_name)) {
        Logger::log(dt('Copied file : @file_name', array('@file_name' => $file_name)), 'success');
      }
      else {
        Logger::log(dt('Erorr while copying file : @file_name', array('@file_name' => $file_name)), 'error');
      }
    }
  }

  /**
   * Helper function to write data into files.
   */
  function write($filepath, $file_contents, $param = self::FILE_EXISTS_OVERWRITE) {
    switch ($param) {
      case self::FILE_EXISTS_OVERWRITE:
        self::writeFile($filepath, $file_contents);
        break;

      case self::FILE_EXISTS_SKIP:
        if (!self::fileNotEmpty($filepath)) {
          self::writeFile($filepath, $file_contents);
        }
        break;

      case self::TWIG_COMMENT:
        if (self::fileNotEmpty($filepath)) {
          // read file.
          $data = file_get_contents($filepath);
          $matches = array();
          if ( preg_match("/^{#([^}]*)#}/", $data, $matches) ) {
            $first_comment = $matches[1];
          }

          if ($first_comment) {
            $first_comment = "{#$first_comment#}\n";
            $str = substr($data, strlen($first_comment));
            $file_contents = $file_contents . $str;
          }
          else {
            $file_contents = $file_contents . $data;
          }
          self::writeFile($filepath, $file_contents);
        }
        else {
          self::writeFile($filepath, $file_contents);
        }
        break;
    }
  }

  /**
   * Helper function to write file.
   */
  function writeFile($filepath, $file_contents) {
    $parent = dirname($filepath);
    if (!is_dir($parent)) {
      mkdir($parent, 0777, TRUE);
    }
    if (file_put_contents($filepath, $file_contents) === FALSE) {
      Logger::log(dt('Error while writing to file @file', array('@file' => $filepath)), 'error');
    }
    else {
      Logger::log(dt('Updated @file', array('@file' => $filepath)), 'success');
    }
  }

  /**
   * Helper function to render using Twig.
   */
  function renderExtended($dirs, $template, $replacements) {
    foreach ($dirs as $weight => $dir) {
      $extention = pathinfo($template, PATHINFO_EXTENSION);
      switch ($extention) {
        case '_twig':
        case 'twig':
          break;

        default:
          $template .= '.twig';
          break;
      }
      if (file_exists($dir . $template)) {
        break;
      }
    }
    $loader = new \Twig_Loader_Filesystem($dir);
    $twig = new \Twig_Environment($loader, array(
      'debug' => TRUE,
    ));
    $twig->addExtension(new \Twig_Extension_Debug());
    return $twig->render($template, $replacements);
  }

  /**
   * Helper function to render using Twig.
   */
  function render($dir, $template, $replacements) {
    $loader = new \Twig_Loader_Filesystem($dir);
    $twig = new \Twig_Environment($loader, array(
      'debug' => TRUE,
    ));
    $twig->addExtension(new \Twig_Extension_Debug());

    $extention = pathinfo($template, PATHINFO_EXTENSION);

    switch ($extention) {
      case '_twig':
      case 'twig':
        break;

      default:
        $template .= '.twig';
        break;
    }

    return $twig->render($template, $replacements);
  }

  /**
   * Helper function to render using Twig.
   */
  function renderInline($pattern, $replacements) {
    $loader = new \Twig_Loader_Array(array(
      'pattern.twig' => $pattern,
    ));
    $twig = new \Twig_Environment($loader);
    return $twig->render('pattern.twig', $replacements);
  }

  /**
   * Helper function to check if a string starts with given string.
   */
  function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }

  /**
   * Helper function to check if a string ends with given string.
   */
  function endsWith($haystack, $needle){
    $length = strlen($needle);
    return $length === 0 ||
    (substr($haystack, -$length) === $needle);
  }

  function fileNotEmpty($file_to_test) {
    if (file_exists($file_to_test)) {
      clearstatcache();
      $stat = stat($file_to_test);
      if ($stat['size'] > 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

  function debug($var, $name = 'var') {
    echo "\n----------------------------------------------------------------\n";
    echo $name;
    echo "\n----------------------------------------------------------------\n";
    echo var_export($var);
    echo "\n----------------------------------------------------------------\n";
  }
}
