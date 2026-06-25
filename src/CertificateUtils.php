<?php

namespace Gigya\PHP;

use Exception;

/**
 * Utility class for handling client certificates in mTLS authentication
 */
class CertificateUtils
{
    /**
     * Resolves a certificate input to a file path
     * 
     * @param string $input File path or PEM content string
     * @param string $type Type identifier for temp file naming (e.g., 'cert' or 'key')
     * @return string Path to the certificate file
     * @throws Exception if the input is invalid
     */
    public static function resolveCertificatePath($input, $type = 'cert')
    {
        if (file_exists($input)) {
            return $input;
        }
        
        if (self::isPemContent($input)) {
            return self::createTempFile($input, $type);
        }
        
        throw new Exception("Client {$type} file not found: " . $input);
    }
    
    /**
     * Checks if the input string is PEM content
     * 
     * @param string $content The content to check
     * @return bool True if the content appears to be PEM format
     */
    private static function isPemContent($content)
    {
        return strpos($content, '-----BEGIN') !== false;
    }
    
    /**
     * Creates a temporary file with the given content
     * 
     * @param string $content The content to write to the temp file
     * @param string $type Type identifier for temp file naming
     * @return string Path to the temporary file
     * @throws Exception if file creation fails
     */
    private static function createTempFile($content, $type)
    {
        $tempFile = tempnam(sys_get_temp_dir(), "gigya_{$type}_");
        if ($tempFile === false) {
            throw new Exception("Failed to create temporary file for {$type}");
        }
        
        if (file_put_contents($tempFile, $content) === false) {
            throw new Exception("Failed to write {$type} content to temporary file");
        }
        
        return $tempFile;
    }
}
