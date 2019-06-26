<?
function writeToLog($data, $title = ''){
    $log = "\n------///BEGIN//////------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n-----/// END ////-------------------\n";
    file_put_contents(getcwd() . '/hook.log', $log, FILE_APPEND);
    return true;
    echo "succes";
}

$defaults = array('TITLE' => '', 'NAME' => '', 'PHONE' => '', 'COMMENTS' => '', 'EMAIL' => '');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $defaults = $_REQUEST;
    writeToLog($_REQUEST, 'webform PHP');
  
    $queryUrl  = 'https://olga-uvarova.bitrix24.ru/rest/1/71mv48eihjrhzh4o/profile/crm.lead.add.json';
    $queryData = http_build_query(array(
        'fields' => array(
         "TITLE" => $_REQUEST['TITLE'].' '.$_REQUEST['formname'],
                        "NAME" => $_REQUEST['NAME'],
                        "SOURCE_ID" => $_REQUEST['formname'], 
         "COMMENTS" => $_REQUEST['comments'],
         "EMAIL" => $_REQUEST['EMAIL'],
         "ASSIGNED_BY_ID" => 1,
         "UF_CRM_SEARCH_WORD" => $_GET['utm_term'],
         "UF_CRM_LEAD_LANDING" => $_SERVER['HTTP_HOST'],
         "UF_CRM_CT_UTM_CAMP" => $_GET['utm_campaign'],
         "UF_CRM_CT_UTM_CONT" => $_GET['utm_content'],
         "UF_CRM_CT_UTM_MEDI" => $_GET['utm_medium'],
         "UF_CRM_CT_UTM_SOUR" => $_GET['utm_source'],
         "UF_CRM_CT_UTM_TERM" => $_GET['utm_term'],
         "STATUS_ID" => "NEW",
            "OPENED" => "Y", // ДОСТУПЕН ВСЕМ
            "PHONE" => array(array("VALUE" => $_REQUEST['PHONE'], "VALUE_TYPE" => "WORK" )),
            "EMAIL" => array(array("VALUE" => $_REQUEST['EMAIL_WORK'], "VALUE_TYPE" => "WORK" )),
            //"TITLE" => array("VALUE" => "Заказ с сайта"), 
        ),
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
  
  $curl = curl_init();
 curl_setopt_array($curl, array(
 CURLOPT_SSL_VERIFYPEER => 0,
 CURLOPT_POST => 1,
 CURLOPT_HEADER => 0,
 CURLOPT_RETURNTRANSFER => 1,
 CURLOPT_URL => $queryUrl,
 CURLOPT_POSTFIELDS => $queryData,
 ));

 $result = curl_exec($curl);
 curl_close($curl);

 $result = json_decode($result, 1);
 writeToLog($result, 'webform result CURL');

 if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";
}

?>