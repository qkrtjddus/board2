<?php
require 'dbconfig.php';

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';
$idx = (isset($_POST['idx']) && $_POST['idx'] != '' && is_numeric($_POST['idx'])) ? $_POST['idx'] : '';
$code = (isset($_POST['code']) && $_POST['code'] != '') ? $_POST['code'] : '';
$password = (isset($_POST['password']) && $_POST['password'] != '') ? $_POST['password'] : '';


if ($mode == '') {
    $arr = ['result' => 'empty_mode'];
    exit(json_encode($arr));
}

if ($idx == '') {
    $arr = ['result' => 'empty_idx'];
    exit(json_encode($arr));
}

if ($password == '') {
    $arr = ['result' => 'empty_password'];
    exit(json_encode($arr));
}

$sql = "SELECT password, code FROM board WHERE idx=:idx";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':idx', $idx);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$row = $stmt->fetch();

if ($row['code'] != $code){
    $arr = ['result' => 'wrong_code'];
    die(json_encode($arr));
}

if (password_verify($password, $row['password'])) {
if ($mode == 'delete'){
    $sql = "DELETE FROM board WHERE idx=:idx";
    $stmt = $conn->prepare(($sql));
    $stmt->bindParam(':idx', $idx);
    $stmt->execute();

    $arr = ['result' => 'delete_success'];

}else if ($mode == 'edit') {
     session_start();
     $_SESSION['edit_idx'] = $idx;
     $arr = ['result' => 'edit_success'];

} else {
    $arr = ['result' => 'wrong_mode'];
    
}

die(json_encode($arr));

} else{
    //비번 오류시
    $arr = ['result' => 'wrong_password'];
    die(json_encode($arr));
}
?>