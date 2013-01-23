<?php
namespace Allty;

class JSONCom {
    
    /**
     * Decode a JSON string containing comments.
     * 
     * Comments can be of the following type :
     *  - One-line comment starting with '#' 
     *  - One-line comment starting with ';'
     *  - One-line comment starting with '//'
     *  - Multi-line comment between '/*' and '* /'
     * 
     * @param string $string The json string being decoded. 
     * This function only works with UTF-8 encoded data.
     * 
     * @param bool $assoc When TRUE, returned objects will be converted 
     * into associative arrays.
     * 
     * @param integer $depth User specified recursion depth.
     * @param type $options Bitmask of JSON decode options. 
     * Currently only JSON_BIGINT_AS_STRING is supported 
     * (default is to cast large integers as floats)
     * 
     * @return mixed Returns the value encoded in json in appropriate PHP type. 
     * Values true, false and null (case-insensitive) are returned as TRUE, 
     * FALSE and NULL respectively. NULL is returned if the json cannot be 
     * decoded or if the encoded data is deeper than the recursion limit.
     * 
     * @throws JSONComInvalidArgumentException
     * @static
     */
    public static function decode($string, $assoc = false, $depth = 512, $options = 0) {
        
        if(!\is_string($string)) {
            throw new JSONComInvalidArgumentException('Parameter $str must be a string. "'.  \gettype($string).'" given.');
        }
        
        $json = self::removeComments($string);
        
        // PHP > 5.4.0 supports the 4th parameter ($options)
        if(\version_compare(\PHP_VERSION, '5.4.0', '>=')) {
            return \json_decode($json, $assoc, $depth, $options);
        }
        
        // PHP < 5.4.0 does not support the 4th parameter
        return \json_decode($json, $assoc, $depth);
    }
    
    /**
     * Remove different kinds of comments in a string
     * 
     * @param string $string String to remove comments from.
     * @return string Returns the comments-stripped string.
     * @private
     */
    protected static function removeComments($string)
    {
        if(!\is_string($string) || empty($string)) {
            return '';
        }
        return preg_replace("%/\*(?:(?!\*/).)*\*/%s", '', 
                    preg_replace("%(#|;|(//)).*%", '', $string)); 
    }

}

class JSONComInvalidArgumentException extends \InvalidArgumentException{}
