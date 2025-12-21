<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class StorefrontLayout extends Component
{
    public $store;

    public function __construct($store)
    {
        $this->store = $store;
    }

    public function render(): View
    {
        return view('layouts.storefront');
    }
}
