<?
$dbgloc=false;
function echdb($str) {
global $dbgloc;
if ($dbgloc) echo $str;
}
?>