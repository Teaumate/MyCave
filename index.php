<?php
session_start();

require 'php/connect.php';
require("libs/Smarty.class.php");

define('MAIN_PATH', getcwd());

$req = $bdd->query("SELECT MIN(id) AS First, MAX(id) AS Last FROM mycave"); // determine 1ere et derniere bouteille de mycave
$donnees = $req->fetch();
$first=$donnees[0];                   // 1er enregistrement
$last=$donnees[1];                    // dernier enregistrement

$page = isset($_GET['page']) ? ($_GET['page']) : 0;            // quelle page afficher
$bottle = isset($_GET['bottle']) ? ($_GET['bottle']) : $first; // ou quelle bouteille si smartphone
$nb_elt = 10;                                                   // nb enregistrements par pages

if(!(isset($_GET['direction']))){       // si grand écran
  $req = $bdd->query("SELECT * FROM mycave ORDER BY id LIMIT ". $page*$nb_elt ."," . $nb_elt);
  $elements=array();
  while ($donnees = $req->fetch()) {
    $elements[]=$donnees;              // tableau de nb_elt bouteilles
  }
}elseif($_GET['direction']=='left'){  // si smartphone
  if($bottle !== $first){
    $req = $bdd->query("SELECT * FROM mycave WHERE id < ". $bottle ." ORDER BY id DESC LIMIT 1");
  }else{
    $req = $bdd->query("SELECT * FROM mycave WHERE id = ". $first);
  }
  $donnees = $req->fetch();
  $bottle = $donnees[0];
  $elements[]=$donnees;
  $_GET['direction']=NULL;
}else{
  if($bottle !== $last){
    $req = $bdd->query("SELECT * FROM mycave WHERE id > ". $bottle ." ORDER BY id LIMIT 1");
  }else{
    $req = $bdd->query("SELECT * FROM mycave WHERE id = ". $last);
  }
  $donnees = $req->fetch();
  $bottle = $donnees[0];
  $elements[]=$donnees;
  $_GET['direction']=NULL;
}
$_SESSION['page'] = $page;    // page en cours pour retour de update, delete ...
$req = $bdd->query("SELECT COUNT(*) AS nb_rec FROM mycave"); // calcul nb enregistrements
$donnees = $req->fetch();
$nb_pages = ceil($donnees[0]/$nb_elt);      // calcul nb pages

$smarty = new Smarty();                 // nouvel objet smarty et recup des variable php dans smarty
$smarty->setTemplateDir('./template');
$smarty->assign('nb_rec',$donnees[0]);      // nombre de lignes dans mycave
$smarty->assign('nb_pages',$nb_pages);      // nombre de pages
$smarty->assign('page',$page);              // page courrente
$smarty->assign('elts',$elements);          // les enregistrements de mycave
$smarty->assign('bottle',$bottle);          // bouteille en cours
$smarty->assign('session',$_SESSION);

$smarty->display('index.tpl');              // appelle la page principale

?>