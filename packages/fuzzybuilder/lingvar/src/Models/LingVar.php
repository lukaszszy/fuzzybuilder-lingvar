<?php

namespace Fuzzybuilder\Lingvar\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Exception;

class LingVar extends Builder
{
    private $name;
    private $term;
    private $function;
    private $sort;

    public function __construct($n, $t, $s){
        $this->name = $n;
        $this->term = $t;
        $this->sort = $s;
        $this->function = self::findFuzzyFunction($n, $t);
    }

    protected function loadLingvarCollection()
    {
        try {
            $file = env('FUZZY_LINGVAR_PATH').env('FUZZY_LINGVAR_FILENAME');
            return new Collection(json_decode(file_get_contents($file), true));
        }
        catch (Exception $e) {
            throw new Exception("Can't load data from source file.");
        }
    }

    protected function findFuzzyFunction($name, $term)
    {
        $function = self::loadLingvarCollection()->where('name', $name)
        ->flatten(2)->where('term', $term)
        ->pluck('function')->get(0);

        if (is_null($function) or empty($function)) {
            throw new Exception("Can't find function atribute.");
        }
        return $function;
    }
    
    protected function addFuzzyDegreeValue($collection)
    {
        if ($collection->isEmpty()) { 
            throw new Exception("Model is empty.");
        }

        $collection = $collection->map(function($state) {
            $var = $state[$this->name];
            try {
                $state['degree'] = eval('return '.$this->function.';');
            } catch (Exception $e) {
                 throw new Exception("Can't execute the instruction with given function definition.");
            }
            return $state;
        });

        return $collection;
    }

    protected function fuzzyOrder($collection)
    {
        if (!is_null($this->sort)) {
            if($this->sort == 'asc') { 
                $ordered = $collection->sortBy('price');
            } elseif ($this->sort == 'desc') {
                $ordered = $collection->sortByDesc($this->name);
            } else {
                throw new Exception("Can't process sorting type.");
            }
        }
        
        $ordered = $ordered->sortByDesc('degree');
        return $ordered;
    }

    protected function getKey($collection)
    {
        $collection = self::addFuzzyDegreeValue($collection);
        $collection = self::fuzzyOrder($collection);

        $accepted = collect();
        foreach ($collection as $row) {
            if ($row['degree'] > 0) {
                $accepted->push($row['id']);
            }
        }

        return $accepted;
    }

    protected function getName(){
        return $this->name;
    }

    protected function getTerm(){
        return $this->term;
    }

    protected function getSort(){
        return $this->sort;
    }

    protected function getFunction(){
        return $this->function;
    }

    protected function setName($name){
        $this->name = $name;
    }

    protected function setTerm($term){
        $this->term = $term;
    }

    protected function setSort($sort){
        $this->sort = $sort;
    }

    protected function setFunction($function){
        $this->function = $function;
    }
}