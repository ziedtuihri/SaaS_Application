<?php
/*
 * @ PHP 5.6
 * @ Author     : zied.th0@gmail.com
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://mind.engineering
 */

function tunisyndic_MetaData()
{
    return array("DisplayName" => "Tunisyndic", "APIVersion" => "1.0", "DefaultNonSSLPort" => "10000", "DefaultSSLPort" => "10000");
}
function tunisyndic_ConfigOptions()
{
    $configarray = array("Template Name" => array("Type" => "text", "Size" => "30"), "Plan Name" => array("Type" => "text", "Size" => "30"), "Dedicated IP" => array("Type" => "yesno", "Description" => "Tick to auto assign next available dedicated IP"));
    return $configarray;
}
function tunisyndic_ClientArea(array $params)
{
    $domain = $params["serverhostname"] ? $params["serverhostname"] : $params["serverip"];
    $domain = explode(":", $domain);
    $port = "";
    if (count($domain) == 2) {
        $port = $domain[1];
    }
    $domain = $domain[0];
    if (!$port) {
        $port = $params["serverport"];
    }
    $domain = $domain . ":" . $port;
    $form = sprintf("<form action=\"#\"><h3>change your instance name </h3><h3 id=\"simpleName\">".$params['customfields']['Instance']."</h3><h3> To : </h3>
        <center><div class=\"alert alert-danger\" role=\"alert\" id=\"Authentification\" style=\"display:none; margin-top: 30px;margin-bottom: 20px;width: 200px;\">
                <span>Authentification échouée nom de l'instance exist</span>
            </div></center>
            <center><div class=\"alert alert-success fade in alert-dismissible\" id=\"Authentification2\" style=\"display:none;margin-top:18px;margin-bottom: 20px;width: 200px;\"><strong><span class=\"title\">Module Command Success</span></strong><br>Service Update Successfully </div></center>
        <input type=\"text\" name=\"Name_Instance\" id=\"instance\"><br><br><button onclick=\"Send_Instance()\" type=\"button\" value=\"Login\" class=\"btn btn-primary block full-width m-b\">
                Update
            </button>
            <input type=\"hidden\" id=\"old\" value=\"".$params['customfields']['Instance']."\" />
            </form><script>function Send_Instance(){ 
                console.log('click');
             $(document).ready(function () {
                key = 'kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G';
                instance = $(\"#instance\").val();
                oldname = $(\"#old\").val();
                
                console.log('value of instance '+instance+' and '+oldname);
                 $.ajax({
                url: 'http://tunisyndic.tn/Generate_multiTanentMysql.php',
                type: \"POST\",
                data: ({key: key, new_Instance: instance, old_Instance: oldname}),
                success: function (data) {
                    obj = JSON.parse(data);
                    console.log(obj[1]+\"bla bla \");
                    if(obj[0]){
                                    $('#Authentification2').show();
                                    $('#Authentification').hide();
                                    $('#simpleName').text(instance); 
                                    $.ajax({
                                    url: 'http://whmcs.dev.local/modules/servers/tunisyndic/tunisyndic.php',
                                    type: \"POST\",
                                    data: ({newInstance: instance, oldInstance: oldname})
                                    });
                                    
                        } else {
                                    $('#Authentification2').hide();
                                    $('#Authentification').show();
                        }
                }
                });
                });

             }</script>");


    return $form;
}
function tunisyndic_AdminLink(array $params)
{
    $domain = $params["serverhostname"] ? $params["serverhostname"] : $params["serverip"];
    $domain = explode(":", $domain);
    $port = "";
    if (count($domain) == 2) {
        $port = $domain[1];
    }
    $domain = $domain[0];
    if (!$port) {
        $port = $params["serverport"];
    }
    $domain = $domain . ":" . $port;
    $form = sprintf("<form action=\"%s://%s/session_login.cgi\" method=\"post\" target=\"_blank\">" . "<input type=\"hidden\" name=\"user\" value=\"%s\" />" . "<input type=\"hidden\" name=\"pass\" value=\"%s\" />" . "<input type=\"hidden\" name=\"notestingcookie\" value=\"1\" />" . "<input type=\"submit\" value=\"%s\" class=\"button\" />" . "</form>", $params["serverhttpprefix"], WHMCS\Input\Sanitize::encode($domain), WHMCS\Input\Sanitize::encode($params["serverusername"]), WHMCS\Input\Sanitize::encode($params["serverpassword"]), "Login to Control Panel");
    return $form;
}
function tunisyndic_CreateAccount($params)
{
    $updateData = array();
    $key = "kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G";
    $postfields = array();
    $postfields["key"] = $key;
    $postfields["Name_Instance"] = "Instance";
    $postfields["Email_Instance"] = $params["clientsdetails"]["email"];
    $postfields["first_name"] = $params["clientsdetails"]["firstname"];
    $postfields["last_name"] = $params["clientsdetails"]["lastname"];
    $postfields["password"] = $params['customfields']['Password'];
    $postfields["phone"] = $params["clientsdetails"]["phonenumber"];
    $postfields["adress"] = $params["clientsdetails"]["address1"].$params["clientsdetails"]["address2"].$params["clientsdetails"]["city"];
    $postfields["gender"] = "male";

    $result = tunisyndic_req($params, $postfields);
/*
    if ($params["type"] == "reselleraccount") {
        if (!$params["username"]) {
            $username = preg_replace("/[^a-z0-9]/", "", strtolower($params["clientsdetails"]["firstname"] . $params["clientsdetails"]["lastname"] . $params["serviceid"]));
            $updateData["username"] = $username;
            $params["username"] = $username;
        }
        $postfields = array();
        $postfields["key"] = $key;
        $postfields["name"] = $params["username"];
        $postfields["pass"] = $params["password"];
        $postfields["email"] = $params["clientsdetails"]["email"];
        if ($params["configoption2"]) {
            $postfields["plan"] = $params["configoption2"];
        }
        $result = tunisyndic_req($params, $postfields);
    } 
        
    else {
        $postfields = array();
        $postfields["domain"] = $params["domain"];
        $postfields["user"] = $params["username"];
        $postfields["pass"] = $params["password"];
        $postfields["email"] = $params["clientsdetails"]["email"];
        if ($params["configoption1"]) {
            $postfields["template"] = $params["configoption1"];
        }
        if ($params["configoption2"]) {
            $postfields["plan"] = $params["configoption2"];
        }
        if ($params["configoption3"]) {
            $postfields["allocate-ip"] = "";
        }
        $postfields["features-from-plan"] = "";
        $result = tunisyndic_req($params, $postfields);
    }
    */

    if ($updateData) {
        $params["model"]->serviceProperties->save($updateData);
    }
    return $result;
}
function tunisyndic_SuspendAccount($params)
{
    $key = "kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G";
    $postfields = array();
    $postfields["key"] = $key;
    $postfields["Suspend_Instance"] = $params['customfields']['Instance'];
    
    $result = tunisyndic_req($params, $postfields);
    return $result;
}
function tunisyndic_UnsuspendAccount($params)
{
  $key = "kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G";
    $postfields = array();
    $postfields["key"] = $key;
    $postfields["delet_Suspend_Instance"] = $params['customfields']['Instance'];
    
    $result = tunisyndic_req($params, $postfields);
    return $result;
}
function tunisyndic_TerminateAccount($params)
{
/*
|--------------------------------------------------------------------------
| Button TerminateAccount 
|--------------------------------------------------------------------------
|
| you should go to Clients->Vie/Search Clients -> Products/Services.
|
| i d'ont work with this function ...
|
*/
    
  $key = "kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G";
    $postfields = array();
    $postfields["key"] = $key;
    $postfields["delete_instance"] = $params['customfields']['Instance'];
    
    $result = tunisyndic_req($params, $postfields);
    return $result;
    
}
function tunisyndic_ChangePassword($params)
{
/*
|--------------------------------------------------------------------------
| Button ChangePassword
|--------------------------------------------------------------------------
|
| you should go to Clients->Vie/Search Clients -> Products/Services.
|
| i d'ont work with this function ...
|
*/  /*
    $postfields = array();
    $postfields["program"] = "modify-domain";
    $postfields["domain"] = $params["domain"];
    $postfields["pass"] = $params["password"];
    $result = tunisyndic_req($params, $postfields);
    return $result;
    */
}
function tunisyndic_ChangePackage($params)
{
/*
|--------------------------------------------------------------------------
| Button ChangePackage
|--------------------------------------------------------------------------
|
| you should go to Clients->Vie/Search Clients -> Products/Services.
|
| i d'ont work with this function ...
|
*/  /*
    $postfields = array();
    $postfields["program"] = "modify-domain";
    $postfields["domain"] = $params["domain"];
    $postfields["plan-features"] = "";
    if ($params["configoption1"]) {
        $postfields["template"] = $params["configoption1"];
    }
    if ($params["configoption2"]) {
        $postfields["apply-plan"] = $params["configoption2"];
    }
    $result = tunisyndic_req($params, $postfields);
    return $result;
    */
}
function tunisyndic_UsageUpdate($params)
{
/*
|--------------------------------------------------------------------------
| go to documentation WHMCS for this function UsageUpdate
|--------------------------------------------------------------------------
|
| i d'ont work with this function ...
|
*/  
/*
    $postfields = array();
    $postfields["program"] = "list-domains";
    $postfields["json"] = 1;
    $postfields["multiline"] = "";
    $result = tunisyndic_req($params, $postfields, true);
    $result = json_decode($result, true);
    $dataArray = $result["data"];
    $services = WHMCS\Service\Service::where("server", "=", $params["serverid"])->get();
    $addons = WHMCS\Service\Addon::whereHas("customFieldValues.customField", function ($query) {
        $query->where("fieldname", "Domain");
    })->with("customFieldValues", "customFieldValues.customField")->where("server", "=", $params["serverid"])->get();
    foreach ($dataArray as $values) {
        $domain = $values["name"];
        $domainData = $values["values"];
        if (!$domain) {
            continue;
        }
        if (!array_key_exists("server_byte_quota_used", $domainData)) {
            $domainData["server_byte_quota_used"] = 0;
        }
        if (!array_key_exists("server_block_quota", $domainData)) {
            $domainData["server_block_quota"] = 0;
        }
        if (!array_key_exists("bandwidth_byte_limit", $domainData)) {
            $domainData["bandwidth_byte_limit"] = 0;
        }
        if (!array_key_exists("bandwidth_byte_usage", $domainData)) {
            $domainData["bandwidth_byte_usage"] = 0;
        }
        $diskusage = $domainData["server_byte_quota_used"] / 1048576;
        $disklimit = $domainData["server_block_quota"] / 1024;
        $bwlimit = $domainData["bandwidth_byte_limit"] / 1048576;
        $bwused = $domainData["bandwidth_byte_usage"] / 1048576;
        $model = $services->where("domain", $domain)->first();
        if (!$model) {
            foreach ($addons as $searchAddon) {
                foreach ($searchAddon->customFieldValues as $customFieldValue) {
                    if (!$customFieldValue->customField) {
                        continue;
                    }
                    if ($customFieldValue->value == $domain) {
                        $model = $searchAddon;
                        break 2;
                    }
                }
            }
        }
        if (!$model) {
            continue;
        }
        $model->serviceProperties->save(array("diskusage" => $diskusage, "disklimit" => $disklimit, "bwusage" => $bwused, "bwlimit" => $bwlimit, "lastupdate" => WHMCS\Carbon::now()->toDateTimeString()));
    }
    */
}
function tunisyndic_req($params, $postfields, $rawdata = false)
{

    $url = "http://".$params["serverip"]."/Generate_multiTanentMysql.php";

    /*
    $fieldstring = "";
    foreach ($postfields as $k => $v) {
        $fieldstring .= (string) $k . "=" . urlencode($v) . "&";
    }
        */


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERPWD, "root" . ":" . "password");
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $data = curl_exec($ch);
    


/*
   return "testing this value -> ".$params['customfields']['Instance']." ** ".$params["clientsdetails"]["email"]."**".$params["clientsdetails"]["firstname"]." - ".$params["clientsdetails"]["lastname"]." ** ".$params["clientsdetails"]["address1"].$params["clientsdetails"]["address2"].$params["clientsdetails"]["city"]." ** ".$params["clientsdetails"]["phonenumber"]."/// ".$params["type"];
   */

    if (curl_errno($ch)) {
        $data = "Curl Error: " . curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    logModuleCall("tunisyndic", $postfields["program"], $postfields, $data);
    if (strpos($data, "Unauthorized") == true) {
        return "Server Login Invalid";
    }
    if ($rawdata) {
        return $data;
    }
    $exitstatuspos = strpos($data, "Exit status:");
    $exitstatus = trim(substr($data, $exitstatuspos + 12));

    $array = json_decode($data);
    
    if ($array->success) {
        $result = "success";
        if($array->instance){
            $mysqli = new mysqli("mysql", "root", "password", "whmcs");
            if($res = $mysqli->query("SELECT MAX(id) FROM tblcustomfieldsvalues ")){
                    foreach ($res as $columnInfo) {
                        $row = $columnInfo['MAX(id)'];
                        $LastId = $columnInfo['MAX(id)']-1;
                    }
                }
                if($row == NULL){
                    $mysqli->query("UPDATE `tblcustomfieldsvalues` SET `value`='".$array->instance."' ; ");
                }
                $mysqli->query("UPDATE `tblcustomfieldsvalues` SET `value`='".$array->instance."' WHERE id = ".$LastId." ");
        }
    } else {
        $dataarray = explode("\n", $data);
        $result = $dataarray[0];
    }

    return $result;
}

$mysqli = new mysqli("mysql", "root", "password", "whmcs");
 if(isset($_POST["newInstance"]) && isset($_POST["oldInstance"])){
            $mysqli->query("UPDATE `tblcustomfieldsvalues` SET `value`='".$_POST["newInstance"]."' WHERE `value` ='".$_POST["oldInstance"]."' ; ");
    }
?>