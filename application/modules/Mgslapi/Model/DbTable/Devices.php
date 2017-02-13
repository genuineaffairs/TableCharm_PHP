<?php

class Mgslapi_Model_DbTable_Devices extends Engine_Db_Table {

  //protected $_name = 'devices';
  protected $_rowClass = 'Mgslapi_Model_Device';

  const IOS_DEVICE_TYPE = 1;
  const ANDROID_DEVICE_TYPE = 2;

}
