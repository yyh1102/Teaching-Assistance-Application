<?php
/**
 * Created by PhpStorm.
 * User: Wu
 * Date: 2016/11/28
 * Time: 16:18
 */
header('Content-type: application/json');
session_start();
// Connect database
include '../login/_include.php';
global $conn;
connectDB();
//Verify token
loginCheck($_SERVER['HTTP_X_ACCESS_TOKEN']);
//Get information
$class_id = test_input(mysqli_escape_string($conn, $_POST['class_id']));
$title = test_input(mysqli_escape_string($conn, $_POST['title']));
$deadline = test_input(mysqli_escape_string($conn, $_POST['deadline']));
$type = test_input(mysqli_escape_string($conn, $_POST['type']));
$punish_type = test_input(mysqli_escape_string($conn, $_POST['punish_type']));
$punish_rate = test_input(mysqli_escape_string($conn, $_POST['punish_rate']));
if($punish_type==0) $punish_rate=1.00;
$time = date('y-m-d h:i:s',time());
//Class_id,title,deadline,type, punish_type,punish_rate
$query_result = mysqli_query($conn, "INSERT INTO homework 
                                     (class_id,title,deadline,type,punish_type,punish_rate,publish_time,over)
                                      values('$class_id','$title','$deadline','$type','$punish_type','$punish_rate','$time',0);");
if($query_result){
    $result = array(
        "code" => 0,
        "msg" => "作业发布成功",
        "res" => array(
            "token" => $_SESSION['token']
        )
    );
    echo json_encode($result);
}
else{
    $result = array(
        "code" => -1,
        "msg" => "作业发布失败",
        "res" => array(
            "token" => $_SESSION['token']
        )
    );
    echo json_encode($result);
}
mysqli_close($conn);
?>