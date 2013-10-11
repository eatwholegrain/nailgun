<?php

require("lib/bootstrap.php");

$pid = $utilities->filter($_GET["pid"]);
$tid = $utilities->filter($_GET["tid"]);
$uid = $utilities->filter($_GET["uid"]);
$fid = $utilities->filter($_GET["fid"]);


$updateFiles = $uploads->getUpdateUpload($pid, $tid, $uid, $fid);

if ($updateFiles) {

    $updateFile = $uploads->getUpload($updateFiles[0]["id"]);

    $fileName = $updateFile[0]["name"];
    $fileType = $updateFile[0]["type"];
    $filePath = $updateFile[0]["path"];
    
    header('Cache-Control: public');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: application/octet-stream');
    //header('Content-type: '.$fileType);
	//header('Content-Disposition: attachment; filename="'.$fileName.'"');
	header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
	header('Accept-Ranges: bytes');
	readfile(ROOT.$filePath);
}
?>