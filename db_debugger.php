<?php

session_start();
include('system/config.php');
include('system/db_connect.php');

$baseurl  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$baseurl .= $_SERVER['SERVER_NAME'];
if (strpos($baseurl, 'localhost') !== false) {
    $baseurl .= ":".$_SERVER['SERVER_PORT'];
}
//$baseurl .= htmlspecialchars($_SERVER['REQUEST_URI']);
$cleanuri = explode('?', $_SERVER['REQUEST_URI'], 2);
$baseurl .= htmlspecialchars($cleanuri[0]);

try{

    //postgres for prod
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //delete tool
    //$deleteid = [val];
    //$db->exec("DELETE FROM [table] WHERE [col] = [val]");

    //table create tool
    //$db->exec("CREATE TABLE IF NOT EXISTS [table] (email TEXT PRIMARY KEY, password TEXT, fname TEXT, lname TEXT, admin BOOLEAN)");

    //add row tool
    // $input_email = "jlatimer@bna.com";
    // $input_password = "temp4jared";
    // $password_store = password_hash($input_password, PASSWORD_BCRYPT);
    // $input_fname = "Jared";
    // $input_lname = "Latimer";
    // $input_cms = 1;
    // $input_admin = 0;

    // $input_email = "treeder@bna.com";
    // $input_password = "crystalcity";
    // $password_store = password_hash($input_password, PASSWORD_BCRYPT);
    // $input_fname = "Tim";
    // $input_lname = "Reeder";
    // $input_cms = 1;
    // $input_admin = 1;

    //FYI $checkpass = password_verify($inputpass, $storedpass) yields t or f for pw check

    // $insert = $db->prepare("INSERT INTO [table] (email, password, fname, lname, cms, admin) VALUES (?, ?, ?, ?, ?, ?)");
    // $insertarray = array($input_email, $password_store, $input_fname, $input_lname, $input_cms, $input_admin);
    // $insert->execute($insertarray); 

    //update row tool
    // $update = $db->prepare("UPDATE [table] SET title = :titleinput, body = :bodyinput WHERE uid = $uid");
    // $update->bindParam(':titleinput', $titleinput, PDO::PARAM_STR);
    // $update->bindParam(':bodyinput', $bodyinput, PDO::PARAM_STR);
    // $update->execute();

    //table delete tool
    //$db->exec("DROP TABLE budgets");

    //row delete tool
    //$db->exec("DELETE FROM users WHERE email = 'melanie.s.reeder@gmail.com'");

    //queries
    $tables = $db->query("SELECT * FROM sqlite_master where type='table';");

    foreach ($tables as $table) {
        $tablename = $table['name'];
        $tablequery = $tablename . "query";
        $$tablequery = $db->query("SELECT * FROM $tablename");
        $tablearray[$tablename] = $tablequery;
    }

    $users = $db->query("SELECT * FROM users");



    // close the database connection
    $db = NULL;
}
catch(PDOException $e){
    $statusMessage = $e->getMessage();
    $statusType = "danger";
}

?>

<?php include('views/header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-xs-12" style="margin-top: 24px;">

            <?php
                if (!empty($statusMessage)){
                    echo "<div id='' class='alert alert-" . $statusType . " notif-alert' role='alert'>". $statusMessage . "</div>";
                }
            ?>

            <h1>TABLES</h1>

            <? foreach ($tablearray as $tablename => $tablequery): ?>
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading"><?php echo $tablename; ?></div>
                <!-- Table -->
                <table class="table table-striped">

                <?php
                    $firstrow = true;
                    $tablerows = $$tablequery->fetchAll(PDO::FETCH_ASSOC);
                    foreach($tablerows as $tablerow){
                        if($firstrow){
                            foreach($tablerow as $columnvalue => $cellvalue){
                                $columns[] = $columnvalue;
                                $cellvalues[] = $cellvalue;
                            }
                            echo "<thead><tr>";
                            foreach($columns as $column){
                                echo "<th>" . $column . "</th>";
                            }
                            echo "</tr></thead><tbody>";
                            echo "<tr>";
                            foreach($cellvalues as $cellvalue){
                                echo "<td>" . $cellvalue . "</td>";
                            }
                            echo "</tr>";
                        }
                        else{
                            echo "<tr>";
                            foreach($tablerow as $column => $cellvalue){
                                echo "<td>" . $cellvalue . "</td>";
                            }
                            echo "</tr>";
                        }
                        $firstrow = false;
                        unset($columns);
                        unset($cellvalues);
                    }
                ?>
                    </tbody>
                </table>
            </div>
            <? endforeach; ?>

        </div>
    </div>
</div>

<?php include('views/ender.php'); ?>