<?php

/**
 * @file
 * Class for the Panelizer fieldable_panels_pane entity plugin.
 */

/**
 * Panelizer Entity fieldable_panels_pane term plugin class.
 *
 * Handles term specific functionality for Panelizer.
 */
class EsHelperPanelsPaneController extends PanelsPaneController {

  /**
   * Add support for a global catch all permission.
   */
  public function access($op, $entity = NULL, $account = NULL) {
    if (user_access(ES_HELPER_PERMISSION_MANAGE_ALL_FPPS, $account)) {
      return TRUE;
    }
    return parent::access($op, $entity, $account);
  }

}
