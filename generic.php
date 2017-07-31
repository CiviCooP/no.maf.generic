<?php

require_once 'generic.civix.php';

/**
 * Implements hook_civicrm_searchTasks().
 *
 *@link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_searchTasks/
 */
function generic_civicrm_searchTasks($objectName, &$tasks) {
  if ($objectName == 'contact') {
    CRM_Generic_Contact::searchTasks($objectName, $tasks);
  }
}

/**
 * Implements hook_civicrm_export().
 *
 *@link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_export/
 */
function generic_civicrm_export(&$exportTempTable, &$headerRows, &$sqlColumns, &$exportMode) {
  CRM_Generic_Export::export($exportTempTable, $headerRows, $sqlColumns, $exportMode);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_buildForm/
\ */
function generic_civicrm_buildForm ($formName, &$form) {
  if ($formName == 'CRM_Export_Form_Select') {
    CRM_Generic_Export::buildForm($formName, $form);
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 *@link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_validateForm/
 */
function generic_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  switch ($formName) {
    case "CRM_Contact_Form_Contact":
      CRM_Generic_Contact::validateForm($formName, $fields, $form, $errors);
      break;
    case "CRM_Contact_Form_Inline_ContactInfo":
      CRM_Generic_Contact::validateForm($formName, $fields, $form, $errors);
      break;
    case "CRM_Export_Form_Select":
      CRM_Generic_Export::validateForm($formName, $fields, $form, $errors);
      break;
  }
}

/**
 * Implements hook_civicrm_post().
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_post/
 *
 */
function generic_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  CRM_Generic_Campaign::post($op, $objectName, $objectId, $objectRef);
}
/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function generic_civicrm_config(&$config) {
  _generic_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function generic_civicrm_xmlMenu(&$files) {
  _generic_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function generic_civicrm_install() {
  _generic_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function generic_civicrm_postInstall() {
  _generic_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function generic_civicrm_uninstall() {
  _generic_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function generic_civicrm_enable() {
  _generic_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function generic_civicrm_disable() {
  _generic_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function generic_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _generic_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function generic_civicrm_managed(&$entities) {
  _generic_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function generic_civicrm_caseTypes(&$caseTypes) {
  _generic_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function generic_civicrm_angularModules(&$angularModules) {
  _generic_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function generic_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _generic_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function generic_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function generic_civicrm_navigationMenu(&$menu) {
  _generic_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'no.maf.generic')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _generic_civix_navigationMenu($menu);
} // */
