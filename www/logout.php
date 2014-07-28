<?
include '../include/lib.php';

session_destroy();
$_SESSION[] = array();

header('location: login.php?out=true');

if(DEBUG==true) {
    echo "<pre class='debug'>";
    echo '$_SESSION=';
    print_r($_SESSION);
    echo '$_POST=';
    print_r($_POST);
    echo '$_GET=';
    print_r($_GET);
    echo "</pre><br />";
}