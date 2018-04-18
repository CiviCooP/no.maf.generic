<?php

/**
 * Class to export contacts with KID
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 July 2017
 * @license AGPL-3.0
 */
class CRM_Generic_Export extends CRM_Export_Form_Select {
	
	protected static $campaign_id = false;
	
	public function buildQuickForm() {
		parent::buildQuickForm();
		// add campaign select field
    $campaignSelectList = array('- select campaign -');
    $campaigns = civicrm_api3('Campaign', 'get', array(
      'is_active' => 1,
      'options' => array('limit' => 0,),
      ));
    foreach ($campaigns['values'] as $campaignId => $campaignData) {
      $campaignSelectList[$campaignId] = $campaignData['title'];
    }
    asort($campaignSelectList);
    $this->add('select', 'campaign_id', ts('Campaign (for KID)'), $campaignSelectList, true, array('class' => 'crm-select2'));
		
		$this->addFormRule(array('CRM_Generic_Export', 'validateCampaign'), $this);
	}
	
	public static function validateCampaign($fields, $files, $self) {
		$errors = array();	
		if (!isset($fields['campaign_id']) || empty($fields['campaign_id'])) {
        $errors['campaign_id'] = ts('You have to select a campaign when exporting contacts with a KID');
    }
		return $errors;
	}
	
	public function postProcess() {
		$params = $this->controller->exportValues($this->_name);
		self::$campaign_id = $params['campaign_id'];
		parent::postProcess();
	}

  /**
   * Method to process the civicrm hook export
   *
   * @param $exportTempTable
   * @param $headerRows
   * @param $sqlColumns
   * @param $exportMode
   */
  public static function export(&$exportTempTable, &$headerRows, &$sqlColumns, &$exportMode) {
    // only for contacts and only when a campaign has been selected
    if ($exportMode == 1 && self::$campaign_id > 0) {
      $sql = "ALTER TABLE " . $exportTempTable . " ADD COLUMN kid_number VARCHAR(45)";
      CRM_Core_DAO::singleValueQuery($sql);
      $headerRows[] = "KID";
      $sqlColumns['kid_number'] = 'kid_number VARCHAR(45)';

      // update temp table
      $dao = CRM_Core_DAO::executeQuery("SELECT * FROM " . $exportTempTable);
      while ($dao->fetch()) {
        try {
          $kid = civicrm_api3('Kid', 'generate', array(
            'campaign_id' => self::$campaign_id,
            'contact_id' => $dao->civicrm_primary_id,
          ));
          $kidNumber = $kid['kid_number'];
        } catch (CiviCRM_API3_Exception $ex) {
          $kidNumber = '';
        }
        $query = "UPDATE ".$exportTempTable." SET kid_number = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($query, array(
          1 => array($kidNumber, 'String'),
          2 => array($dao->id, 'Integer'),
        ));
      }
    }
    return;
  }
}
