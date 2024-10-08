<?php

require_once 'Base32.php';

/**
 * PHP Class for handling Time-based one-time password.
 */
class Totp
{
    protected $_codeLength = 6;
    protected $base32;

    public function __construct()
    {
        $this->base32 = new Base32();
    }

    /**
     * Create new secret.
     * 16 characters, randomly chosen from the allowed base32 characters.
     *
     * @param int $secretLength
     *
     * @return string
     */
    public function createSecret(int $secretLength = 16): string
    {
        $validChars = $this->base32->_getBase32LookupTable();

        if ($secretLength < 16 || $secretLength > 128) {
            throw new \InvalidArgumentException('Bad secret length');
        }

        $secret = '';
        $rnd = random_bytes($secretLength);

        if ($rnd !== false) {
            for ($i = 0; $i < $secretLength; ++$i) {
                $secret .= $validChars[ord($rnd[$i]) & 31];
            }
        } else {
            throw new \RuntimeException('No source of secure random bytes');
        }

        return $secret;
    }

    /**
     * Calculate the code, with given secret and point in time.
     *
     * @param string $secret
     * @param int|null $timeSlice
     *
     * @return string
     */
    public function getCode(string $secret, ?int $timeSlice = null): string
    {
        $timeSlice ??= floor(time() / 30);

        $secretKey = $this->base32->_base32Decode($secret);
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

        $hm = hash_hmac('SHA1', $time, $secretKey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashPart = substr($hm, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = 10 ** $this->_codeLength;

        return str_pad($value % $modulo, $this->_codeLength, '0', STR_PAD_LEFT);
    }

    /**
     * Check if the code is correct. This will accept codes starting from $discrepancy*30sec ago to $discrepancy*30sec from now.
     *
     * @param string   $secret
     * @param string   $code
     * @param int      $discrepancy      This is the allowed time drift in 30 second units (8 means 4 minutes before or after)
     *
     * @return bool
     */
    public function verifyCode($secret, $code, $discrepancy = 1)
    {
        if (strlen($code) != 6) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; ++$i) {
            $calculatedCode = $this->getCode($secret, floor(time() / 30) + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A timing safe equals comparison
     * more info here: http://blog.ircmaxell.com/2014/11/its-all-about-time.html.
     *
     * @param string $safeString The internal (safe) value to be checked
     * @param string $userString The user submitted (unsafe) value
     *
     * @return bool True if the two strings are identical
     */
    private function timingSafeEquals($safeString, $userString)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($safeString, $userString);
        }
        $safeLen = strlen($safeString);
        $userLen = strlen($userString);

        if ($userLen != $safeLen) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < $userLen; ++$i) {
            $result |= (ord($safeString[$i]) ^ ord($userString[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }
}
