<?php

namespace Fuzzybuilder\Lingvar\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Fuzzybuilder\Lingvar\Models\LingVar;
use Exception;

class MacroProvider
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
     * @param  \Illuminate\Database\Query\Builder $query
     * @return void
     */
    private function registerMacros()
    {   
        Builder::macro('whereFuzzy', function ($name, $term, $sort = NULL)
        {
            if (is_null($name) or is_null($term) or empty($name) or empty($term)) {
                throw new Exception("Argument is empty.");
            }

            $model = new Collection(json_decode($this->get(),true));
            $lingVar = new LingVar($name, $term, $sort);
            $accepted = $lingVar->getKey($model);
            
            $this->whereIn('id', $accepted);
            if (!$accepted->isEmpty()) {
                $this->orderByRaw("FIELD("."id".",".$accepted->implode(',').")");
            }

        });
    }
}