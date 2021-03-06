<?php
/**
 * Created by PhpStorm.
 * User: Wu
 * Date: 2016/11/27
 * Time: 20:56
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
$title = test_input(mysqli_escape_string($conn, $_POST['title']));
$content = test_input(mysqli_escape_string($conn, $_POST['content']));
$level = test_input(mysqli_escape_string($conn, $_POST['level']));
$teacher_id = test_input(mysqli_escape_string($conn, $_POST['teacher_id']));
$time = date('Y-m-d H:i:s',time());
$notice_id = test_input(mysqli_escape_string($conn, $_POST['notice_id']));
if($_SESSION['type']!=2){
    $result = array(
        "code" => 403,
        "msg" => "无效用户尝试操作",
        "res" => null
    );
    echo json_encode($result);
    exit;
}
$query_result = mysqli_query($conn, "update notification
                                     set title = '$title', content = '$content' , level = '$level' , teacher_id = '$teacher_id', time = '$time'
                                     WHERE noti_id = '$notice_id';");
if($query_result){
    $result = array(
        "code" => 0,
        "msg" => "修改成功",
        "res" => array(
            'notice_id' => $notice_id,
            'title' => $title,
            'level' => $level,
            'time' => $time,
            'content' => htmlspecialchars_decode($content),
            'teacher_id'=>$teacher_id,
            "token" => $_SESSION['token']
        )
    );
    echo json_encode($result);
}
else{
    $result = array(
        "code" => -1,
        "msg" => "修改失败",
        "res" => array(
            "token" => $_SESSION['token']
        )
    );
    echo json_encode($result);
}
mysqli_close($conn);
?>