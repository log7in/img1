<?php 
require_once 'api2.php';

// устанавливаем API-HASH рекламодателя
$api = new Api("14d9f4f2e8c741a3ca18b8685591049e");

// устанавливаем URL лендинга
// может быть:
// http://landing.ru
// http://landing.domen.ru
// http://domen.ru/landing




$hash = $_GET["hash"];

if(!$hash) {
    $hash= $_COOKIE["hash"];
}
 

// определяем ХЭШ ПОТОКА вебмастера 
$api->setFlowHash();
	
//utm и vcode метки
if(isset($_GET['vcode']))
	$vcode = "&vcode=".$_GET['vcode'];  //vcode=[SUBID] для постбека
if(isset($_GET['utm_source']))
	$source = '&utm_source='.$_GET['utm_source'];	//utm_source
if(isset($_GET['utm_term']))
	$term = "&utm_term=".$_GET['utm_term'];  	//utm_term
if(isset($_GET['utm_content']))
	$content = "&utm_content=".$_GET['utm_content']; //utm_content
if(isset($_GET['utm_campaign']))
	$campaign = '&utm_campaign='.$_GET['utm_campaign'];  //не используется | utm_campaign
if(isset($_GET['utm_medium']))
	$medium = '&utm_medium='.$_GET['utm_medium'];    	//сплит тест | utm_medium

$utm_metki = $source.$term.$content.$medium.$campaign.$vcode;
$utm_metki = substr($utm_metki, 1);
	
if (!isset($hash)) {

// записываем посещение и получаем ХЭШ ПОСЕЩЕНИЯ
    $result = $api->insertTransit();
     
    $hash = $result->transit_hash; 
	
// перенаправляем посетителя, если есть условие траффикбека
    if ($result->trafficback_url != '')
        header("Location: " . $result->trafficback_url . '?' . $utm_metki);
}

if (isset($hash) AND strlen($hash) == 32) {
    $api->set_hash($hash);

   $api->getInfo();
	//var_dump($api->getFloats());
	$ya_metrika = $api->getYaMetrikaId();
    $metrika_id = $api->getMetrikaId();
    $mail_id = $api->getMailId();
    $google_id = $api->getGoogleId();

    // устанавливаем ПОСТКЛИК (дней)
    $api->setHashToCookie(30);
}
 
// в коде верстки устанавливаем абсолютные пути к файлам и картинкам, используя $base_url
if (isset($_GET['fio'])) {
    $fio = $_GET['fio'];
}
if (isset($_GET['phone'])) {
    $phone = $_GET['phone'];
}

if($_POST['phone']&&$_POST['fio']&&!$_POST['com']){
	$_POST["Order"]['phone'] = $_POST['phone'];
	$_POST["Order"]['fio'] = $_POST['fio'];
	$api->set_dop_info($_POST["Order"]);
	
	$api->insertRequest();
}elseif($_POST['com']){
	
	$api->set_comment($_POST['com']);
	$api->insertComment();
}
?> 