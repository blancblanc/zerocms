<?php
    
    //REQ  $listtitle, $listnoun, $itemurlpara, $itemtitleurl, $terminalnode, $dbtable, $createtable, $insertarray, $insertprep, $dbgetdata

    $_SESSION['sessionnoun'] = $listnoun;

    try{
        //postgres for prod
        $db = new PDO($dsn);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //create a table
        $db->exec("CREATE TABLE IF NOT EXISTS $createtable");
        
        //new or edited item save
        if(isset($_POST['item-title-input']) && isset($_POST['item-desc-input'])){
                
            $itemtitle = $_POST['item-title-input'];
            $itemdesc = $_POST['item-desc-input'];
            $newitempos = $_POST['new-item-pos'];
            $edituid = $_POST['edit-item-uid'];

            // if new pos has value and edit uid is empty, add a NEW item to the db
            if(!empty($newitempos) && empty($edituid)){
                $insert = $db->prepare("INSERT INTO $insertprep");
                array_push($insertarray, $newitempos, $itemtitle, $itemdesc);
                $insert->execute($insertarray);
                $_SESSION['sessionalert'] = "itemcreated";
            }
            // if new pos is empty and edit uid has a value, UPDATE the item based on its UID
            elseif(!empty($edituid) && empty($newitempos)){
                $update = $db->prepare("UPDATE $dbtable SET title = :itemtitle, description = :itemdesc WHERE uid = $edituid");
                $update->bindParam(':itemtitle', $itemtitle, PDO::PARAM_STR);
                $update->bindParam(':itemdesc', $itemdesc, PDO::PARAM_STR);
                $update->execute();
                $_SESSION['sessionalert'] = "itemedited";
            }
            else{
                $statusMessage = "Error saving item";
                $statusType = "danger";
            }

            header("Location: ".$_SERVER['REQUEST_URI']);
            exit();

        }

        //delete item
        if(isset($_POST['delete-item-uid'])){
            
            //always delete the central item
            $deleteUID = $_POST['delete-item-uid'];
            $db->exec("DELETE FROM $dbtable WHERE uid = $deleteUID;");

            if(isset($_GET['pid']) && !isset($_GET['sid'])){
                // PID with NO SID means it's a section
                $db->exec("DELETE FROM content WHERE sid = $deleteUID;");
            }
            elseif(!isset($_GET['pid']) && !isset($_GET['sid'])){
                // NO PID with NO SID means it's a page
                $db->exec("DELETE FROM content WHERE pid = $deleteUID;");
                $db->exec("DELETE FROM sections WHERE pid = $deleteUID;");
            }

            $_SESSION['sessionalert'] = "itemdeleted";

            header("Location: ".$_SERVER['REQUEST_URI']);
            exit();

        }

        //generate content from query db
        $results = $db->query("SELECT * FROM $dbgetdata ORDER BY pos ASC");

        //determine page header and breadcrumb
        if(isset($_GET['pid'])){
            $thispg = $db->query("SELECT title FROM pages WHERE uid = $pid");
            foreach($thispg as $row){
                $thispgtitle = $row['title'];
            }
            if(isset($_GET['sid'])){
                $thissec = $db->query("SELECT title FROM sections WHERE uid = $sid");
                foreach($thissec as $row){
                    $thissectitle = $row['title'];
                }
                $pageheader = "$thissectitle &nbsp; <small>SECTION</small>";
                $breadcrumb = "<li><a href='".$baseurl."?mode=list&pid=$pid'>$thispgtitle</a></li> <li class='active'>$thissectitle</li>";
            }
            else{
                $pageheader = "$thispgtitle &nbsp; <small>PAGE</small>";
                $breadcrumb = "<li class='active'>$thispgtitle</li>";
            }
        }
        else{
            $pageheader = "PAGES";
        }

        //reordering save - called via ajax
        if(isset($_POST['moveuid']) && isset($_POST['movepos'])){
            $moveuid = $_POST['moveuid'];
            $movepos = $_POST['movepos'];

            $posupdate = $db->prepare("UPDATE $dbtable SET pos = :movepos WHERE uid = $moveuid");
            $posupdate->bindParam(':movepos', $movepos, PDO::PARAM_STR);
            $posupdate->execute();
        }

        // close the database connection
        $db = NULL;
    }
    catch(PDOException $e){
        $statusMessage = $e->getMessage();
        $statusType = "danger";
    }

    // remove alert variable
    unset($_SESSION['sessionalert']);
