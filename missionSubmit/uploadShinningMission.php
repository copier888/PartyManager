<?php
/**
 * Created by PhpStorm.
 * User: TreeNewBeee
 * Date: 2017-04-19
 * Time: 10:59
 */
$path = "../Files/".date("Y")."/".date("m")."/ShinningMissionFiles/";
if (!is_dir($path)){
    mkdir($path,0777,true);
}                         // 以年/月/类别为路径存储，如果不存在该路径则创建


    if ($_FILES['file']['name'] != '') {
        $filename = "JB-Shinning-".date("Y-m")."-".$_FILES['file']['name'];    // 重命名文件
        if ($_FILES['file']['error'] > 0) {
            echo "错误状态：" . $_FILES['file']['error'];
        } else {
            if (file_exists($path . $_FILES["file"]["name"])) {
//                echo $_FILES["file"]["name"] . " already exists. ";
            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"],
                    $path . iconv('utf-8', 'gb2312', $filename));
//                echo "文件保存在: " . "/Files/" . $_FILES["file"]["name"] . " <br />";
//                echo "类型: " . $_FILES["file"]["type"] . "<br />";
//                echo "大小: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
                echo "<script>alert('创建成功！');</script>";
            }


        }
    } else {
//        echo "<script>alert('请上传文件！');</script>";
    }

    require_once '../db_login.php';
    $conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
    if ($conn->connect_error) die($conn->connect_error);
    mysqli_set_charset($conn, 'utf8');
    $today = date("Y-m-d");
    $now = date("H:i:s");

//    echo $_POST['branch'];

    /*将任务记录*/
    $query = "INSERT INTO `shiningmission` (`id`, `publisher`, `title`, `annix`,
              `details`, `branch`, `timeLimit`) VALUES (NULL, '".$_POST['publisher']."',
               '" . $_POST['title'] . "', '" . $path.$filename . "', 
               '" . $_POST['details'] . "', '" . $_POST['branch'] . "', '" . $_POST['timeLimit'] . "');";
    $conn->query($query);

    /*为每个党支部创建任务*/

    $query = "INSERT INTO `missionlog` (`id`, `title`, `publisher`, `annix`, `details`, `publishTime`,
                `timeLimit`, `finishTime`, `type`, `status`, `score`, `annixSubmit`, `branch`,
                 `submitter`) VALUES 
                 (NULL, '" . $_POST['title'] . "', '".$_POST['publisher']."', '" . $path.$filename . "',
                  '" . $_POST['details'] . "','".$today."', '" . $_POST['timeLimit'] . "', NULL, '亮点工作', '已上传',
                   '0', NULL, '" . $_POST['branch'] . "', '".$_POST['publisher']."');";

    $conn->query($query);

    $query = "INSERT INTO `msg` (`ID`, `branch`, `date`, `time`, `title`, `type`, `status`, `processing`) 
                          VALUES (NULL, '" . $_POST['branch'] . "', '" . $today . "', '" . $now . "', '" . $_POST['title'] . "',
                           '亮点工作', '已上传', '未处理');";
    $conn->query($query);

    $conn->close();

    echo "<script> window.location.href='../missionList.php?branch={$_POST['branch']}&type=亮点工作';</script>";

