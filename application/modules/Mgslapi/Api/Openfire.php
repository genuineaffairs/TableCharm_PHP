<?php

/**
 * Description of Openfire
 *
 * @author abakivn
 */
class Mgslapi_Api_Openfire extends Core_Api_Abstract {

  protected $_serverURL = 'http://54.206.51.197';
  protected $_secret = 'dUWB0fwh';
//  protected $_serverURL = 'http://localhost:9090/plugins/userService/userservice';
//  protected $_secret = 'pwP8qce1';
  protected $_xmppDomain;

  /**
   * URL: {$_serverURL}?type=add_or_update&secret=bigsecret&username=kafka&password=drowssap
   * @param string $username Openfire username
   * @param string $password Openfire password (currently synchronized with MGSL account password)
   */
  public function addOrUpdateAccount($username, $password) {
    $params = get_defined_vars();
    $type = 'add_or_update';

    $this->_execRequest($type, $params);
  }

  /**
   * This will reset the user's roster
   * @param string $username username of the user whose roster the item_jids are added to
   * @param string $item_jid list of item_jid, items are separated by commas
   * @param int $subscription subscription type, possible numeric values are: -1(remove), 0(none), 1(to), 2(from), 3(both).
   */
  public function addRosterMulti($username, $item_jid, $subscription = 3) {
    $params = get_defined_vars();
    $type = 'add_roster_multi';

    $this->_execRequest($type, $params);
  }
  
  /**
   * Add more item to user's roster
   */
  public function addRoster($username, $item_jid, $subscription = 3) {
    $params = get_defined_vars();
    $type = 'add_roster';

    $this->_execRequest($type, $params);
  }
  
  public function deleteRoster($username, $item_jid) {
    $params = get_defined_vars();
    $type = 'delete_roster';

    $this->_execRequest($type, $params);
  }

  public function getXMPPDomain() {
    if (!$this->_xmppDomain) {
      $type = 'get_xmpp_domain';
      $this->_xmppDomain = trim($this->_execRequest($type, array()));
    }
    return $this->_xmppDomain;
  }

  protected function _execRequest($type, $params) {
    $params['type'] = $type;
    $params['secret'] = $this->_secret;
    $ch = curl_init();
    
    if($_SERVER['HTTP_HOST'] == 'engage.myglobalsportlink.com') {
      $service_port = '9092';
    } else {
      $service_port = '9090';
    }
    $server_url = $this->_serverURL . ':' . $service_port;
    $server_url .= '/plugins/userService/userservice';

    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $server_url,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($params)
    ));

    $resp = curl_exec($ch);

    curl_close($ch);

    return $resp;
  }

}
