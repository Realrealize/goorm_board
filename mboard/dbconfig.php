<?php
$servername = 'localhost';
$username = 'root';
$password = 'ghddudckd';
$db = 'kingchobo';

try {
  $conn = new PDO("mysql:host=".$servername.";dbname=".$db, $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //이거 안하면 에러 메시지 못봄
  //echo "DB 연결 성공";
}catch(PDOException $e){
  echo "Connection failed: ". $e->getMessage();
  //어떤 에러인지 보고 찍어봄
}

//PDO를 쓰는 이유는 PDO 객체는 다른 데이터베이스 연결할때도 표준으로 쓸 수 있기 때문에 추천!
//create datbase kingchobo;


?>