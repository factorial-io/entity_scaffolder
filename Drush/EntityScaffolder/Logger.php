<?php

namespace Drush\EntityScaffolder;

class Logger {

  const LEVEL_WARNING = 'warning';
  const LEVEL_ERROR = 'error';
  const LEVEL_SUCCESS = 'success';
  const LEVEL_STATUS = 'status';
  const LEVEL_DEBUG = 'debug';

  public function formatMessage($message, $level = self::LEVEL_STATUS) {
    switch ($level) {
      case self::LEVEL_ERROR:   $prefix = "%w%F%1 (✖╭╮✖) %n%r "; break;

      case self::LEVEL_WARNING: $prefix = "%w%F%3 ¯\_(ツ)_/¯ %n%y "; break;

      case self::LEVEL_DEBUG:   $prefix = "%b ᒡ◯ᵔ◯ᒢ  "; break;

      case self::LEVEL_SUCCESS:  $prefix = "%g【ツ】%n "; break;

      case self::LEVEL_STATUS:  $prefix = "%n"; break;

      default: $prefix = "%n[$level] %n"; break;
    }
    return $prefix . $message . "%n\n";
  }

  public function log($message, $level = self::LEVEL_STATUS) {
    $formatted_message = self::formatMessage($message, $level);
    $color = new \Console_Color2();
    print $color->convert($formatted_message);
  }

  public function debug($var, $variable_name = 'var') {
    echo "\n----------------------------------------------------------------\n";
    echo $variable_name;
    echo "\n----------------------------------------------------------------\n";
    echo var_export($var);
    echo "\n----------------------------------------------------------------\n";
  }

  public function table($headers, $data, $level = self::LEVEL_STATUS) {
    $tbl = new \Console_Table();
    $tbl->setHeaders($headers);
    foreach ($data as $row) {
      $tbl->addRow($row);
    }
    $table = $tbl->getTable();
    self::log($table, $level);
  }
}
