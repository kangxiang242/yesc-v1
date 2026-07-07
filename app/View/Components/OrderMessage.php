<?php

namespace App\View\Components;

use Illuminate\View\Component;

class OrderMessage extends Component
{
    public function __construct()
    {
    }

    public function render()
    {
        return view('components.order-message');
    }
}
