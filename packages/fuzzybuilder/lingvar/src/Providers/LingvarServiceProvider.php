<?php

namespace Fuzzybuilder\Lingvar\Providers;

use Illuminate\Support\ServiceProvider;
use Fuzzybuilder\Lingvar\Controllers\MacroController;

Class LingvarServiceProvider extends ServiceProvider
{
    public function register()
    {
        (new LingVarMacroProvider);
    }
}

