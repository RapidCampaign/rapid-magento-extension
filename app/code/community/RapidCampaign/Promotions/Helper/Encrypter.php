<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Helper_Encrypter extends Mage_Core_Helper_Abstract
{
    const IV_SIZE = 16;

    protected $key = '1234567890123456';
    protected $cipher = 'AES-128-CBC';

    /**
     * Set encryption key
     *
     * @param string $key
     * @return RapidCampaign_Promotions_Helper_Encrypter
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Set cipher
     *
     * @param string $cipher
     * @return RapidCampaign_Promotions_Helper_Encrypter
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
        return $this;
    }

    /**
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function encrypt($value)
    {
        $iv    = openssl_random_pseudo_bytes(self::IV_SIZE);
        $value = openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);

        if ($value === false) {
            throw new Exception('Could not encrypt the data.');
        }

        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);

        return base64_encode(json_encode(compact('iv', 'value', 'mac')));
    }

    /**
     * @param string $payload
     * @return mixed
     * @throws Exception
     */
    public function decrypt($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (!$payload || $this->invalidPayload($payload)) {
            throw new Exception('The payload is invalid.');
        }

        if (!$this->validMac($payload)) {
            throw new Exception('The MAC is invalid.');
        }

        $iv = base64_decode($payload['iv']);

        $decrypted = openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new Exception('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  string  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  array|mixed  $data
     * @return bool
     */
    protected function invalidPayload($data)
    {
        return !is_array($data) || !isset($data['iv']) || !isset($data['value']) || !isset($data['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     *
     * @throws \RuntimeException
     */
    protected function validMac(array $payload)
    {
        $bytes = self::randomBytes(16);

        $calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);

        return self::equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
    }

    /**
     * Generate a more truly "random" bytes.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function randomBytes($length = 16)
    {
        if (PHP_MAJOR_VERSION >= 7) {
            $bytes = random_bytes($length);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);

            if ($bytes === false || $strong === false) {
                throw new RuntimeException('Unable to generate random string.');
            }
        } else {
            throw new RuntimeException('OpenSSL extension is required for PHP 5 users.');
        }

        return $bytes;
    }

    /**
     * Compares two strings using a constant-time algorithm.
     *
     * Note: This method will leak length information.
     *
     * Note: Adapted from Symfony\Component\Security\Core\Util\StringUtils.
     *
     * @param  string  $knownString
     * @param  string  $userInput
     * @return bool
     */
    protected function equals($knownString, $userInput)
    {
        if (!is_string($knownString)) {
            $knownString = (string) $knownString;
        }

        if (!is_string($userInput)) {
            $userInput = (string) $userInput;
        }

        if (function_exists('hash_equals')) {
            return hash_equals($knownString, $userInput);
        }

        $knownLength = mb_strlen($knownString);

        if (mb_strlen($userInput) !== $knownLength) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < $knownLength; ++$i) {
            $result |= (ord($knownString[$i]) ^ ord($userInput[$i]));
        }

        return 0 === $result;
    }
}
