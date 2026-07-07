<?php

header('Content-Type: application/json');

$apiKey = 'e74b011e8a5aae41:0e255bf4df0cb643eed429b421dbf1ad'; 
$playlistId = 25;


$data = json_decode(file_get_contents('php://input'), true);
if (!$data) die(json_encode(['erreur' => 'Aucune donnée']));

// 1. Envoi du fichier à AzuraCast
$ch1 = curl_init("https://radio.askipradiotv.com/api/station/1/files?api_key=" . $apiKey);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_POST, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode(['path' => $data['path'], 'file' => $data['file']]));
$res1 = curl_exec($ch1);
if (curl_getinfo($ch1, CURLINFO_HTTP_CODE) !== 200) die(json_encode(['erreur' => 'Échec upload']));

// 2. Ajout à la playlist
$ch2 = curl_init("https://radio.askipradiotv.com/api/station/1/files/batch?api_key=" . $apiKey);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
    'do' => 'playlist',
    'playlists' => [$playlistId],
    'files' => [$data['path']]
]));
$res2 = curl_exec($ch2);
if (curl_getinfo($ch2, CURLINFO_HTTP_CODE) !== 200) die(json_encode(['erreur' => 'Échec playlist']));

// Tout est bon
echo json_encode(['succes' => true]);
?>