<?php
include 'dbconfig.php';

//print_r($_POST);

//isset ->이 값이 세팅이 되어 있니? 되어 있으면서 값이 비어있지 않니?
// ? 참 : 거짓;
$name     = (isset($_POST['name'    ]) && $_POST['name'    ] != '') ? $_POST['name'    ]: ''; 
$password = (isset($_POST['password']) && $_POST['password'] != '') ? $_POST['password']: ''; 
$subject  = (isset($_POST['subject' ]) && $_POST['subject' ] != '') ? $_POST['subject' ]: ''; 
$content  = (isset($_POST['content' ]) && $_POST['content' ] != '') ? $_POST['content' ]: ''; 
$code     = (isset($_POST['code'    ]) && $_POST['code'    ] != '') ? $_POST['code'    ]: ''; 
//어디가 공백이 있어도 되는지 없어도 되는지 꼭 체크!
//깔끔하게 맞춰놓으면 에러찾기 수월!

if($code == 'undefinged'){ 
  $code = 'freeboard';
}



//비밀번호 단방향 암호화
$pwd_hash = password_hash($password, PASSWORD_BCRYPT);

//정규식, 정규표현식 EXP
preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);

$img_array = [];
foreach($matches[1] AS $key => $val){
  // data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAUFBQUFBQUGBgUICAcIC
  list($type, $data) = explode(';', $val);
  // $type : data:image/jpeg
  // $data : base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAUFBQUFBQUGBgUICAcIC
  list(, $ext) = explode('/', $type);
  $ext = ($ext =='jpeg') ? 'jpg' : $ext;
  $filename = date('YmdHis').'_'. $key .'.'.$ext; //20231212062042_0.jpg 이미지 여러개업로드시 파일명이 겹치면 안되므로 $key활용하여 숫자 기입

  

  list(,$base64_decode_data) = explode(',', $data);// 뒷부분만 필요 ',' 기준으로 자름

  $rs_code = base64_decode($base64_decode_data);

  file_put_contents("upload/".$filename, $rs_code);

  $img_array[] = "upload/".$filename;
  //$content = str_replace(바꿀대상, 변경할 이름, 본문대상);
  $content = str_replace($val, "upload/".$filename, $content);

} 

$imglist = implode('|', $img_array);

//DB에 Insert
//rdate = 저장날짜
$sql = "INSERT INTO mboard (code, name, subject, password, content, imglist, ip, rdate)
VALUES(:code, :name, :subject, :password, :content, :imglist, :ip, NOW())";

$ip = $_SERVER['REMOTE_ADDR'];

$stmt = $conn->prepare($sql);
$stmt->bindParam(':code'    , $code    );
$stmt->bindParam(':name'    , $name    );
$stmt->bindParam(':subject' , $subject );
$stmt->bindParam(':content' , $content );
$stmt->bindParam(':password', $pwd_hash);
$stmt->bindParam(':imglist' , $imglist );
$stmt->bindParam(':ip'      , $ip      );
$stmt->execute();

//저장이 다 되고 결과를 알려줌
$arr = ['result' => 'success'];

$j = json_encode($arr); // json인코딩을하면 {}"result" : "success:} 이런식으로 문자열이 바뀌어 들어감
die($j);





?>