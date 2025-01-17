<?php
require_once 'support/MysqlManager.php';
require_once 'support/ActionExecuter.php';
$pdo = new PDO('mysql:host=localhost;dbname=refactoring', 'root', 'root');
$mysqlManager = new MysqlManager($pdo);
$actionExecuer = new ActionExecuter($mysqlManager, $_GET);
$actionExecuer->renderTable($dg_bgcolor);
