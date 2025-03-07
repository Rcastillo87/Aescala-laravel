<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class TableHeader extends Component
{
    public $headers;
    public $items ; // Cambiar $items por $data
    
    public function __construct($headers = [])
    {
        $this->headers = $headers;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.table-header');
    }
}