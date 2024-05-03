<?php
namespace App\Service;

use BaconQrCode\Encoder\QrCode;
use OTPHP\TOTP;
 class TwoFactorAuthenticator
 {
     public function generateSecret(): string
     {
         return TOTP::create()->getSecret();
     }
     public function getQrCode(string $secret, string $accountName, string $issuer): string
     {
       $otp= TOTP::createFromSecret($secret);
         $otp->setLabel($issuer);
        //$label = sprintf('%s:%s', $issuer, $accountName);
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );
        return $grCodeUri;
    
        //  $provisiongUri=$otp->getProvisioningUri($issuer);
        //  $qrCode=QrCod
        //  return new QrCodeResons($qrCode);
         

     }
     public function validateOTPCode(string $secret, string $code): bool
     {
         return TOTP::create($secret)->verify($code);
     }
     public function generateOTP(string $secret): string
     {
         return TOTP::create($secret)->now();
     }
     
 }
 ?>