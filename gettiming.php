<?php

$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
//*********************************************************************************************************************
// V1.0 : Script qui fournit le temps passé en mn et j/h/mn dans la dernière valeur des états de la liste définie
// V1.1 : Version sans appel API
// V1.2 : Récupère les accents
//*************************************** Tableau des états *******************************************************
// recuperation des ID depuis la requete
$periphs = getArg ( "periphIds", $mandatory = true, $default = '' );
$tabPeriphs = explode ( ",", $periphs );
//**********************************************************************************************************************
// recuperation des details des peripheriques
foreach ( $tabPeriphs as $periphId ) {
    $arrValue = getValue ( $periphId, true );
    $lastValue = utf8_decode ( $arrValue["value"] );
    $lastValueText = $arrValue["value_text"];
    $lastValueChange = $arrValue["change"];
    if ( $lastValueChange == '' ) {
        die ( "## ERROR: Empty result" );
    }
    $tabetats[] = array ("API" => $periphId, "ETAT" => $lastValue, "TEXT" => $lastValueText, "CHANGE" => $lastValueChange);
}

/* * ******************************************************************************************************************** */

$xml .= "<ETATS>";
foreach ( $tabetats as $etats ) {
    list($an, $mo, $jo, $he, $mi, $se) = sscanf ( $etats["CHANGE"], "%d-%d-%d %d:%d:%d" );
    $timestamp = mktime ( $he, $mi, $se, $mo, $jo, $an );
    $difference = time () - $timestamp;
    $onlymn = floor ( $difference / 60 );
    $jour = floor ( $difference / 86400 );
    $reste1 = ($difference % 86400);
    $heure = floor ( $reste1 / 3600 );
    $reste2 = ($reste1 % 3600);
    $minute = floor ( $reste2 / 60 );
    $xml .= "<ETAT_" . $etats["API"] . ">";
    $xml .= "<MINUTES>" . $onlymn . "</MINUTES><TIMING>";
    $timing = "";
    if ( $jour > 1 ) {
        $timing .= $jour . " jours, ";
    } else if ( $jour == 1 ) {
        $timing .= $jour . " jour, ";
    }
    if ( $heure > 1 ) {
        $timing .= $heure . " heures, ";
    } else if ( $heure == 1 ) {
        $timing .= $heure . " heure, ";
    }
    if ( $minute > 1 ) {
        $timing .= $minute . " minutes";
    } else if ( $minute == 1 ) {
        $timing .= $minute . " minute";
    }
    if ( $timing == "" ) {
        $timing = "moins d'une minute";
    }
    $xml .= $timing . "</TIMING>";
    $xml .= "<MESSAGE>" . $etats["TEXT"] . " depuis " . $timing . "</MESSAGE>";
    $xml .= "</ETAT_" . $etats["API"] . ">";
}

$xml .= "</ETATS>";
sdk_header ( 'text/xml' );
echo $xml;
?>
