<?php
/**
 * Created by Edward Rodriguez
 * Date: 04/30/2013
 * Time: 10:15 AM
 * 
 */

define("PATH",dirname(__FILE__));
define("RelativePath", PATH);
define("PathToCurrentPage", "/");
define("FileName","main.php");
define("LOGPATH","/blackcube/targets/");


date_default_timezone_set("America/Santo_Domingo");
include_once("Common.php");

/**
 * Available functions in this file
 * function setsniffer()
 * function create_logpath($phone)
 * function getcurrent_ip($phone)
 * function updatelastip($phone,$lastip)
 * function verifyipchange()
 * 
 */

function setsniffer() {

    $db = new clsDBdbConnection();

    $sql = "select phone from targets where status = 1"; //Phones to be sniffed
    $db->query($sql);
    while ($db->next_record()) {
        $phone = trim($db->f("phone"));
        $phonelogpath = create_logpath($phone);
        $srcip = getcurrent_ip($phone);
        if (strlen($srcip) > 0) {
            updatelastip($phone,$srcip);
            exec("./bctdump.sh $srcip $phonelogpath > /dev/null 2>&1");
        }

    }

    $db->close();

} //setsniffer

function stopsniffer() {
    $db = new clsDBdbConnection();
    $sql = "select phone from targets where status = 0"; //Phones to be sniffed
    $db->query($sql);
    while ($db->next_record()) {
        $phone = trim($db->f("phone"));
        $srcip = getcurrent_ip($phone);
        if (strlen($srcip) > 0) {
            exec("./bctstopdump.sh $srcip > /dev/null 2>&1");
        }

    }

    $db->close();

} //stopsniffer

function create_logpath($phone) {
    if (!(file_exists(LOGPATH))) {
        mkdir(LOGPATH,0755,true);
    }


    $phonelogpath = LOGPATH.$phone."/";
    if (!(file_exists($phonelogpath))) {
        mkdir($phonelogpath,0755);
    }

    $datefile = date("Ymd_His");
    $filename = $phone."_".$datefile.".bsc";
    /*
    if (!(file_exists($phonelogpath.$filename))) {
        mkdir($phonelogpath.$filename,0755);
    }
    */
    return $phonelogpath.$filename;

} //create_logpath

function getcurrent_ip($phone) {
    $db = new clsDBdbConnection();
    $currentip = trim(CCDLookUp("srcip","radcap","phone = '$phone' order by id desc limit 1",$db));
    $db->close();
    return $currentip;

} //getcurrent_ip


function updatelastip($phone,$lastip) {
    $db = new clsDBdbConnection();
    $sqlupdateip = "update targets set lastip = '$lastip' where phone = '$phone'";
    $db->query($sqlupdateip);
    $db->close();

} //updatelastip

function verifyipchange() {
    $db = new clsDBdbConnection();

    $sql = "select phone,lastip from targets where status = 1"; //Phones to be sniffed
    $db->query($sql);
    while ($db->next_record()) {
        $phone = trim($db->f("phone"));
        $lastip = trim($db->f("lastip"));
        $currentip = getcurrent_ip($phone);
        if ($currentip != $lastip) {
            //Phone changed ip address, stop current sniffing proccess
            exec("./bctstopdump.sh $lastip > /dev/null 2>&1");

        }

    } //while

    $db->close();
    
} //verifyipchange

//Stop active sniffings that changed ip address
verifyipchange();
//Active sniffing protocol for targets
setsniffer();
//Stop sniffing tasks for disabled targets
stopsniffer();

?>
 
