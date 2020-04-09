<?php
/**
 * script to generate Multi tenant Mysql
 * @author zied.th0@gmail.com
 * @company http://mind.engineering
 */

// this error htaccess Access-Control-Allow-Origin for sending post to host to another host 
header('Access-Control-Allow-Origin: http://whmcs.dev.local'); // allow access http://whmcs.dev.local

header('Access-Control-Allow-Methods: GET, POST'); // allow method of input

$con = mysqli_connect("mysql", "root", "password", "database") or die("Couldn't make connection.");

$mysqli = new mysqli("mysql", "root", "password", "database");


// select all table from your database.
$sql_Full_Tables = "SELECT table_name FROM information_schema.tables WHERE table_schema ='database' AND TABLE_TYPE = 'BASE TABLE'";

/* error 1067 when add id_tenant not added and when create view is an error
par defeaut sql_mode = ONLY_FULL_GROUP_BY,​STRICT_TRANS_TABLES,​NO_ZERO_IN_DATE,​NO_ZERO_DATE,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
dans cet cas il ya un erreur dans la creation des 3 view members et logs et ..... alors changé sql_mode = ''; par vide

par default
ONLY_FULL_GROUP_BY,​STRICT_TRANS_TABLES,​NO_ZERO_IN_DATE,​NO_ZERO_DATE,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
*/

$mysqli->query("SET SESSION sql_mode = '';");
$mysqli->query("SET GLOBAL sql_mode = '';");

// create table for all Instance suspended for not add aculumn actif yes or non.
$mysqli->query("CREATE TABLE IF NOT EXISTS `prefix_suspend` (`suspend_id` int(11) NOT NULL AUTO_INCREMENT,`etat` varchar(20) NOT NULL DEFAULT 'null',PRIMARY KEY (`suspend_id`));");

$sql_result1 = mysqli_query($con, $sql_Full_Tables) or die("execute query 1 All Tables!");



    //this step is the begin of Multi tenant MySql .....
while ($row1 = mysqli_fetch_array($sql_result1)) {

    $tab = explode('_', $row1['table_name'], 2);
    $TableName = $tab[0];

    if ($TableName == "prefix") {
        $View_Name = $tab[1];
        $TableName = $row1['table_name'];
    } else {
        $View_Name = $row1['table_name'];
        $TableName = "prefix_" . $row1['table_name'];
        if ($mysqli->query("RENAME TABLE " . $View_Name . " TO " . $TableName)) {

        } else {
            printf("Échec : %s\n", $mysqli->error);

        }
    }

    // test sur l'ajout des id_tenant sur chaque table
    if ($result = $mysqli->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $TableName . "' AND COLUMN_NAME = 'id_tenant' ORDER BY ORDINAL_POSITION")) {
        if ($result->num_rows == 0) {
            //si il n ya pas des id_tenant  les ajouté
            $mysqli->query("ALTER TABLE " . $TableName . " ADD COLUMN id_tenant VARCHAR(65) NOT NULL DEFAULT 'root' ;");
        }

    } else {
        printf("Échec when add COLUMN id_tenant : %s\n", $mysqli->error);
    }

    $trigger_Name = "bi_" . $TableName;

    $mysqli->query("DROP TRIGGER IF EXISTS" . $trigger_Name);

    if ($mysqli->query("CREATE TRIGGER " . $trigger_Name . " BEFORE INSERT ON " . $TableName . " FOR EACH ROW thisTrigger: BEGIN IF (SUBSTRING_INDEX(USER(),'@',1) = 'root') THEN LEAVE thisTrigger; END IF; SET new.id_tenant = SUBSTRING_INDEX(USER(),'@',1); END")) {

    } else {
        // if you want to put message for error Triger
        //printf("Échec : CREATE TRIGGER  %s\n", $mysqli->error);
    }


    $all_columns = [];
    if ($sql_Columns = $mysqli->query("SHOW COLUMNS FROM " . $TableName)) {
        foreach ($sql_Columns as $columnInfo) {
            $fieldName = $columnInfo['Field'];
            if ($fieldName != 'id_tenant') {
                $columnsName = "`" . $columnInfo['Field'] . "`";
                array_push($all_columns, $columnsName);
            }
        }
            //print all columns for testing only.
         // print_r($all_columns);
        $fieldList = implode(",", $all_columns);
    } else {
        printf("<br>Échec SHOW COLUMNS : %s\n", $mysqli->error);
    }

    //select a name for all creating View.
    $nameView = $View_Name;
    //creating Views.
    $query = "CREATE OR REPLACE VIEW `" . $nameView . "` AS SELECT " . $fieldList . " FROM `" . $TableName . "` WHERE (id_tenant = SUBSTRING_INDEX(USER( ),'@',1));";

    $stmt = $mysqli->prepare($query);

    if (!$stmt->execute()) {
        $arr = $stmt->errorInfo();
        print_r($arr);
        echo "error when create Views" . $mysqli->error;
    }

}


//finally your database is genereted next create users and ensure they only have access to our views.

// select all Tables and Views.
$sql_ViewAndTables = "SHOW FULL TABLES";

// group all tables/views into two buckets, allowed & banned
$allowedTables = [];
$bannedTables = [];

if ($sql_ViewAnd_Tables = $mysqli->query("SHOW FULL TABLES")) {
    foreach ($sql_ViewAnd_Tables as $ViewTable) {
        if ($ViewTable['Table_type'] != "BASE TABLE") {
            // this is a view, it's ok this work basic with Views.
            array_push($allowedTables, $ViewTable['Tables_in_database']);
        } else {
            array_push($bannedTables, $ViewTable['Tables_in_database']);
        }
    }

} else {
    printf("<br>Échec group all tables/views : %s\n", $mysqli->error);
}

// create the user and setting privilége.
// WHMCS will send a key and post data to create Tenant.

function blowfish_encrypt($password = "")
{
    $timeTarget = 0.1;
    $cost = 8;
    do {
        $cost++;
        $start = microtime(true);
        password_hash("calculate_cost", PASSWORD_BCRYPT, ["cost" => $cost]);
        $end = microtime(true);
    } while (($end - $start) < $timeTarget);
    $options = ['cost' => $cost];

    return password_hash($password, PASSWORD_BCRYPT, $options);
}





        if( isset($_POST['Name_Instance']) && isset($_POST['Email_Instance']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['password']) && isset($_POST['phone']) && isset($_POST['adress']) && isset($_POST['gender'])){

                // get the last number of id to do a name_Instance randomized with this number id 
                if($res = $mysqli->query("SELECT MAX(id) FROM prefix_members ")){
                    foreach ($res as $columnInfo) {
                        $LastId = $columnInfo['MAX(id)'];
                    }
                }
                // name of Instance = user in MySql.
                $USER = $_POST['Name_Instance'].$LastId;
                $Email = $_POST['Email_Instance'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $password = blowfish_encrypt($_POST['password']);
                $phone = $_POST['phone'];
                $adress = $_POST['adress'];
                $gender = $_POST['gender'];



                // if user not exist create user and giv hem privilege for views......
                if($mysqli->query("CREATE USER `" . $USER . "` IDENTIFIED BY 'password';")){
                    // grant them access.
                    $mysqli->query("GRANT LOCK TABLES ON *.* TO '" . $USER . "'@'%';");

                    // get all views and giv the user privilege tor hem.
                    foreach ($allowedTables as $table) {
                        $mysqli->query("GRANT SELECT, UPDATE, INSERT, DELETE ON database." . $table . " TO `" . $USER . "`");
                    }

                    // revoke privilege from all table.
                    foreach ($bannedTables as $table) {
                        $mysqli->query("REVOKE ALL ON database." . $table . " FROM `" . $USER . "`");
                    }
                    // connect to user created to add email and password.
                    $mysqli_insert = new mysqli("mysql", $USER, "password", "database");

                    // testing for existing email if you want
                    /*if ($result = $mysqli->query("SELECT * FROM prefix_members where email = '".$Email."' ;")) {
                        if ($result->num_rows == 0) {
                        }else {
                            echo json_encode(array("success" => false, 'message' => 'Échec this Instance have an exist email !!!!!'));
                        }
                    }*/

                    $mysqli_insert->query(" INSERT INTO `members` (`id`, `first_name`, `last_name`, `role_id`, `email`, `password`, `image`, `status`, `disable_login`, `address`, `alternative_address`, `phone`, `alternative_phone`, `dob`, `gender`, `skype`, `created_at`, `notification_checked_at`, `sticky_note`) VALUES ('0', '".$first_name."', '".$last_name."', '0', '".$Email."', '".$password."', '', 'active', '0', '', '', '".$phone."', '".$phone."', '2000-01-23', '".$gender."', '', '2020-01-23 00:00:00', '2020-01-23 00:00:00', '');  ");

                    $mysqli->query("FLUSH PRIVILEGES");
                    echo json_encode(array("success" => true, 'message' => 'seccess create user','instance' => $USER));

                }else {
                    echo json_encode(array('success' => false, 'message' => 'user exist !!!!!'));
                }
        }

        if(isset($_POST['Suspend_Instance'])) {
            $USER = $_POST['Suspend_Instance'];
            if ($result = $mysqli->query("SELECT * FROM prefix_suspend where id_tenant = '" . $USER . "' ;")) {
               //exist instace and updated Suspend user MySql or instance Inserted.
                if ($result->num_rows == 1) {
                    $mysqli->query("UPDATE `prefix_suspend` SET `etat`= 'false' where id_tenant ='" . $USER . "' ;");
                    echo json_encode(array("success" => true, 'message' => 'exist instace and updated Suspend user MySql or instance Inserted. '));
                }else {
                    //Suspend user MySql or instance Inserted.
                    $mysqli->query("INSERT INTO `prefix_suspend`(`suspend_id`, `etat`, `id_tenant`) VALUES ('0','false','" . $USER . "');");
                    echo json_encode(array("success" => true, 'message' => 'Suspend user MySql or instance Inserted. '));
                }

            }
        }
        if(isset($_POST['delet_Suspend_Instance'])) {
            $USER = $_POST['delet_Suspend_Instance'];
            $mysqli->query("UPDATE `prefix_suspend` SET `etat`= 'true' where id_tenant ='" . $USER . "' ;");
            echo json_encode(array("success" => true, 'message' => 'Activet user MySql or instance Inserted. '));
        }

        if(isset($_POST['new_Instance']) && isset($_POST['old_Instance'])) {
            // name instance updated by the client .....
            $new_instance = $_POST['new_Instance'];
            $old_instance = $_POST['old_Instance'];
            $tab[0] = false;
            $tab[1] = 'name of instance exist';
            
            
            if($mysqli->query("RENAME USER ".$old_instance." TO ".$new_instance." ;"))
            {

                 if ($sql_ViewAnd_Tables = $mysqli->query("SHOW FULL TABLES")) {
                            foreach ($sql_ViewAnd_Tables as $ViewTable) {
                                if ($ViewTable['Table_type'] = "BASE TABLE") {
                                    // delete all rows contains id_tenant 
                                $mysqli->query("UPDATE `".$ViewTable['Tables_in_database']."` SET `id_tenant`='".$new_instance."' WHERE `id_tenant` ='".$old_instance."' ; ");
                                }
                            }
                        }

                $tab[0] = true;
                $tab[1] = 'update success';
                echo json_encode($tab);
                // echo json_encode(array("success" => true, 'message' => 'update success'));
            } else 
            {
                echo json_encode($tab);
               // echo json_encode(array("success" => false, 'message' => 'name of instance exist '));
            }
                
        }
            if(isset($_POST["delete_instance"])){
                    $id_tenant = $_POST["delete_instance"];
                if($mysqli->query("drop user ".$id_tenant." ;")){
                        
                        if ($sql_ViewAnd_Tables = $mysqli->query("SHOW FULL TABLES")) {
                            foreach ($sql_ViewAnd_Tables as $ViewTable) {
                                if ($ViewTable['Table_type'] = "BASE TABLE") {
                                    // delete all rows contains id_tenant 
                                    $mysqli->query("DELETE FROM ".$ViewTable['Tables_in_database']." WHERE id_tenant='".$id_tenant."' ");
                                }
                            }
                        }
                       echo json_encode(array("success" => true, 'message' => 'seccess drop user')); 
                }
            }
            
    }else {
        echo json_encode(array("success" => false, 'message' => 'Échec invalid Key  !!!!!'));
    }



