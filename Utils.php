<?php

class Utils {

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
    $config = file_exists($file) ? Spyc::YAMLLoad($file) : array();
    return $config;
  }

  /**
   * Helper functions to copy folder contents to another location.
   */
  public function copyFolderContents($source, $destination) {
    if (!file_exists($destination) && !is_dir($destination)) {
      if (mkdir($destination)) {
        drush_log(dt('Created directory : @dir', array('@dir' => $destination)), 'success');
      }
      else {
        drush_log(dt('Error while creating directory : @dir', array('@dir' => $destination)), 'error');
      }
    }
    $files = array();
    foreach (glob($source . '/*.*') as $file) {
      $file_name = basename($file);
      if (copy($file, $destination . '/' . $file_name)) {
        drush_log(dt('Copied file : @file_name', array('@file_name' => $file_name)), 'success');
      }
      else {
        drush_log(dt('Erorr while copying file : @file_name', array('@file_name' => $file_name)), 'success');
      }
    }
  }
}