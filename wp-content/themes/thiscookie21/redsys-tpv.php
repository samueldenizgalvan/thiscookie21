<?php
/**
 * Generación de formulario para TPV BBVA/Redsys
 */

class RedsysTPV {
    private $merchantCode = '368734406';
    private $terminal = '001';
    private $secretKey = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
    private $urlOK;
    private $urlKO;
    private $urlNotification;
    private $currentOrderId;
    
    // URLs del entorno
    private $testURL = 'https://sis-t.redsys.es:25443/sis/realizarPago';
    private $productionURL = 'https://sis.redsys.es/sis/realizarPago';
    
    public function __construct($isTest = false) {
        $baseURL = get_site_url();
        $themeURL = get_template_directory_uri();
        $this->urlOK = $themeURL . '/tpv-success.php';
        $this->urlKO = $themeURL . '/tpv-success.php'; // También redirigir errores a success para debug
        $this->urlNotification = $baseURL . '/wp-content/themes/thiscookie21/tpv-notification.php';
        $this->isTest = $isTest;
    }
    
    public function setOrderId($orderId) {
        $this->currentOrderId = $orderId;
    }
    
    public function generatePaymentForm($orderId, $amount, $currency = '978') {
        // Guardar el ID completo del pedido para pasarlo en la URL
        $this->setOrderId($orderId);
        
        // Generar número de pedido único (debe ser numérico, 4-12 caracteres)
        // Formato: YYMMDDHHMMSS (12 dígitos)
        $orderNumber = date('ymdHis');
        
        // Asegurarse de que el número de pedido tenga exactamente 12 caracteres
        $orderNumber = str_pad($orderNumber, 12, '0', STR_PAD_LEFT);
        
        // Convertir el importe a céntimos (sin decimales, como STRING)
        $amountInCents = strval(intval(round($amount * 100)));
        
        // Crear parámetros del comercio (TODOS DEBEN SER STRINGS)
        $merchantParameters = array(
            'DS_MERCHANT_AMOUNT' => $amountInCents,
            'DS_MERCHANT_ORDER' => $orderNumber,
            'DS_MERCHANT_MERCHANTCODE' => $this->merchantCode,
            'DS_MERCHANT_CURRENCY' => $currency,
            'DS_MERCHANT_TRANSACTIONTYPE' => '0',
            'DS_MERCHANT_TERMINAL' => $this->terminal,
            'DS_MERCHANT_MERCHANTURL' => $this->urlNotification,
            'DS_MERCHANT_URLOK' => $this->urlOK . '?order=' . urlencode($this->currentOrderId),
            'DS_MERCHANT_URLKO' => $this->urlKO,
            'DS_MERCHANT_PRODUCTDESCRIPTION' => 'Pedido ThisCookie21',
        );
        
        // Codificar parámetros en base64
        $merchantParamsBase64 = base64_encode(json_encode($merchantParameters));
        
        // Generar firma
        $signature = $this->generateSignature($orderNumber, $merchantParamsBase64);
        
        // URL del TPV (test o producción)
        $tpvURL = $this->isTest ? $this->testURL : $this->productionURL;
        
        return array(
            'url' => $tpvURL,
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => $merchantParamsBase64,
            'Ds_Signature' => $signature
        );
    }
    
    private function generateSignature($orderNumber, $merchantParamsBase64) {
        // Decodificar la clave secreta
        $key = base64_decode($this->secretKey);
        
        // Cifrar el número de pedido con 3DES
        $cipher = 'des-ede3-cbc';
        $iv = "\0\0\0\0\0\0\0\0";
        
        // El número de pedido debe tener padding si es necesario
        $orderPadded = $orderNumber;
        $blockSize = 8;
        $paddingLength = $blockSize - (strlen($orderPadded) % $blockSize);
        if ($paddingLength < $blockSize) {
            $orderPadded .= str_repeat("\0", $paddingLength);
        }
        
        // Generar clave derivada
        $derivedKey = openssl_encrypt($orderPadded, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
        
        // Generar firma HMAC SHA256
        $signature = hash_hmac('sha256', $merchantParamsBase64, $derivedKey, true);
        
        // Codificar en base64
        return base64_encode($signature);
    }
    
    public function verifySignature($merchantParams, $signature) {
        $decodedParams = json_decode(base64_decode($merchantParams), true);
        $orderNumber = $decodedParams['Ds_Order'];
        
        $calculatedSignature = $this->generateSignature($orderNumber, $merchantParams);
        
        return $signature === $calculatedSignature;
    }
}
