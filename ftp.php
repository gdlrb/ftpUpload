<?php
/**
 * Upload files to server
 */

ob_start();
set_time_limit(0);

$folder="doc"; //this is the folder that you want to upload with all subfolder and files of it.

$ftpDomain="fh3752ju.bget.ru"; //ftp domain name
$ftpUserName="fh3752ju_fh3752ju";  //ftp user name
$ftpPass="5ltlYmu6"; //ftp password
$ftpFolder="/public_html"; //ftp main folder

$ftpDomainConnect = ftp_connect($ftpDomain);
$ftpDomainLogin = ftp_login($ftpDomainConnect, $ftpUserName, $ftpPass);

if ((!$ftpDomainConnect) || (!$ftpDomainLogin)) {
    echo "Connection fail!";
    die();
}

function upload($catalog)
{
    global $currentCatalog, $fullCatalog, $ftpDomainConnect, $ftpFolder;

    chdir($catalog."\\");
    $openCatalog = opendir(".");

    while ($info=readdir($openCatalog)) {
        if ($info!='.' and $info!='..' and $info!="Thumbs.db") {
            $path="$catalog\\$info";

            $local=str_replace("".$currentCatalog."\\","",$catalog)."";
            $ftpPath="$local\\$info";
            $ftpCatalogs=str_replace(array("\\\\","\\"),array("/","/"),"$ftpFolder\\".str_replace("".$fullCatalog."","",$catalog)."\\$info");

            if(!is_dir($info)) {
                $setFiles = ftp_put($ftpDomainConnect, $ftpCatalogs, $path, FTP_BINARY);

                if (!$setFiles) {
                    echo "$ftpPath <span color=red>fail</span>"; echo "<br>"; fls();
                } else {
                    echo "$ftpPath <span color=green>uploaded</span>"; echo "<br>";  fls();
                }
            } else {
                ftp_mkdir($ftpDomainConnect, $ftpCatalogs);
                upload("$catalog\\$info");
                chdir($catalog."\\");
                fls();
            }
        }
    }
    closedir ($openCatalog);
}

function fls()
{
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
}

$currentCatalog=getcwd();
$fullCatalog=$currentCatalog."\\$folder";
upload($fullCatalog);
ftp_close($ftpDomainConnect);
