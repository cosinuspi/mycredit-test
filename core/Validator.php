<?php

namespace core;

class Validator
{
    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public static function email($value)
    {
        if (!is_string($value)) {
            $valid = false;
        } elseif (!preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches)) {
            $valid = false;
        } elseif (strlen($matches['local']) > 64) {
            // The maximum total length of a user name or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
            // http://tools.ietf.org/html/rfc5321#section-4.5.3.1.1
            $valid = false;
        } elseif (strlen($matches['local'] . '@' . $matches['domain']) > 254) {
            // There is a restriction in RFC 2821 on the length of an address in MAIL and RCPT commands
            // of 254 characters. Since addresses that do not fit in those fields are not normally useful, the
            // upper limit on address lengths should normally be considered to be 254.
            //
            // Dominic Sayers, RFC 3696 erratum 1690
            // http://www.rfc-editor.org/errata_search.php?eid=1690
            $valid = false;
        } else {
            $valid = preg_match('/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/', $value);
        }
        
        return $valid ? null : 'Некорректный email!';
    }
    
    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public static function required($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        
        if ($value === null || $value === [] || $value === '') {
            return 'Обязательное поле!';
        }
    }
    
    /**
     * @param mixed $value
     * @param array $rules
     *
     * @return string|null
     */
    public static function string($value, $rules)
    {
        if (!is_string($value)) {
            return 'Поле должно быть строкой.';
        }
        
        $length = mb_strlen($value, 'UTF-8');
        
        if (isset($rules['max'])) {
            $length = mb_strlen($value, 'UTF-8');
            
            if ($length > $rules['max']) {
                return sprintf('Длина поля не должна превышать %s символов', $rules['max']);
            }
        }
    }
}
