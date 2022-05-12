<?php
// --------------------------------------------------
// ATT modem online check script  (C) 2022 v1.2
// --------------------------------------------------
// log the att modem stats
//AT&T modem logging script.   
//a PHP script to log your modem
//Manufacturer	Pace Plc
//Model	5268AC
//Outdoor Antenna Information
//Type	Version
//Manufacturer	NetComm Wireless Limited
//Model	IFWA661 Series
//This is a script to monitor the att modem and log reboots and down events.
//run in chron on a pi
// modem 
//$ip  ="192.168.2.240";
$ip  ="192.168.0.1";
$url ="/xslt?PAGE=C_0_0";//mode
$url2="/xslt?PAGE=A_0_0";//home
$url3="/xslt?PAGE=C_1_0";//status
$path = dirname(__FILE__) ;
$file = "modem.log";



$agent="mmattv1";$phpVersion= phpversion();

$datum = date('[H:i:s]');

$time = date('[g:i a]');
#print "$datum Checking $ip Time Online :->";
$error = ""; $getheader = false; $htmlON=false;
$html = http_request('GET', $ip, 80 , $url);
$online="NA";$status="-";$signal="-";
$pos1 = strpos($html ,"Time Since Last Boot"); 

if($pos1){
$test = substr($html, ($pos1),90);//print $test;
 $Lpos = strpos($test, '<td>');$Rpos = strpos($test, '</td>');$online = substr($test, $Lpos+4,$Rpos-$Lpos-1);

$pos1 = strpos($html ,"Temperature"); 
$test = substr($html, ($pos1),78);//print $test;
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22);// print "[$newtest]";
$Rpos = strpos($newtest, '</td>');
$Temperature = substr($newtest, 4,$Rpos-4);
//print $Temperature;


}

$html = http_request('GET', $ip, 80 , $url2);
//small_warning.gif icon-broadband.png
$pos1 = strpos($html ,"warning"); if ($pos1){$status="warning";}

$pos1 = strpos($html ,">Up<");    if ($pos1){$status="UP";}
$pos1 = strpos($html ,">Down<");  if ($pos1){$status="Down";}

// signal-0.png 1-5 signal"images/signal-1.png" alt="signalstrength"
$pos1 = strpos($html ,'signal-0.png" alt="signalstrength');  if ($pos1){$signal="0";}
$pos1 = strpos($html ,'signal-1.png" alt="signalstrength');  if ($pos1){$signal="1";}
$pos1 = strpos($html ,'signal-2.png" alt="signalstrength');  if ($pos1){$signal="2";}
$pos1 = strpos($html ,'signal-3.png" alt="signalstrength');  if ($pos1){$signal="3";}
$pos1 = strpos($html ,'signal-4.png" alt="signalstrength');  if ($pos1){$signal="4";}
$pos1 = strpos($html ,'signal-5.png" alt="signalstrength');  if ($pos1){$signal="5";}

$html = http_request('GET', $ip, 80 , $url3);
//RSRP	
//RSRQ	
//RSSI
$pos1 = strpos($html ,"RSRP"); 
$test = substr($html, ($pos1),42);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22); 
$Rpos = strpos($newtest, '</td>');
$RSRP = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);

$pos1 = strpos($html ,"RSRQ"); 
$test = substr($html, ($pos1),42);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22); 
$Rpos = strpos($newtest, '</td>');
$RSRQ = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);

$pos1 = strpos($html ,"RSSI"); 
$test = substr($html, ($pos1),42);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22); 
$Rpos = strpos($newtest, '</td>');
$RSSI = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);

//Current radio band</td>
//<td>LTE Band 30 - 2300MHz</td>
$pos1 = strpos($html ,"Current radio band"); 
$test = substr($html, ($pos1),75);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,32); 
$Rpos = strpos($newtest, '</td>');
$band = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);


$pos1 = strpos($html ,"Cell ID"); 
$test = substr($html, ($pos1),59);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,32); 
$Rpos = strpos($newtest, '</td>');
$cellid = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);
//
$pos1 = strpos($html ,"TAC"); 
$test = substr($html, ($pos1),42);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22); 
$Rpos = strpos($newtest, '</td>');
$tac = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);
$pos1 = strpos($html ,"RFCN"); 
$test = substr($html, ($pos1),42);//print"[$test]";
$Lpos = strpos($test, '<td>');
$newtest= substr($test,$Lpos,22); 
$Rpos = strpos($newtest, '</td>');
$rfcn = substr($newtest, 4,$Rpos-4);
//print substr($test, $Lpos+4,$Rpos-4);




$fileOUT = fopen("$path/$file", "a");
flock( $fileOUT, LOCK_EX );
fwrite ($fileOUT, "$time,$online,$status,$signal,$Temperature,$RSRP,$RSRQ,$RSSI,$cellid,$tac,$rfcn,$band,, \n");
flock( $fileOUT, LOCK_UN );
fclose ($fileOUT);
die;


function http_request(
    $verb = 'GET',             /* HTTP Request Method (GET and POST supported) */
    $ip,                       /* Target IP/Hostname */
    $port = 80,                /* Target TCP port */
    $uri = '/',                /* Target URI */
    $getdata = array(),        /* HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $postdata = array(),       /* HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $cookie = array(),         /* HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2') */
    $custom_headers = array(), /* Custom HTTP headers ie. array('Referer: http://localhost/ */
    $timeout = 5000,           /* Socket timeout in milliseconds */
    $req_hdr = false,          /* Include HTTP request headers */
    $res_hdr = false           /* Include HTTP response headers */
    )
{
global $BasicA,$agent,$version,$getheader,$htmlON,$responceHeader,$Postit,$req,$exede,$exedeCookie1,$exedeCookie2;

 $postdata_str="";


    if($Postit) {$postdata=$Postit;}

    if ($getheader){$res_hdr = true; }
    $ret = '';
    $verb = strtoupper($verb);
    $cookie_str = '';
    $getdata_str = count($getdata) ? '?' : '';
//    $postdata_str = '';
if (!$postdata_str){$postdata_str = '';}

    foreach ($getdata as $k => $v)
        $getdata_str .= urlencode($k) .'='. urlencode($v).'&';

    foreach ($postdata as $k => $v)
        $postdata_str .= urlencode($k) .'='. urlencode($v) .'&';

    foreach ($cookie as $k => $v)
        $cookie_str .= urlencode($k) .'='. urlencode($v) .'; ';

    $crlf = "\r\n";
//    $req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf;

    $req = $verb .' '. $uri .' HTTP/1.1' . $crlf;
    $req .= 'Host: '. $ip . $crlf;
    $req .= 'Connection: close' . $crlf;
    $req .= 'User-Agent: Mozilla/5.0 '. $agent . $crlf;
if ($BasicA){$req .= 'Authorization: Basic '. $BasicA . $crlf;}    $req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf;
    $req .= 'Accept-Language: en-us,en;q=0.5' . $crlf;
//    $req .= 'Accept-Encoding: deflate' . $crlf;
    $req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;

    foreach ($custom_headers as $k => $v)
        $req .= $k .': '. $v . $crlf;

    if (!empty($cookie_str))
        $req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf;

    if ($exede){
       $req .= "Cookie: _ga=$exedeCookie1" . $crlf;
       $req .= "Cookie: _mkto_trk=$exedeCookie2" . $crlf;
    }

    if ($verb == 'POST' && !empty($postdata_str))
    {
        $postdata_str = substr($postdata_str, 0, -1);
        $req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf;
        $req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf;
        $req .= $postdata_str;
    }
    else $req .= $crlf;

    if ($req_hdr)
        $ret .= $req;

    if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false){
    if ($errno=10060){$errstr="Timed Out";}
    return " error! $errno: $errstr";
    }
    fputs($fp, $req); //print "$req

    stream_set_timeout($fp, 0, $timeout * 1000);
        $ret = fgets($fp); $responceHeader =$ret; // gets responce header
// if is a webpage stop loading at the /html Prevents looping.
 while ($line = fgets($fp)) {
    $ret .= $line;
    if($htmlON) {$EndOfLine = strpos($line, '/html>'); if ($EndOfLine) { break;}}
  }
    fclose($fp);
    if (!$res_hdr){ $ret = substr($ret, strpos($ret, "\r\n\r\n") + 4);}
    return $ret;
}
