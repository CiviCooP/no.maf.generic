<?php
/**
 * Class following Singleton pattern o create or update configuration items from
 * JSON files in resources folder
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 7 Feb 2017
 * @license AGPL-3.0
 */
class CRM_Generic_ConfigItems_ConfigItems {

  private static $_singleton;

  protected $_resourcesPath;
  protected $_customDataDir;

  /**
   * CRM_Generic_ConfigItems_ConfigItems constructor.
   */
  function __construct() {
    // Get the directory of the extension based on the name.
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcesPath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    if (!is_dir($resourcesPath) || !file_exists($resourcesPath)) {
      throw new Exception(ts('Could not find the folder '.$resourcesPath
        .' which is required for extension no.maf.generic in '.__METHOD__
        .'.It does not exist or is not a folder, contact your system administrator'));
    }
    $this->_resourcesPath = $resourcesPath;

    $this->setOptionGroups();
    $this->setCampaigns();
    $this->setActivityTypes();
    // customData as last one because it might need one of the previous ones (option group, relationship types)
    $this->setCustomData();
  }

  /**
   * Singleton method
   *
   * @return CRM_Generic_ConfigItems_ConfigItems
   * @access public
   * @static
   */
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Generic_ConfigItems_ConfigItems();
    }
    return self::$_singleton;
  }

  /**
   * Method to create option groups
   *
   * @throws Exception when resource file not found
   * @access protected
   */
  protected function setOptionGroups() {
    $jsonFile = $this->_resourcesPath.'option_groups.json';
    if (!file_exists($jsonFile)) {
      throw new Exception(ts('Could not load option_groups configuration file for extension,
      contact your system administrator!'));
    }
    $optionGroupsJson = file_get_contents($jsonFile);
    $optionGroups = json_decode($optionGroupsJson, true);
    foreach ($optionGroups as $name => $optionGroupParams) {
      $optionGroup = new CRM_Generic_ConfigItems_OptionGroup();
      $optionGroup->create($optionGroupParams);
    }
  }

  /**
   * Method to create activity types
   *
   * @throws Exception when resource file not found
   * @access protected
   */
  protected function setActivityTypes() {
    $jsonFile = $this->_resourcesPath.'activity_types.json';
    if (!file_exists($jsonFile)) {
      throw new Exception(ts('Could not load activity_types configuration file for extension,
      contact your system administrator!'));
    }
    $activityTypesJson = file_get_contents($jsonFile);
    $activityTypes = json_decode($activityTypesJson, true);
    foreach ($activityTypes as $name => $activityTypeParams) {
      $activityType = new CRM_Generic_ConfigItems_ActivityType();
      $activityType->create($activityTypeParams);
    }
  }

  /**
   * Method to set the custom data groups and fields
   *
   * @throws Exception when config json could not be loaded
   * @access protected
   */
  protected function setCustomData() {
    // read all json files from custom_data dir
    $customDataPath = $this->_resourcesPath.'custom_data';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Generic_ConfigItems_CustomGroup();
          $created = $customGroup->create($customGroupData);
          foreach ($customGroupData['fields'] as $customFieldName => $customFieldData) {
            $customFieldData['custom_group_id'] = $created['id'];
            $customField = new CRM_Generic_ConfigItems_CustomField();
            $customField->create($customFieldData);
          }
          // remove custom fields that are still on install but no longer in config
          CRM_Generic_ConfigItems_CustomField::removeUnwantedCustomFields($created['id'], $customGroupData);
        }
      }
    }
  }

  /**
   * Method to disable configuration items
   */
  public static function disable() {
    self::disableCustomData();
    self::disableOptionGroups();
    self::disableCampaigns();
    self::disableActivityTypes();

  }

  /**
   * Method to enable configuration items
   */
  public static function enable() {
    self::enableCustomData();
    self::enableOptionGroups();
    self::enableCampaigns();
    self::enableActivityTypes();

  }

  /**
   * Method to uninstall configuration items
   */
  public static function uninstall() {
    self::uninstallCustomData();
    self::uninstallOptionGroups();
    self::uninstallCampaigns();
    self::uninstallActivityTypes();
  }

  /**
   * Method to uninstall custom data
   */
  private static function uninstallCustomData() {
    // read all json files from custom_data dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/custom_data';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Generic_ConfigItems_CustomGroup();
          $customGroup->uninstall($customGroupName);
        }
      }
    }
  }

  /**
   * Method to enable custom data
   */
  private static function enableCustomData() {
    // read all json files from custom_data dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/custom_data';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Generic_ConfigItems_CustomGroup();
          $customGroup->enable($customGroupName);
        }
      }
    }
  }

  /**
   * Method to disable custom data
   */
  private static function disableCustomData() {
    // read all json files from custom_data dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/custom_data';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Generic_ConfigItems_CustomGroup();
          $customGroup->disable($customGroupName);
        }
      }
    }
  }

  /**
   * Method to disable campaigns
   */
  private static function disableCampaigns() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'campaigns.json';
    if (file_exists($jsonFile)) {
      $campaignsJson = file_get_contents($jsonFile);
      $campaigns = json_decode($campaignsJson, true);
      foreach ($campaigns as $name => $campaignParams) {
        $campaign = new CRM_Generic_ConfigItems_Campaign();
        $campaign->disable($name, $campaignParams['campaign_type_id']);
      }
    }
  }

  /**
   * Method to create campaigns
   */
  private static function setCampaigns() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'campaigns.json';
    if (file_exists($jsonFile)) {
      $campaignsJson = file_get_contents($jsonFile);
      $campaigns = json_decode($campaignsJson, true);
      foreach ($campaigns as $name => $campaignParams) {
        $campaign = new CRM_Generic_ConfigItems_Campaign();
        $campaign->create($campaignParams);
      }
    }
  }

  /**
   * Method to enable activity types
   */
  private static function enableActivityTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'activity_types.json';
    if (file_exists($jsonFile)) {
      $activityTypesJson = file_get_contents($jsonFile);
      $activityTypes = json_decode($activityTypesJson, true);
      foreach ($activityTypes as $name => $activityParams) {
        $activityType = new CRM_Generic_ConfigItems_ActivityType();
        $activityType->enable($name);
      }
    }
  }

  /**
   * Method to disable activity types
   */
  private static function disableActivityTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'activity_types.json';
    if (file_exists($jsonFile)) {
      $activityTypesJson = file_get_contents($jsonFile);
      $activityTypes = json_decode($activityTypesJson, true);
      foreach ($activityTypes as $name => $activityParams) {
        $activityType = new CRM_Generic_ConfigItems_ActivityType();
        $activityType->disable($name);
      }
    }
  }

  /**
   * Method to enable campaigns
   */
  private static function enableCampaigns() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'campaigns.json';
    if (file_exists($jsonFile)) {
      $campaignsJson = file_get_contents($jsonFile);
      $campaigns = json_decode($campaignsJson, true);
      foreach ($campaigns as $name => $campaignParams) {
        $campaign = new CRM_Generic_ConfigItems_Campaign();
        $campaign->enable($name, $campaignParams['campaign_type_id']);
      }
    }
  }

  /**
   * Method to uninstall activity types
   */
  private static function uninstallActivityTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'activity_types.json';
    if (file_exists($jsonFile)) {
      $activityTypesJson = file_get_contents($jsonFile);
      $activityTypes = json_decode($activityTypesJson, true);
      foreach ($activityTypes as $name => $activityTypeParams) {
        $activityType = new CRM_Generic_ConfigItems_ActivityType();
        $activityType->uninstall($name);
      }
    }
  }

  /**
   * Method to uninstall campaigns
   */
  private static function uninstallCampaigns() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'campaigns.json';
    if (file_exists($jsonFile)) {
      $campaignsJson = file_get_contents($jsonFile);
      $campaigns = json_decode($campaignsJson, true);
      foreach ($campaigns as $name => $campaignParams) {
        $campaign = new CRM_Generic_ConfigItems_Campaign();
        $campaign->uninstall($name, $campaignParams['campaign_type_id']);
      }
    }
  }

  /**
   * Method to disable option groups
   */
  private static function disableOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Generic_ConfigItems_OptionGroup();
        $optionGroup->disable($name);
      }
    }
  }

  /**
   * Method to enable option groups
   */
  private static function enableOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Generic_ConfigItems_OptionGroup();
        $optionGroup->enable($name);
      }
    }
  }

  /**
   * Method to uninstall option groups
   */
  private static function uninstallOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('no.maf.generic').'/CRM/Generic/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Generic_ConfigItems_OptionGroup();
        $optionGroup->uninstall($name);
      }
    }
  }
}