<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:ConfigItems.Load',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Load MAF Norge ConfigItems',
      'description' => 'Load (create, update and remove if necessary) MAF Norge Configuration Items',
      'run_frequency' => 'Daily',
      'is_active' => 0,
      'api_entity' => 'ConfigItems',
      'api_action' => 'Load',
      'parameters' => '',
    ),
  ),
);
