<?php

namespace Fuzzybuilder\Lingvar\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Fuzzybuilder\Lingvar\Models\LingVar;
use Exception;

class LingVarMacroProvider
{
    /**
     * Apply macros on the Builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function __construct()
    {
        $this->registerMacros();
    }

    /**
     * Register macros
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function registerMacros()
    {   
        Builder::macro('whereFuzzy', function ($name, $term, $minDegree = null, $sort = null, $negation = false)
        {
            if (is_null($name) or is_null($term) or empty($name) or empty($term)) {
                throw new Exception("Argument is empty.");
            }
            $model = new Collection(json_decode($this->get(),true));
            $key = $this->getModel()->getKeyName();
            $lingVar = new LingVar($name, $term, $minDegree, $sort, $negation);
            $accepted = $lingVar->getKeysArray($model, $key);
            $this->whereIn($key, $accepted);
            //if (!$accepted->isEmpty() and !is_null($sort)) {
            //    $this->orderByRaw("FIELD(".$key.",".$accepted->implode(',').")");
            return $this;
        });

        Builder::macro('whereFuzzyNot', function ($name, $term, $minDegree = null, $sort = null)
        {
            return $this->whereFuzzy($name, $term, $minDegree, $sort, true);
        });
        

    }
}