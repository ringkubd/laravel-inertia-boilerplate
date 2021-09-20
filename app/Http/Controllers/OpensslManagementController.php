<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpensslManagementController extends Controller
{
    public function generate(){
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 8192 * 100,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        $initialize = openssl_pkey_new($config);
        openssl_pkey_export($initialize, $privKey);
        $publicKey = openssl_pkey_get_details($initialize);

        $privateKeyFile = fopen('private.pem', 'w');
        $publicKeyFile = fopen('public.pem', 'w');
        fwrite($privateKeyFile,$privKey);
        fwrite($publicKeyFile,$publicKey['key']);
        dd($privKey, $publicKey['key']);
    }

    public function publicEncrypt(){
        $file = "Hi, Please encrypt me.";
        $file = file_get_contents('docx.html');
        $publicKey = file_get_contents('public.pem');
        $encryptedData = "";
        try {
            $encrypt = openssl_public_encrypt($file, $encryptedData, $publicKey);
        }catch (Exception $e){
            dd($e->getMessage());
        }
        $encryptedFile = fopen('file-sample.docx.enc', 'w');
        fwrite($encryptedFile,$encryptedData);
        fclose($encryptedFile);
        dd($encryptedData);
    }

    public function privateDecrypt(){
        $file = file_get_contents('file-sample.docx.enc');
        $privateKey = file_get_contents('private.pem');
        $decryptedData = '';
        try {
            $encrypt = openssl_private_decrypt($file, $decryptedData, $privateKey);
            $encryptedFile = fopen('file-sample.txt', 'w');
            fwrite($encryptedFile,$decryptedData);
            fclose($encryptedFile);
        }catch (Exception $e){
            dd($e->getMessage());
        }
    }
}
