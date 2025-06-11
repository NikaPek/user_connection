<?php

namespace App\Services;

class PasswordService
{
    function GetHashedPassword($password)
    {
        // Hash the password using bcrypt
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function GenerateAesKey()
    {
        // Generate a random 256-bit key for AES encryption
        return bin2hex(random_bytes(32)); // 32 bytes = 256 bits
    }

    function EncryptAes($plaintext, $password) {
        return $this->aes_encrypt($plaintext, $password);
    }

    function DecryptAes($ivHashCiphertext, $password) {
        return $this->aes_decrypt($ivHashCiphertext, $password);
    }

    function aes_encrypt(string $plainText, string $password): string|false
    {
        // Define the cipher method, hash algorithm, and key derivation iterations
        $cipherMethod = 'AES-256-CBC';
        $hashAlgo = 'sha256';
        $keyIterations = 9999; // A higher number makes it harder to brute-force the password

        // Generate a random salt for key derivation. This makes the key unique for each encryption.
        // Ensure the salt is sufficiently long and cryptographically secure.
        $salt = openssl_random_pseudo_bytes(16); // 16 bytes for a good salt

        // Generate a random Initialization Vector (IV).
        // The IV size is specific to the cipher method. openssl_cipher_iv_length() gets the correct size.
        $ivLength = openssl_cipher_iv_length($cipherMethod);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Derive a strong encryption key from the password and salt using PBKDF2.
        // This is crucial for security, making dictionary attacks and brute-force attacks much harder.
        $key = hash_pbkdf2($hashAlgo, $password, $salt, $keyIterations, 32, true); // 32 bytes (256 bits) for AES-256

        // Encrypt the plain text.
        // OPENSSL_RAW_DATA ensures raw binary output for $cipherText.
        // OPENSSL_ZERO_PADDING is generally NOT used with CBC as it requires PKCS7 padding.
        // However, openssl_encrypt with CBC mode usually handles PKCS7 padding by default when not using OPENSSL_RAW_DATA for the input.
        // If OPENSSL_RAW_DATA is used for output, you need to handle padding manually or ensure the input is already padded.
        // Here, we let openssl_encrypt handle the padding.
        $cipherText = openssl_encrypt($plainText, $cipherMethod, $key, OPENSSL_RAW_DATA, $iv);

        if ($cipherText === false) {
            error_log("AES encryption failed: " . openssl_error_string());
            return false;
        }

        // Combine IV, salt, and encrypted data.
        // Prepending IV and salt to the ciphertext allows them to be retrieved during decryption.
        // Use a delimiter if needed, but simple concatenation often works if lengths are known.
        // Here, we just concatenate and rely on knowing the lengths for splitting.
        $encoded = base64_encode($iv . $salt . $cipherText);

        return $encoded;
    }

    function aes_decrypt(string $encryptedText, string $password): string|false
    {
        // Define the same cipher method, hash algorithm, and key derivation iterations as used for encryption.
        $cipherMethod = 'AES-256-CBC';
        $hashAlgo = 'sha256';
        $keyIterations = 9999;

        // Decode the base64 string.
        $decoded = base64_decode($encryptedText);

        if ($decoded === false) {
            error_log("Base64 decoding failed.");
            return false;
        }

        // Determine the lengths of IV and salt to correctly split the decoded string.
        $ivLength = openssl_cipher_iv_length($cipherMethod);
        $saltLength = 16; // The same salt length used during encryption

        // Check if the decoded string is long enough to contain IV, salt, and at least some ciphertext.
        if (strlen($decoded) < ($ivLength + $saltLength)) {
            error_log("Decoded string too short to contain IV and salt.");
            return false;
        }

        // Extract IV, salt, and ciphertext from the decoded string.
        $iv = substr($decoded, 0, $ivLength);
        $salt = substr($decoded, $ivLength, $saltLength);
        $cipherText = substr($decoded, $ivLength + $saltLength);

        // Derive the decryption key using the same password and extracted salt.
        $key = hash_pbkdf2($hashAlgo, $password, $salt, $keyIterations, 32, true);

        // Decrypt the ciphertext.
        $plainText = openssl_decrypt($cipherText, $cipherMethod, $key, OPENSSL_RAW_DATA, $iv);

        if ($plainText === false) {
            // This could mean wrong password, corrupted data, or invalid padding.
            error_log("AES decryption failed: " . openssl_error_string());
            return false;
        }

        return $plainText;
    }
}