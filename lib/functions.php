<?php
 
$GLOBALS['types'] = array
(
  'ch' => 'Cheating',
  'sp' => 'Statspadding',
  'gl' => 'Glitching',
  'st' => 'Strict ToS Enforcement'
);

$GLOBALS['typeIds'] = array
(
  'ch' => 1,
  'sp' => 2,
  'gl' => 3,
  'st' => 4
);

$GLOBALS['idTypes'] = array
(
  1 => 'ch',
  2 => 'sp',
  3 => 'gl',
  4 => 'st'
);

function process($msg)
{
  $msg = preg_replace("#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/|-|\[|\])))#", "<a target='_blank' href='$1'>$1</a>", $msg);
  $msg = preg_replace("#\[score=(.*)\](.*)\[\/score\]#", "<span class='score_$1'>$2</span>", $msg);
  $msg = preg_replace("#\[stats\](.*)\[\/stats\]#", "<span class='score_stats'>$1</span>", $msg);
  
  return $msg;
}

function prepare($str)
{
  $str = str_replace('[__]', '+', $str);
  $str = str_replace('[___]', '#', $str);
  
  return $str;
}

function addLog($type, $action, $value='')
{
  $userId = getUserId();
  $now = time();
  $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
  $httpReq = mysql_real_escape_string(serialize($_SERVER));
  
  alxDatabaseManager::query("INSERT INTO `log` SET userId = '{$userId}', type = '{$type}', date = '{$now}', action = '{$action}', value = '{$value}', ip = '{$ip}', httpRequest = '{$httpReq}'");
}

function isLogged()
{
  if(@getUser(getUserId())->userId > 0)
  {
    return true;
  }
  
  return false;
}

function isMod()
{
  if(@getUser(getUserId())->mod > 0)
  {
    return true;
  }
  
  return false;
}

function isAdmin()
{
  if(@getUser(getUserId())->admin > 0)
  {
    return true;
  }
  
  return false;
}

function isTech()
{
  if(@getUser(getUserId())->tech > 0)
  {
    return true;
  }
  
  return false;
}

function getUser($id)
{
  $user = alxDatabaseManager::query("SELECT userId, username, mail, `mod`, `admin`, `tech`, clanId FROM users WHERE userId = '{$id}' LIMIT 1")->fetch();

  return $user;
}

function getUserId()
{
  return (int) @$_SESSION['t4gBlacklistUserId'];
}

function getClanName($clanId)
{
  $clan = alxDatabaseManager::query("SELECT label FROM clans WHERE clanId= '{$clanId}' LIMIT 1")->fetch();
  
  return $clan ? $clan->label : null;
}

function getNiceWeaponName($name)
{ // WEAPON_SG_870COMBAT_DEFAULT
  $spl = explode('_', $name);
  
  $type = array_shift($spl);
  
  if($type == 'GADGET')
  {
    return ucFirst(strToLower(implode(' ', $spl)));
  }
  
  $category = array_shift($spl);
  $item = implode(' ', $spl);
  
  $item = str_replace('ELITE', 'Elite', $item);
  $item = str_replace(' I', ' +3', $item);
  $item = str_replace('DEFAULT', '', $item);
  $item = str_replace('DEAFULT', '', $item);
  $item = str_replace('USED', 'Veteran', $item);
  
  return $item;
}

function sendMail($to, $subject, $msg)
{

  $mail = new phpmailer();
    
  $mail->IsSMTP();           
  
  // New T4G Mailserver
  /*
  $mail->Host     = "mail.tools4games.com"; 
  $mail->SMTPAuth = true;     
  $mail->Username = "blacklist@tools4games.com";  
  $mail->Password = "dsfesda"; 

  $mail->From     = "blacklist@tools4games.com";
  $mail->FromName = "T4G Blacklist";
  */

  // GMX.com mail account setup by cpt.carrot
  $mail->Host     = "mail.t4g-blacklist.net"; 
  $mail->SMTPAuth = true;     
  $mail->Username = "admin@t4g-blacklist.net";  
  $mail->Password = "nordisgay69"; 

  $mail->From     = "t4g-team@t4g-blacklist.net";
  $mail->FromName = "T4G Blacklist";

  $mail->AddAddress($to);

  $mail->WordWrap = 50;        
  $mail->IsHTML(true);   

  $mail->Subject  =  $subject;
  $mail->Body     =  $msg;

  $res = $mail->Send();
  
  /*
  if(!$res)
  {
    echo "Mailer Error: " . $mail->ErrorInfo;
  }
  */
  return $res;
}

function sec2hms($sec, $useColon = false)
{
 
  // holds formatted string
  $hms = "";
 
  // there are 3600 seconds in an hour, so if we
  // divide total seconds by 3600 and throw away
  // the remainder, we've got the number of hours
  $years = intval(intval($sec) / 31536000);
  $days = intval(intval($sec) / 86400);
  $hours = intval(intval($sec) / 3600); 
 
  if($years > 0)
  {
    $hms.= $years . 'y ';
    
    $days-= ($years * 365);
    $hours-= (($years * 365) * 24);
  }
 
  if($days > 0)
  {
    $hms.= $days . 'd ';
    
    $hours-= ($days * 24);
  }
 
  // add to $hms, with a leading 0 if asked for
  if ($hours > 0){
    $hms .= ($useColon) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. 'h ';
  }elseif ($useColon){
    $hms .= '00:';  
  }
 
  // dividing the total seconds by 60 will give us
  // the number of minutes, but we're interested in 
  // minutes past the hour: to get that, we need to 
  // divide by 60 again and keep the remainder
  /*
  $minutes = intval(($sec / 60) % 60); 
 
  // then add to $hms (with a leading 0 if needed)
  if ($minutes > 0)
  $hms .= ($useColon) 
          ? str_pad($minutes, 2, "0", STR_PAD_LEFT). ':'
          : $minutes. 'm ';
 */
    /*
  // seconds are simple - just divide the total
  // seconds by 60 and keep the remainder
  $seconds = intval($sec % 60); 
 
  // add to $hms, again with a leading 0 if needed
  $hms .= ($useColon) 
          ? str_pad($seconds, 2, "0", STR_PAD_LEFT)
          : $seconds. 's';
 */
  return $hms;
}

// http://stackoverflow.com/a/2690541 - Modified for usage
function time2str($ts)
{
    if(!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) return 'Today';
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }
}

function getQueryStr($extra)
{
  // copy
  $params = $_GET;
  unset($params['lang'], $params['controller'], $params['action']);
  
  if(is_array($extra))
  {
    foreach($extra as $key => $val)
    {
      if($val === null)
      {
        unset($params[$key]);
      }
      else
      {
        $params[$key] = $val;
      }
    }
  }
  
  return http_build_query($params);
}

$GLOBALS['countries'] = array(
"AU" => "Australia",
"AF" => "Afghanistan",
"AL" => "Albania",
"DZ" => "Algeria",
"AS" => "American Samoa",
"AD" => "Andorra",
"AO" => "Angola",
"AI" => "Anguilla",
"AQ" => "Antarctica",
"AG" => "Antigua & Barbuda",
"AR" => "Argentina",
"AM" => "Armenia",
"AW" => "Aruba",
"AT" => "Austria",
"AZ" => "Azerbaijan",
"BS" => "Bahamas",
"BH" => "Bahrain",
"BD" => "Bangladesh",
"BB" => "Barbados",
"BY" => "Belarus",
"BE" => "Belgium",
"BZ" => "Belize",
"BJ" => "Benin",
"BM" => "Bermuda",
"BT" => "Bhutan",
"BO" => "Bolivia",
"BA" => "Bosnia/Hercegovina",
"BW" => "Botswana",
"BV" => "Bouvet Island",
"BR" => "Brazil",
"IO" => "British Indian Ocean Territory",
"BN" => "Brunei Darussalam",
"BG" => "Bulgaria",
"BF" => "Burkina Faso",
"BI" => "Burundi",
"KH" => "Cambodia",
"CM" => "Cameroon",
"CA" => "Canada",
"CV" => "Cape Verde",
"KY" => "Cayman Is",
"CF" => "Central African Republic",
"TD" => "Chad",
"CL" => "Chile",
"CN" => "China, People's Republic of",
"CX" => "Christmas Island",
"CC" => "Cocos Islands",
"CO" => "Colombia",
"KM" => "Comoros",
"CG" => "Congo",
"CD" => "Congo, Democratic Republic",
"CK" => "Cook Islands",
"CR" => "Costa Rica",
"CI" => "Cote d'Ivoire",
"HR" => "Croatia",
"CU" => "Cuba",
"CY" => "Cyprus",
"CZ" => "Czech Republic",
"DK" => "Denmark",
"DJ" => "Djibouti",
"DM" => "Dominica",
"DO" => "Dominican Republic",
"TP" => "East Timor",
"EC" => "Ecuador",
"EG" => "Egypt",
"SV" => "El Salvador",
"GQ" => "Equatorial Guinea",
"ER" => "Eritrea",
"EE" => "Estonia",
"ET" => "Ethiopia",
"FK" => "Falkland Islands",
"FO" => "Faroe Islands",
"FJ" => "Fiji",
"FI" => "Finland",
"FR" => "France",
"FX" => "France, Metropolitan",
"GF" => "French Guiana",
"PF" => "French Polynesia",
"TF" => "French South Territories",
"GA" => "Gabon",
"GM" => "Gambia",
"GE" => "Georgia",
"DE" => "Germany",
"GH" => "Ghana",
"GI" => "Gibraltar",
"GR" => "Greece",
"GL" => "Greenland",
"GD" => "Grenada",
"GP" => "Guadeloupe",
"GU" => "Guam",
"GT" => "Guatemala",
"GN" => "Guinea",
"GW" => "Guinea-Bissau",
"GY" => "Guyana",
"HT" => "Haiti",
"HM" => "Heard Island And Mcdonald Island",
"HN" => "Honduras",
"HK" => "Hong Kong",
"HU" => "Hungary",
"IS" => "Iceland",
"IN" => "India",
"ID" => "Indonesia",
"IR" => "Iran",
"IQ" => "Iraq",
"IE" => "Ireland",
"IL" => "Israel",
"IT" => "Italy",
"JM" => "Jamaica",
"JP" => "Japan",
"JT" => "Johnston Island",
"JO" => "Jordan",
"KZ" => "Kazakhstan",
"KE" => "Kenya",
"KI" => "Kiribati",
"KP" => "Korea, Democratic Peoples Republic",
"KR" => "Korea, Republic of",
"KW" => "Kuwait",
"KG" => "Kyrgyzstan",
"LA" => "Lao People's Democratic Republic",
"LV" => "Latvia",
"LB" => "Lebanon",
"LS" => "Lesotho",
"LR" => "Liberia",
"LY" => "Libyan Arab Jamahiriya",
"LI" => "Liechtenstein",
"LT" => "Lithuania",
"LU" => "Luxembourg",
"MO" => "Macau",
"MK" => "Macedonia",
"MG" => "Madagascar",
"MW" => "Malawi",
"MY" => "Malaysia",
"MV" => "Maldives",
"ML" => "Mali",
"MT" => "Malta",
"MH" => "Marshall Islands",
"MQ" => "Martinique",
"MR" => "Mauritania",
"MU" => "Mauritius",
"YT" => "Mayotte",
"MX" => "Mexico",
"FM" => "Micronesia",
"MD" => "Moldavia",
"MC" => "Monaco",
"ME" => "Montenegro",
"MN" => "Mongolia",
"MS" => "Montserrat",
"MA" => "Morocco",
"MZ" => "Mozambique",
"MM" => "Union Of Myanmar",
"NA" => "Namibia",
"NR" => "Nauru Island",
"NP" => "Nepal",
"NL" => "Netherlands",
"AN" => "Netherlands Antilles",
"NC" => "New Caledonia",
"NZ" => "New Zealand",
"NI" => "Nicaragua",
"NE" => "Niger",
"NG" => "Nigeria",
"NU" => "Niue",
"NF" => "Norfolk Island",
"MP" => "Mariana Islands, Northern",
"NO" => "Norway",
"OM" => "Oman",
"PK" => "Pakistan",
"PW" => "Palau Islands",
"PS" => "Palestine",
"PA" => "Panama",
"PG" => "Papua New Guinea",
"PY" => "Paraguay",
"PE" => "Peru",
"PH" => "Philippines",
"PN" => "Pitcairn",
"PL" => "Poland",
"PT" => "Portugal",
"PR" => "Puerto Rico",
"QA" => "Qatar",
"RE" => "Reunion Island",
"RO" => "Romania",
"RS" => "Serbia",
"RU" => "Russian Federation",
"RW" => "Rwanda",
"WS" => "Samoa",
"SH" => "St Helena",
"KN" => "St Kitts & Nevis",
"LC" => "St Lucia",
"PM" => "St Pierre & Miquelon",
"VC" => "St Vincent",
"SM" => "San Marino",
"ST" => "Sao Tome & Principe",
"SA" => "Saudi Arabia",
"SN" => "Senegal",
"SC" => "Seychelles",
"SL" => "Sierra Leone",
"SG" => "Singapore",
"SK" => "Slovakia",
"SI" => "Slovenia",
"SB" => "Solomon Islands",
"SO" => "Somalia",
"ZA" => "South Africa",
"GS" => "South Georgia and South Sandwich",
"ES" => "Spain",
"LK" => "Sri Lanka",
"XX" => "Stateless Persons",
"SD" => "Sudan",
"SR" => "Suriname",
"SJ" => "Svalbard and Jan Mayen",
"SZ" => "Swaziland",
"SE" => "Sweden",
"CH" => "Switzerland",
"SY" => "Syrian Arab Republic",
"TW" => "Taiwan, Republic of China",
"TJ" => "Tajikistan",
"TZ" => "Tanzania",
"TH" => "Thailand",
"TL" => "Timor Leste",
"TG" => "Togo",
"TK" => "Tokelau",
"TO" => "Tonga",
"TT" => "Trinidad & Tobago",
"TN" => "Tunisia",
"TR" => "Turkey",
"TM" => "Turkmenistan",
"TC" => "Turks And Caicos Islands",
"TV" => "Tuvalu",
"UG" => "Uganda",
"UA" => "Ukraine",
"AE" => "United Arab Emirates",
"GB" => "United Kingdom",
"UM" => "US Minor Outlying Islands",
"US" => "USA",
"HV" => "Upper Volta",
"UY" => "Uruguay",
"UZ" => "Uzbekistan",
"VU" => "Vanuatu",
"VA" => "Vatican City State",
"VE" => "Venezuela",
"VN" => "Vietnam",
"VG" => "Virgin Islands (British)",
"VI" => "Virgin Islands (US)",
"WF" => "Wallis And Futuna Islands",
"EH" => "Western Sahara",
"YE" => "Yemen Arab Rep.",
"YD" => "Yemen Democratic",
"YU" => "Yugoslavia",
"ZR" => "Zaire",
"ZM" => "Zambia",
"ZW" => "Zimbabwe"
);
