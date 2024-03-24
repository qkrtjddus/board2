<?php
include 'dbconfig.php';

//print_r($_POST);

$name     = (isset($_POST['name']) && $_POST['name'] != '') ? $_POST['name']: '';
$password = (isset($_POST['password']) && $_POST['password'] != '') ? $_POST['password']: '';
$subject  = (isset($_POST['subject']) && $_POST['subject'] != '') ? $_POST['subject']: '';
$content  = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content']: '';
$code     = (isset($_POST['code']) && $_POST['code'] != '') ? $_POST['code']: '';

if($code == 'undefined'){
    $code = 'freeboard';
}

//비밀번호 단방향 암호화

$pwd_hash = password_hash($password, PASSWORD_BCRYPT);

$img_array = [];
// 정규식, 정규표현식 EXP
preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);

foreach($matches[1] AS $key => $val){
    // data:image/png;base64,AAAFBfj42Pj4kjlkjjkjjkjkj
    list($type, $data)=explode(';', $val);
    //$type : data:image/png
    //$data : base64,AAAFBfj42Pj4kjlkjjkjjkjkj
    list(, $ext) = explode('/', $type);
    $ext = ($ext == 'jpeg') ? 'jpg' : $ext;
    
    $filename = date('YmdHis') .'_'. $key .'.'. $ext; //202312414124123_0.png

    list(,$base64_decode_data) = explode(',', $data);
    
    $rs_code = base64_decode($base64_decode_data);
    file_put_contents("upload/". $filename, $rs_code);

    $img_array[] = "upload/". $filename;

    $content = str_replace($val, "upload/". $filename, $content);
}
$imglist = implode('|', $img_array);

$sql = "INSERT INTO board (code, name, subject, password, content, imglist, ip, rdate)
VALUES(:code, :name, :subject, :password, :content, :imglist, :ip, NOW())";

$ip = $_SERVER['SERVER_ADDR'];

$stmt = $conn->prepare($sql);
$stmt->bindParam(':code', $code);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':subject', $subject);
$stmt->bindParam(':content', $content);
$stmt->bindParam(':password', $pwd_hash);
$stmt->bindParam(':imglist', $imglist,PDO::PARAM_STR);
$stmt->bindParam(':ip', $ip);
$stmt->execute();

$arr = ['result' => 'success'];

$j = json_encode($arr);
die($j);