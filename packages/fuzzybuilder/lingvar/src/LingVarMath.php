<?php

namespace Fuzzybuilder\Lingvar;

class LingVarMath
{
    protected function triangle($x, $a, $b, $c)
    {
        return max(min(($x-$a)/($b-$a),($c-$x)/($c-$b)),0);
    }

    public static function trapezoid($x, $a, $b, $c, $d)
    {
        return max(min(($x-$a)/($b-$a),1,($d-$x)/($d-$c)),0);
    } 

    protected function gaussian($x, $c, $o)
    {
        return exp(-0.5*pow($x-$c/$o,2));
    } 

    protected function bell($x, $a, $b, $c)
    {
        return 1/(1+pow(abs($x-$c/$a), 2*$b));
    }
    
    protected function sig($x, $a, $c)
    {
        return 1/(1+exp(-$a*($x-$c)));
    }

}