<?php
$db_instance = new PDO(
    "mysql:host=localhost;dbname=school;charset=utf8",
    "root",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);

function iterate_data_to_table ($arg_data) {
    echo "<table border=\"1\">";
    foreach ($arg_data as $key => $radek) {
        echo "<tr>";
        foreach ($radek as $sloupec) {
            echo "<td>$sloupec</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

$sql_1 = "SELECT student.first_name AS student_first_name, student.last_name AS student_last_name, teacher.first_name AS teacher_first_name, teacher.last_name AS teacher_last_name FROM student
LEFT OUTER JOIN `group_student` ON group_student.student_id=student.id
LEFT OUTER JOIN `group` ON group_student.group_id=`group`.id
LEFT OUTER JOIN group_teacher ON group_teacher.group_id=`group`.id
LEFT OUTER JOIN teacher ON group_teacher.teacher_id=teacher.id;
";
$sql_vyucujiciho_studenti = "SELECT
teacher.id AS t_id,
teacher.first_name AS teacher_first_name,
teacher.last_name AS teacher_last_name,
student.id AS s_id,
student.first_name AS student_first_name,
student.last_name AS student_last_name
FROM teacher
JOIN group_teacher ON teacher.id=group_teacher.teacher_id AND teacher.id=?
JOIN `group` ON group_teacher.group_id=`group`.id
JOIN group_student ON `group`.id=group_student.group_id
JOIN student ON group_student.student_id=student.id;";

$query = $db_instance->prepare($sql_1);
$query->execute();
$data = $query->fetchAll();

if (array_key_exists("submit-vyucujici", $_POST)) {
    $vyucujici = $_POST["vyucujici"];

    $query = $db_instance->prepare($sql_vyucujiciho_studenti);
    $query->execute([$vyucujici]);
    $result_studenti = $query->fetchAll();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>database</title>
</head>
<body>

    <?php
        var_dump($data);
    
        echo "<ul>";
        foreach ($data as $item) {
            echo "<li>{$item["teacher_last_name"]} - {$item["student_last_name"]}</li>";
        }
        echo "</ul>";
    ?>

    <form action="" method="post">
        <label>ucitel: </label>
        <input type="text" name="vyucujici">
        <input type="submit" name="submit-vyucujici" value="zobrazit">
    </form>

    <?php var_dump($result_studenti); 
        iterate_data_to_table($result_studenti)
    ?>

</body>
</html>