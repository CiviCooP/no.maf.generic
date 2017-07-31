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

}