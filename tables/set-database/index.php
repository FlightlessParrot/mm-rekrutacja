<?php
require_once '../support/DatabaseHelper.php';

?>

<html>
    <body>
        <a href="../">Wróć</a>
        <?php
        try{

        
        $dbHelper= new DatabaseHelper('db','user','password','database');
        echo "<p>";    
        echo $dbHelper->cleanDatabase();
        echo "</p>";
        echo "<p>";    
        echo $dbHelper->createTables();
        echo "</p>";

        echo "<p>";
        echo $dbHelper->insertSampleData();
        echo "</p>";

        }catch(PDOException $e){
            echo "<p>Connection failed: " . $e->getMessage()."</p>";
        }
        ?>
    </body>
</html>