<?php

/**
 * Class to export contacts with KID
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 July 2017
 * @license AGPL-3.0
 */
class CRM_Generic_Export {

  /**
   * Method to process civicrm hook buildForm
   *
   * @param $formName
   * @param $form
   */
  public static function buildForm($formName, &$form) {
    // check if KID needs to be included
    $session = CRM_Core_Session::singleton();
    $session->mafIncludeKid = 0;
    if (isset($session->mafExportWithKidTask) && isset($form->_task)) {
      if ($session->mafExportWithKidTask == $form->_task) {
        // set session variable so we know at export time that KID needs to be included
        $session->mafIncludeKid = 1;
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
        $form->add('select', 'campaign_id', ts('Campaign (for KID)'), $campaignSelectList, true, array('class' => 'crm-select2'));
          CRM_Core_Region::instance('page-body')->add(array(
          'template' => 'CRM/Generic/Export/addCampaignSelect.tpl'));
      }
    }
  }

  /**
   * Method to process civicrm hook validateForm
   * @param $formName
   * @param $fields
   * @param $form
   * @param $errors
   */
  public static function validateForm($formName, $fields, $form, &$errors) {
    // only if KID needs to be included
    $session = CRM_Core_Session::singleton();
    if (isset($session->mafIncludeKid) && $session->mafIncludeKid == 1) {
      if (!isset($fields['campaign_id']) || empty($fields['campaign_id'])) {
        $errors['campaign_id'] = ts('You have to select a campaign when exporting contacts with a KID');
      } else {
        // if campaign valid, store in session
        $session = CRM_Core_Session::singleton();
        $session->mafKidCampaign = $fields['campaign_id'];
      }
    }
    return;
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
    // only for contacts
    if ($exportMode == 1) {
      $session = CRM_Core_Session::singleton();
      // only if KID needs to be included
      if (isset($session->mafIncludeKid) && $session->mafIncludeKid == 1) {
        // only if there is a campaign id in the session
        if (isset($session->mafKidCampaign) && !empty($session->mafKidCampaign)) {
          $sql = "ALTER TABLE " . $exportTempTable . " ADD COLUMN kid_number VARCHAR(45)";
          CRM_Core_DAO::singleValueQuery($sql);
          $headerRows[] = "KID";
          $sqlColumns['kid_number'] = 'kid_number VARCHAR(45)';

          // update temp table
          $dao = CRM_Core_DAO::executeQuery("SELECT * FROM " . $exportTempTable);
          while ($dao->fetch()) {
            try {
              $kid = civicrm_api3('Kid', 'generate', array(
                'campaign_id' => $session->mafKidCampaign,
                'contact_id' => $dao->id,
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
      }
    }
    return;
  }
}
