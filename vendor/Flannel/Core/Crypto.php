<?php

namespace Flannel\Core;

use \Mdanter\Ecc\EccFactory;
use \Mdanter\Ecc\Math\GmpMathInterface;
use \Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use \Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;

class Crypto {

    const USE_DERANDOMIZED_SIGNATURES = true;

    protected $_adapter = null;
    protected $_generator = null;
    
    protected $_pemPrivate = null;
    protected $_pemPublic = null;

    public function __construct() {
        $this->_adapter = \Mdanter\Ecc\EccFactory::getAdapter();
        $this->_generator = \Mdanter\Ecc\EccFactory::getNistCurves()->generator384();

        $this->_pemPrivate = new PemPrivateKeySerializer(new DerPrivateKeySerializer());
        $this->_pemPublic = new PemPublicKeySerializer(new DerPublicKeySerializer());
        
        return $this;
    }

    public function generateSharedKey() {
        $privateKey = $this->_pemPrivate->parse(file_get_contents(''));
        $publicKey = $this->_pemPublic->parse(file_get_contents(''));

        $exchange = $privateKey->createExchange($publicKey);
        $shared = $exchange->calculateSharedKey();

        # The shared key is never used directly, but used with a key derivation function (KDF)
        $kdf = function (GmpMathInterface $math, \GMP $sharedSecret) {
            $binary = $math->intToString($sharedSecret);
            $hash = hash('sha256', $binary, true);
            return $hash;
        };

        $key = $kdf($this->_adapter, $shared);
        
        return unpack("H*", $kdf($this->_adapter, $shared))[1];
    }

    public function encrypt($value) {
        $binKey = hex2bin(\Flannel\Core\Config::get('crypt.key'));

        if (mb_strlen($binKey, '8bit') !== 32) {
            return '';
        }

        $ivSize = openssl_cipher_iv_length(\Flannel\Core\Config::get('crypt.algorithm'));
        $iv = openssl_random_pseudo_bytes($ivSize);

        $cipherText = openssl_encrypt(
            $value,
            \Flannel\Core\Config::get('crypt.algorithm'),
            $binKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        return bin2hex($iv . $cipherText);
    }

    public function decrypt($value) {
        $binKey = hex2bin(\Flannel\Core\Config::get('crypt.key'));

        if (mb_strlen($binKey, '8bit') !== 32) {
            return '';
        }

        $encryptedStr = hex2bin($value);

        $ivSize = openssl_cipher_iv_length(\Flannel\Core\Config::get('crypt.algorithm'));
        $iv = mb_substr($encryptedStr, 0, $ivSize, '8bit');

        $cipherText = mb_substr($encryptedStr, $ivSize, null, '8bit');

        $clearText =  openssl_decrypt(
            $cipherText,
            \Flannel\Core\Config::get('crypt.algorithm'),
            $binKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $clearText;
    }
}
