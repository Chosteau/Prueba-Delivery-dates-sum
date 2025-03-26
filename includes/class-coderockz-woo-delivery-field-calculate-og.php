<?php
class Coderockz_Woo_Delivery_Field_Calculate {
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';
    const PARENTHESIS_DEPTH = 10;
    public function calculate($input){
        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = str_replace(' ', '', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);
            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);
                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }
            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                return $this->compute($match[0]);
            }
            // To handle the special case of expressions surrounded by global parenthesis like "(1+1)"
            if(is_numeric($input)){
                return $input;
            }
            return 0;
        }
        return $input;
    }

    private function create_function( $arg, $body ) { 
        static $cache = array(); 
        static $max_cache_size = 64;
        static $sorter;
        
        if ( $sorter === NULL ) {
            $sorter = function( $a, $b ) {
                if ( $a->hits == $b->hits ) {
                    return 0;
                }
        
                return ($a->hits < $b->hits) ? 1 : -1;
            };
        }
        
        $crc = crc32($arg . "\\x00" . $body);
        
        if (isset($cache[$crc])) {
            ++$cache[$crc][1];
            return $cache[$crc][0];
        }
        
        if ( sizeof($cache) >= $max_cache_size ) {
            uasort($cache, $sorter);
            array_pop($cache);
        }
        
        $cache[$crc] = array( $cb = eval('return function('.$arg.'){'.$body.'};'), 0 );
        return $cb; 
    }

    private function compute($input){
        $compute = $this->create_function('', 'return '.$input.';');
        return 0 + $compute();
    }
    private function callback($input){
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
            return $this->compute($match[0]);
        }
        return 0;
    }
}