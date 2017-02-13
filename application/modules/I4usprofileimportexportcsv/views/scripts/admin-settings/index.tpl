<?
 /**
 * Integration4us
 *
 * @category   Application_Module
 * @package    Profile Import/Export using CSV File
 * @copyright  Copyright 2009-2010 Integration4us
 * @license    http://www.integration4us.com/terms
 * @author     Jomar
 */
 
$streamTable = Engine_Api::_()->getDbtable('forgot', 'user'); // this is just a dummy settings just to get the database link
$db = $streamTable->getAdapter(); 
 
if ( $_POST['action'] == "export" )
   {
   $sql = "select `label`,(select `order` from engine4_user_fields_maps where option_id=1 and child_id=engine4_user_fields_meta.field_id) as fieldorder
 from engine4_user_fields_meta where `type` <> 'profile_type' and `type` <> 'heading'
and field_id IN (select child_id from engine4_user_fields_maps where option_id='".$_POST['profiletype'] ."')  order by fieldorder";
$stmt = $db->query($sql);
$arr_labels = $stmt->fetchAll();
$csv_output .="Email,Profile Address";
foreach( $arr_labels as $key => $arr )
   {
   $csv_output .=",".str_replace(","," ",$arr['label']);  
   }
   $csv_output .= "\n";   
   $stmt = $db->query("select * from `engine4_users`");
   $arr_users = $stmt->fetchAll();
   foreach( $arr_users as $key => $arr )
   {       
      $bexitloop=false;
       $csv_output2 = str_replace(","," ",$arr['email']).",".str_replace(","," ",$arr['username']);
       $user_id = $arr['user_id'];   
       $sql = "select *,(select `value` from engine4_user_fields_values where field_id=engine4_user_fields_maps.child_id and item_id='$user_id' limit 1) as `value`,
       (select `label` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) as `label`,
       (select `required` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) as `required`
       from engine4_user_fields_maps where  (select `type` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) <> 'heading'
       and (select `value` from engine4_user_fields_values where field_id=1 and item_id='$user_id' limit 1)=option_id  order by `order`";
       $stmt = $db->query($sql);
       $arr_fields = $stmt->fetchAll();
       $bwithdata=false;
       foreach( $arr_fields as $key => $arr1 )
       {
          $bwithdata=true;
          if ( $arr1['option_id'] <> $_POST['profiletype'] )
             {
             $bexitloop=true;
             break;
             }
          $csv_output2 .= ",".str_replace(","," ",$arr1['value']);
       }
       
       if ( $bexitloop or !$bwithdata )
            continue;
       
       $csv_output .= $csv_output2."\n";     
   }

   $filename = "profile_".date("d-m-Y_H-i",time());
 
   header("Content-type: application/vnd.ms-excel");
   header("Content-disposition: csv" . date("Y-m-d") . ".csv");
   header( "Content-disposition: filename=".$filename.".csv");
 
   print $csv_output;
 
   exit;
   
   } 

echo '<h3>'.$this->translate("Profile Import/Export using CSV File").'</h3><br>';


//select profile type
echo '<form action="'.$_SERVER['REQUEST_URI'].'" name="form1" method="post" enctype="multipart/form-data">';
echo '<script type="text/javascript">
function profiletypechange()
{
document.forms[\'form1\'].submit(); 
} 
</script>';

echo '<table>';
echo '<tr><td>(Import/Export) Select a profile type : </td><td><select name="profiletype" onChange="profiletypechange()">';
$stmt = $db->query("select * from engine4_user_fields_options where field_id=1");
$arr_profiletype = $stmt->fetchAll();
$bsel=false;
foreach( $arr_profiletype as $key => $arr )
{
 
 if ( !$_POST['profiletype']  and !$bsel and $arr['option_id']  == 1)
     {
     $bsel=true;
     $profile_type=1;
     echo '<option value="1" selected>'.$arr['label'].'</option>';
     }
  elseif ( $_POST['profiletype'] == $arr['option_id'] and !$bsel )
     {
     $bsel=true;
     $profile_type=$arr['option_id'];
     echo '<option value="'.$arr['option_id'].'" selected>'.$arr['label'].'</option>';
     }
  else
     {
     echo '<option value="'.$arr['option_id'].'">'.$arr['label'].'</option>';
     }
}
echo '</select></td></tr>';

// Member Level

echo '<tr><td>(Import) Select a member level : </td><td><select name="memberlevel">';
$stmt = $db->query("select * from engine4_authorization_levels where `type` <> 'public'");
$arr_levels = $stmt->fetchAll();
$bsel=false;
foreach( $arr_levels as $key => $arr )
{
 
  if ( !$_POST['pmemberlevel']  and !$bsel and $arr['level_id'] == 4 )
     {
     $bsel=true;
     $level_id=4;
     echo '<option value="4" selected>'.$arr['title'].'</option>';
     }
  elseif ( $_POST['memberlevel'] == $arr['level_id']  and !$bsel )
     {
     $bsel=true;
     $level_id=$arr['level_id'];
     echo '<option value="'.$arr['level_id'].'" selected>'.$arr['title'].'</option>';
     }
  else
     {
     echo '<option value="'.$arr['level_id'].'">'.$arr['title'].'</option>';
     }
}
echo '</select></td></tr>';



// Email Profile
$schk_emailprofile="";
if ( $_POST['emailprofile'] )
   {
   $schk_emailprofile="checked";
   }
echo '<tr><td colspan=2><input type="checkbox" name="emailprofile" value="1" '.$schk_emailprofile.'/> (Import) Email created profile</td></tr>';
//echo '<tr><td colspan=2><br><button name="goemailprofile" value="1" id="submit">Set</button></td></tr>'
echo '</table><br><br>';



// Regular member field labels
echo "Regular member field labels, the CSV file must contained the following column values, make sure the fields matches the csv column values.<br>";
$sql = "select `label`,(select `order` from engine4_user_fields_maps where option_id=1 and child_id=engine4_user_fields_meta.field_id) as fieldorder
 from engine4_user_fields_meta where `type` <> 'profile_type' and `type` <> 'heading'
and field_id IN (select child_id from engine4_user_fields_maps where option_id='".$profile_type."')  order by fieldorder";
$stmt = $db->query($sql);
$arr_labels = $stmt->fetchAll();
$cnt=0;
echo "<br>".$cnt.". <b>Email</b>";
$cnt++;
echo "<br>".$cnt.". <b>Profile Address</b>";
foreach( $arr_labels as $key => $arr )
   {
   $cnt++;
   echo "<br>".$cnt.". ".$arr['label'];
   if ( $arr['label'] == "Birthday" )
      {
      echo " ( YYYY-MM-DD )";
      }
   if ( $arr['label'] == "Gender" )
      {
      echo " ( 2=male OR 3=female )";
      }
   if ( strtolower($arr['label']) == "first name" )
      {
      $ifirstname=$cnt;
      }      
   if ( strtolower($arr['label']) == "last name" )
      {
      $ilastname=$cnt;
      }
   }
   
   
//echo '<br><form method="post">';
//echo '<input type="hidden" name="action" value="export">';
//echo '<input type="hidden" name="profiletype" value="'.$profile_type.'">';
//echo '<input type="hidden" name="memberlevel" value="'.$level_id.'">';
//echo '<input type="hidden" name="emailprofile" value="'.$_POST['emailprofile'].'">';
echo '<br><button name="action" value="export">Export To File</button><br>';


            
if ( $_POST['action'] == "import" )
   {    
   if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {   
      echo "<br><b>Imported Profiles</b><br>";
      $filename = APPLICATION_PATH."/temporary/".$_FILES["file"]["name"];
      move_uploaded_file($_FILES["file"]["tmp_name"],$filename);      
      // get contents of a file into a string
      $handle = fopen($filename, "r");
      while (($adata = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $stmt = $db->query("select `user_id`,`creation_date` from engine4_users where lcase(`email`)='".strtolower($adata[0])."'");
      $arr_user = $stmt->fetch();
      if ( $arr_user['user_id'] ) // update existing user from csv
         {                  
         $sql ="update `engine4_users` set `username`='".$adata[1]."',`displayname`='".$adata[$ifirstname]." ".$adata[$ilastname]."' where user_id='".$arr_user['user_id']."'";
         $db->query($sql);
         $db->query("delete from engine4_user_fields_values where item_id='".$arr_user['user_id']."'");
         echo "<br>Profile Updated $adata[0]";  
         }
      else
         {                              
      $timezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone');
      //$ip2long  = ip2long($_SERVER['REMOTE_ADDR']);
      $dbip =  Engine_Db_Table::getDefaultAdapter();
      $ipObj = new Engine_IP();
      $ipExpr = new Zend_Db_Expr($dbip->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
      $ip2long = $ipExpr;
      // password encryption
      $qpassword = rand(1000000, 9999999);
      $salt = (string) rand(1000000, 9999999);
      $password_cryped = md5( Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt'). $qpassword. $salt );
      $sql ="insert `engine4_users` set `email`='".$adata[0]."',`username`='".$adata[1]."',
`displayname`='".$adata[$ifirstname]." ".$adata[$ilastname]."',`photo_id`='0',`status`='',`status_date`=NULL,`password`='$password_cryped',
`salt`='$salt',`locale`='auto',`language`='en_US',`timezone`='$timezone',`search`='1',`show_profileviewers`='1',
`level_id`='".$level_id."',`invites_used`='0',`extra_invites`='0',`enabled`='1',`verified`='1',`creation_date`='".date("Y-m-d H:i:s")."',
`creation_ip`=".$ip2long.",`modified_date`='0000-00-00 00:00:00',`lastlogin_date`='".date("Y-m-d H:i:s")."',`lastlogin_ip`=".$ip2long.",
`update_date`='".date("Y-m-d H:i:s")."',`member_count`='0',`view_count`='0',approved='1'";
//echo $sql;
//exit;
      $db->query($sql);   
      echo "<br>Profile New $adata[0]";      
         } 
      $stmt = $db->query("select `user_id`,`creation_date` from engine4_users where lcase(`email`)='".strtolower($adata[0])."'");
      $arr_user = $stmt->fetch();
      $user_id = $arr_user['user_id'];   
      // Profile Type = 1 = Regular Member
      $db->query("insert engine4_user_fields_values set `index`=0,item_id='$user_id',field_id='1',`value`='".$profile_type."'");
      $sql = "select *,(select `value` from engine4_user_fields_values where field_id=engine4_user_fields_maps.child_id and item_id='$user_id' limit 1) as `value`,
       (select `label` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) as `label`,
       (select `required` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) as `required`
       from engine4_user_fields_maps where  (select `type` from engine4_user_fields_meta where field_id=engine4_user_fields_maps.child_id  limit 1) <> 'heading'
       and (select `value` from engine4_user_fields_values where field_id=1 and item_id='$user_id' limit 1)=option_id  order by `order`";
       $stmt = $db->query($sql);
       $arr_fields = $stmt->fetchAll();
       $cnt=2;
       foreach( $arr_fields as $key => $arr )
        {         
        $db->query("insert engine4_user_fields_values set `index`=0,item_id='$user_id',field_id='".$arr['child_id']."',`value`='".$adata[$cnt]."'");
        $cnt++;               
        }            
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='comment',`role`='member',`role_id`='0',`value`='1'");
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='comment',`role`='network',`role_id`='0',`value`='1'");
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='view',`role`='everyone',`role_id`='0',`value`='1'");
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='view',`role`='member',`role_id`='0',`value`='1'");
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='view',`role`='network',`role_id`='0',`value`='1'");
       $db->query("insert engine4_authorization_allow set `resource_type`='user',`resource_id`='$user_id',`action`='view',`role`='registered',`role_id`='0',`value`='1'");
       
       
               // Email User
        $user=$this->user($user_id);  
        $mailType = null;
    $mailParams = array(
      'host' => $_SERVER['HTTP_HOST'],
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
    );
        $mailParams['password'] = $qpassword;
        $random =1;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        
        // send welcome email
        $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
        
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $user,
        $mailType,
        $mailParams
      );
       
      }             
      fclose($handle);      
      unlink(APPLICATION_PATH."/temporary".$_FILES["file"]["name"]);
    }
   }  
//echo '<br><br><br><form method="post" enctype="multipart/form-data">';
//echo '<input type="hidden" name="action" value="import">';
//echo '<input type="hidden" name="profiletype" value="'.$profile_type.'">';
//echo '<input type="hidden" name="memberlevel" value="'.$level_id.'">';
//echo '<input type="hidden" name="emailprofile" value="'.$_POST['emailprofile'].'">';
echo '<br>File to import must be comma delimited  : <input type="file" name="file" id="file">';
echo '<br><button name="action" value="import" >Import From File</button></form><br><br>';


?>