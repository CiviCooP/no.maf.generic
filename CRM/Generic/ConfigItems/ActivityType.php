<?php
/**
 * Class for ActivityType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 7 Feb 2017
 * @license AGPL-3.0
 */
class CRM_Generic_ConfigItems_ActivityType extends CRM_Generic_ConfigItems_OptionValue {
  /**
   * Overridden parent method to validate params for create
   *
   * @param $params
   * @throws Exception when missing mandatory params
   */
  protected function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception(ts('Missing mandatory param name in class CRM_Generic_ConfigItems_ActivityType'));
    }
    $this->_apiParams = $params;
    try {
      $this->_apiParams['option_group_id'] = $this->getOptionGroupId();
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Unable to find option group for activity_type in CRM_Generic_ConfigItems_ActivityType, contact your system administrator'));
    }
  }

  /**
   * Method to get option group id for activity type
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function getOptionGroupId() {
    return civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'activity_type', 'return' => 'id'));
  }
}