<?php
session_start();
require 'connect.php';

if (isset($_SESSION['id']) AND isset($_SESSION['pseudo'])){
    $req = $bdd->prepare("UPDATE `mycave` SET `name`=?,`year`=?,`grapes`=?,`country`=?,`region`=?,`description`=?,`picture`=? WHERE id=?");
    $req->execute(array($_POST['name'],$_POST['year'],$_POST['grapes'],$_POST['country'],$_POST['region'],$_POST['description'],$_POST['picture'],$_POST['id']));
    $msg='Enregistrement modifié';
    header('Location: ../index.php?msg='.$msg);
}
header('Location: ../index.php?page='.$_SESSION['page']);