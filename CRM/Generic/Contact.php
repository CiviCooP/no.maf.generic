<?php

/**
 * Class for Contact processing
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 July 2017
 * @license AGPL-3.0
 */
class CRM_Generic_Contact {

  /**
   * Method to process civicrm_hook_validateForm
   *
   * @param $formName
   * @param $fields
   * @param $form
   * @param $errors
   */
  public static function validateForm($formName, $fields, $form, &$errors) {
    // nick name is required
    if (!isset($fields['nick_name']) || empty($fields['nick_name'])) {
      $errors['nick_name'] = ts('Nick name can not be empty!');
    }
    return;
  }

  /**
   * Method to process civicrm_hook_searchTasks
   * @param $objectName
   * @param $tasks
   */
  public static function searchTasks($objectName, &$tasks) {
    $tasks[] = array(
      'title' => 'Export contacts with KID',
      'class' => 'CRM_Generic_Export', 'CRM_Export_Form_Map',
    );
  }
}