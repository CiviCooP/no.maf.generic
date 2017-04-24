<?php

/**
 * Class for Campaign processing
 * Initially: populate and keep updated option group maf_partners_campaign with active campaigns
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 7 Feb 2017
 * @license AGPL-3.0
 */
class CRM_Generic_Campaign {

  private $_optionGroupName = NULL;

  /**
   * CRM_Generic_Campaign constructor.
   */
  public function __construct() {
    $this->_optionGroupName = 'maf_partners_campaign';
  }

  /**
   * Method to process the civicrm post hook
   * @param $op
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   */
  public static function post($op, $objectName, $objectId, $objectRef) {
    if ($objectName == 'Campaign') {
      $mafCampaign = new CRM_Generic_Campaign();
      switch ($op) {
        case 'create':
          if (isset($objectRef->title)) {
            $mafCampaign->add($objectId, $objectRef->title);
          } else {
            $mafCampaign->add($objectId);
          }
          break;
        case 'edit':
          // update with new title if active else remove because disabled
          if ($objectRef->is_active == 1) {
            $mafCampaign->update($objectId, $objectRef->title);
          } else {
            $mafCampaign->delete($objectId);
          }
          break;
        case 'delete':
          $mafCampaign->delete($objectId);
          break;
      }
    }
  }

  /**
   * Method to change an option value label when the campaign title has changed
   *
   * @param $campaignId
   * @param $campaignTitle
   * @throws Exception when campaignId is empty or error from API OptionValue getsingle or create
   */
  public function update($campaignId, $campaignTitle = NULL) {
    if (empty($campaignId)) {
      throw new Exception('CampaignId can not be empty in '.__METHOD__.', contact your system administrator');
    }
    // if not exists yet (was disabled and is now enabled again) add
    if ($this->exists($campaignId) == FALSE) {
      $this->add($campaignId, $campaignTitle);
    } else {
      try {
        $optionValue = civicrm_api3('OptionValue', 'getsingle', array(
          'option_group_id' => $this->_optionGroupName,
          'value' => $campaignId
        ));
      } catch (CiviCRM_API3_Exception $ex) {
        throw new Exception('Could not identify option value for campaign in ' . __METHOD__
          . ', contact your system administrator. Error from API OptionValue getsingle: ' . $ex->getMessage());
      }
      $optionValue['label'] = $campaignTitle;
      try {
        civicrm_api3('OptionValue', 'create', $optionValue);
      } catch (CiviCRM_API3_Exception $ex) {
        throw new Exception('Could not update option value with campaign title in ' . __METHOD__
          . ', contact your system administrator. Error from API OptionValue create: ' . $ex->getMessage());
      }
    }
  }

  /**
   * Method to remove an option value when a campaign is deleted or disabled
   *
   * @param $campaignId
   * @throws Exception when campaignId is empty or error from API OptionValue delete or getvalue
   */
  public function delete($campaignId) {
    if (empty($campaignId)) {
      throw new Exception('CampaignId can not be empty in '.__METHOD__.', contact your system administrator');
    }
    try {
      $optionValueId = civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => $this->_optionGroupName,
        'value' => $campaignId,
        'return' => 'id'
      ));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find an option value for campaign in '.__METHOD__
        .', contact your system administrator. Error from API OptionValue getvalue: '.$ex->getMessage());
    }
    try {
      civicrm_api3('OptionValue', 'delete', array('id' => $optionValueId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not delete option value in '.__METHOD__
        .', contact your system administrator. Error from API OptionValue delete: '.$ex->getMessage());
    }
  }

  /**
   * Method to add a campaign to the option group
   *
   * @param $campaignId
   * @param $campaignTitle
   * @throws Exception when mandatory params not set or error from API OptionValue create
   */
  public function add($campaignId, $campaignTitle = NULL) {
    $txt = NULL;
    // campaignId can not be empty
    if (empty($campaignId)) {
      throw new Exception('CampaignId can not be empty in '.__METHOD__.', contact your system administrator');
    }
    // retrieve title if not passed
    if (empty($campaignTitle)) {
      try {
        $campaignTitle = civicrm_api3('Campaign', 'getvalue', array('id' => $campaignId, 'return' => 'title'));
      } catch (CiviCRM_API3_Exception $ex) {
        throw new Exception('Could not find a campaign with id '.$campaignId.' in '.__METHOD__.', contact your system administrator');
      }
    }
    // add if option value does not exist yet
    if ($this->exists($campaignId) == FALSE) {
      try {
        civicrm_api3('OptionValue', 'create', array(
          'option_group_id' => $this->_optionGroupName,
          'label' => $campaignTitle,
          'value' => $campaignId,
          'is_active' => 1,
          'is_reserved' => 1));
      } catch (CiviCRM_API3_Exception $ex) {
        throw new Exception('Error when adding campaign '.$campaignTitle.'to the option group '.$this->_optionGroupName
          .' in '.__METHOD__.', contact your system administrator. Error from API OptionValue create: '.$ex->getMessage());
      }
    }
  }

  /**
   * Method to check if option value already exists
   *
   * @param $campaignId
   * @return bool
   */
  private function exists($campaignId) {
    $count = civicrm_api3('OptionValue', 'getcount', array(
      'option_group_id' => $this->_optionGroupName,
      'value' => $campaignId));
    if ($count > 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Method to initially load the campaigns
   */
  public static function initialLoad() {
    try {
      $campaigns = civicrm_api3('Campaign', 'get', array('is_active' => 1));
      foreach ($campaigns['values'] as $campaign) {
        $mafCampaign = new CRM_Generic_Campaign();
        $mafCampaign->add($campaign['id'], $campaign['title']);
      }
    } catch (CiviCRM_API3_Exception $ex) {}
  }
}