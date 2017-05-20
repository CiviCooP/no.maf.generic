<?php
/**
 * Class for Campaign configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 20 May 2017
 * @license AGPL-3.0
 */
class CRM_Generic_ConfigItems_Campaign {

  protected $_apiParams = array();

  /**
   * CRM_Generic_ConfigItems_Campaign constructor.
   */
  public function __construct() {
    $this->_apiParams = array();
  }

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception when missing mandatory params
   */
  protected function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in '.__METHOD__);
    }
    $this->_apiParams = $params;
  }

  /**
   * Method to create or update campaign
   *
   * @param $params
   * @return array
   * @throws Exception when error in API Campaign Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithNameAndType($this->_apiParams['name'], $this->_apiParams['campaign_type_id']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    $this->_apiParams['is_active'] = 1;
    $this->_apiParams['is_reserved'] = 1;
    if (!isset($this->_apiParams['title'])) {
      $this->_apiParams['title'] = ucfirst($this->_apiParams['name']);
    }
    try {
      $campaign = civicrm_api3('Campaign', 'create', $this->_apiParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Could not create or update campaign with name'
        .$this->_apiParams['name'].', error from API Campaign Create: ') . $ex->getMessage());
    }
  }

  /**
   * Function to get the campaign with name and type
   *
   * @param string $name
   * @param int $typeId
   * @return array|boolean
   */
  public function getWithNameAndType($name, $typeId) {
    $params = array(
      'name' => $name,
      'campaign_typoe_id' => $typeId,
      );
    try {
      return civicrm_api3('Campaign', 'getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Method to disable campaigns
   *
   * @param $campaignName
   * @param $campaignTypeId
   */
  public function disable($campaignName, $campaignTypeId) {
    if (!empty($campaignName) && !empty($campaignTypeId)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get campaign id with name and type
        $campaignId = civicrm_api3('Campaign', 'getvalue', array(
          'name' => $campaignName,
          'campaign_type_id' => $campaignTypeId,
          'return' => 'id'));
        $sql = "UPDATE civicrm_campaign SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sql, array(
          1 => array(0, 'Integer'),
          2 => array($campaignId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable option group
   *
   * @param $campaignName
   * @param $campaignTypeId
   */
  public function enable($campaignName, $campaignTypeId) {
    if (!empty($campaignName) && !empty($campaignTypeId)) {
      // catch any errors and ignore (enabling can be done manually if problems)
      try {
        // get campaign id with name and type
        $campaignId = civicrm_api3('Campaign', 'getvalue', array(
          'name' => $campaignName,
          'campaign_type_id' => $campaignTypeId,
          'return' => 'id'));
        $sql = "UPDATE civicrm_campaign SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sql, array(
          1 => array(1, 'Integer'),
          2 => array($campaignId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to uninstall campaign
   *
   * @param $campaignName
   * @param $campaignTypeId
   */
  public function uninstall($campaignName, $campaignTypeId) {
    if (!empty($campaignName) && !empty($campaignTypeId)) {
      // catch any errors and ignore (uninstalling can be done manually if problems)
      try {
        // get campaign id with name and type
        $campaignId = civicrm_api3('Campaign', 'getvalue', array(
          'name' => $campaignName,
          'campaign_type_id' => $campaignTypeId,
          'return' => 'id'));
        civicrm_api3('Campaign', 'delete', array('id' => $campaignId));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }
}