<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');

session_start();

$pdo_school = new PDO(
    "mysql:host=localhost;dbname=school;charset=utf8",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$pdo_accounts = new PDO(
    "mysql:host=localhost;dbname=user_accounts;charset=utf8",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


function logged_in_message ($arg_status, $arg_message, $arg_username, $arg_first_name, $arg_last_name) {
    return [
        "status" => $arg_status,
        "message" => $arg_message,
        "username" => $arg_username,
        "first_name" => $arg_first_name,
        "last_name" => $arg_last_name,
    ];
}

if (array_key_exists("logout-request", $_POST)) {
    unset($_SESSION["logged-in"]);
    header("Location: ?");
    exit;
}

when_logged_in();

if (array_key_exists("login-request", $_POST) 
    && !array_key_exists("username", $_POST)
    && !array_key_exists("password", $_POST)) {

    if (array_key_exists("logged-in", $_SESSION)) {
        $query = $pdo_accounts->prepare("SELECT * FROM user_account WHERE id=?");
        $query->execute([$_SESSION["logged-in"]]);
        $user = $query->fetch();

        $profile = get_profile($user);
        echo json_encode(logged_in_message("logged-in", "session", $user["username"], $profile["first_name"], $profile["last_name"]));
    } else {
        echo json_encode(["status" => "error", "message" => "nosession"]);
    }

} else if (array_key_exists("login-request", $_POST)
    && array_key_exists("username", $_POST)
    && array_key_exists("password", $_POST)) {

    //legit login request -> extract user account from database

    $query = $pdo_accounts->prepare("SELECT * FROM user_account WHERE username=? AND password=?");
    $query->execute([$_POST["username"], $_POST["password"]]);
    $user = $query->fetch();

    if ($user != false) {
        $_SESSION["logged-in"] = $user["id"];
        $profile = get_profile($user);
        echo json_encode(logged_in_message("logged-in", "success", $user["username"], $profile["first_name"], $profile["last_name"]));
    } else {
        echo json_encode(["status" => "error", "message" => "credentials mismatch"]);
    }
}

function get_profile($user) {
    if (in_array($user["user_type"], ["student","teacher"])) {
        $query = $GLOBALS["pdo_school"]->prepare("SELECT * FROM {$user["user_type"]} WHERE id=?");
        $query->execute([$user["user_id"]]);
        $profile = $query->fetch();
        return $profile;
    }
}

function when_logged_in () {
    if (!array_key_exists("logged-in", $_SESSION)) {
        //echo json_encode(["error" => "nosession"]);
        return;
    }

    $sql_vyucujiciho_studenti = "SELECT
    --teacher.id AS teacher_id,
    teacher.first_name AS teacher_first_name,
    teacher.last_name AS teacher_last_name,
    --student.id AS student_id,
    student.first_name AS student_first_name,
    student.last_name AS student_last_name,
    subject.id AS subject_id,
    subject.short AS subject_short,
    subject.name AS subject_name
    FROM teacher
    JOIN group_teacher ON teacher.id=group_teacher.teacher_id AND teacher.id=?
    JOIN `group` ON group_teacher.group_id=`group`.id
    JOIN group_student ON `group`.id=group_student.group_id
    JOIN student ON group_student.student_id=student.id
    JOIN subject ON `group`.subject_id=subject.id;";

    $query = $GLOBALS["pdo_accounts"]->prepare("SELECT * FROM user_account WHERE id=?");
    $query->execute([$_SESSION["logged-in"]]);
    $user = $query->fetch();

    //var_dump($user);
    
    if (array_key_exists("students-request", $_POST)) {
        $query = $GLOBALS["pdo_school"]->prepare($sql_vyucujiciho_studenti);
        $query->execute([$user["user_id"]]);
        $result = $query->fetchAll();
        echo json_encode($result);
    }


}

//echo json_encode($_POST);

?>