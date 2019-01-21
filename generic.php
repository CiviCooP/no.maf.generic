<?php

require_once 'generic.civix.php';

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
 * Implements hook_civicrm_sumfields_definitions().
 *
 * This hook is provided by the summary fields extension.
 * We change the trigger for the deductible amount fields because
 * that trigger calculates the total amount of all contributions with a financial type set to
 * deductible = 1.
 * We change it that we calculate the total amount minus the non deductible amount on the contribution.
 *
 * @param $custom
 */
function generic_civicrm_sumfields_definitions(&$custom) {
  $custom['fields']['contribution_total_deductible_this_year']['trigger_sql'] = '
    (SELECT (COALESCE(SUM(total_amount),0) - COALESCE(SUM(non_deductible_amount),0))
      FROM civicrm_contribution t1 JOIN civicrm_financial_type t2 ON
      t1.financial_type_id = t2.id
      WHERE CAST(receive_date AS DATE) BETWEEN "%current_fiscal_year_begin" AND
      "%current_fiscal_year_end" AND t1.contact_id = NEW.contact_id AND
      t1.contribution_status_id = 1 AND t1.financial_type_id IN (%financial_type_ids))
  ';
  $custom['fields']['contribution_total_deductible_last_year']['trigger_sql'] = '
    (SELECT (COALESCE(SUM(total_amount),0) - COALESCE(SUM(non_deductible_amount),0))
      FROM civicrm_contribution t1 JOIN civicrm_financial_type t2 ON
      t1.financial_type_id = t2.id
      WHERE CAST(receive_date AS DATE) BETWEEN "%last_fiscal_year_begin" AND
      "%last_fiscal_year_end" AND t1.contact_id = NEW.contact_id AND
      t1.contribution_status_id = 1 AND t1.financial_type_id IN (%financial_type_ids)) 
  ';
  $custom['fields']['contribution_total_deductible_year_before_last_year']['trigger_sql'] = '
    (SELECT (COALESCE(SUM(total_amount),0) - COALESCE(SUM(non_deductible_amount),0))
      FROM civicrm_contribution t1 JOIN civicrm_financial_type t2 ON
      t1.financial_type_id = t2.id
      WHERE CAST(receive_date AS DATE) BETWEEN "%year_before_last_fiscal_year_begin" AND
      "%year_before_last_fiscal_year_end" AND t1.contact_id = NEW.contact_id AND
      t1.contribution_status_id = 1 AND t1.financial_type_id IN (%financial_type_ids))
  ';
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
