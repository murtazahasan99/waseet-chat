<?php


if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input to prevent XSS and limit input size to mitigate DDoS attacks.
     *
     * @param string|array $data Input data to sanitize
     * @param int $max_length Maximum allowed length of input to prevent large payload attacks
     * @return string|array Cleaned and sanitized data
     */
    function sanitize_input($data, $max_length = 10000)
    {
        // Handle array input
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = sanitize_input($value, $max_length);
            }
            return $data;
        }

        $data = strip_tags($data);
        $data = addslashes($data);
        $data = esc($data, 'html');
       
        if (strlen($data) > $max_length) {
            $data = substr($data, 0, $max_length);
        }
        
        $data = trim($data);

        return $data;
    }
}
