<?php

trait Filter
{   

    /**
     * Replace a specific key in an array with a given string.
     *
     * @param array $array The array to modify.
     * @param string $key The key to replace.
     * @param string $replacement The string to replace the key with.
     * @return array The modified array.
     */
    private function replaceKeyWithString(array $array, string $key, string $replacement): array
    {
        if (array_key_exists($key, $array)) {
            $array[$replacement] = $array[$key];
            unset($array[$key]);
        }
        return $array;
    }
    /** 
     * Get and sanitize values from $_GET array based on provided keys.
     *
     * @param array $keys Array of keys to retrieve from $_GET.
     * @return array Sanitized key-value array.
     */
    private function getSanitizedParams(array $keys): array
    {
        $sanitizedParams = [];
        foreach ($keys as $key) {
            if (isset($_GET[$key]) && !empty($_GET[$key])) {
                $sanitizedParams[$key] = htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        return $sanitizedParams;
    }
    /**
     * Generate SQL WHERE clause based on key-value parameters.
     *
     * @param array $params Key-value parameters for filtering.
     * @return string SQL WHERE clause.
     */
    private function generateWhereClause(array $params): string
    {
        if (empty($params)) {
            return '';
        }

        $conditions = [];
        foreach ($params as $key => $value) {
            if (is_null($value)) {
                $conditions[] = "$key IS NULL";
            } else {
                $conditions[] = "$key = $value";
            }
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }



    private function checkIfClauseAlreadyExists(string $clause,$sql):bool
    {
        $clause=str_replace(['AND','OR','WHERE','LIKE'],'',$clause);
        if(str_contains($sql,trim($clause)))
            {
                return true;
            }
        return false;
    }
}