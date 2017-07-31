
  // Preprocess %field_name%.
  $vars['%map%'] = array();
  if(!empty($vars['%field_name%'])) {
    foreach($vars['%field_name%'] as $value) {
      $vars['%map%'][] = isset($value['value']) ? $value['value'] : '';
    }
  }
