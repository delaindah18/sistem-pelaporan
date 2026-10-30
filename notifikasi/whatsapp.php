<?php
function kirimWhatsApp($target, $message) {
    $token = FONNTE_TOKEN;
    if (!$token || $token == 'token_fonnte_anda') {
        error_log("Fonnte token belum diset");
        return false;
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $target,
            'message' => $message,
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $token,
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>