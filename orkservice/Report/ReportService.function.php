<?php

function GetActiveKingdomsSummary($request) {
	$R = new Report();
	return $R->GetActiveKingdomsSummary($request);
}

function GetActivePlayers($request) {
	$R = new Report();
	return $R->GetActivePlayers($request);
}

function GetKingdomParkAverages($request) {
	$R = new Report();
	return $R->GetKingdomParkAverages($request);
}

function GetPlayerRoster($request) {
	$R = new Report();
	return $R->GetPlayerRoster($request);
}

?>