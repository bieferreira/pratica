<?php
function create_jwt($payload, $secret = JWT_SECRET) {
    $header = base64_encode(json_encode(['typ'=>'JWT','alg'=>'HS256']));
    $payload = base64_encode(json_encode($payload));
    $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
    return "$header.$payload." . base64_encode($signature);
}

function validate_jwt($token, $secret = JWT_SECRET) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    [$header, $payload, $signature] = $parts;
    $validSig = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
    if (!hash_equals($validSig, $signature)) return false;
    return json_decode(base64_decode($payload), true);
}
