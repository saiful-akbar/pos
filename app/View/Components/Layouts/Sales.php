<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Module\Entities\ModuleAccessEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sales extends Component
{
    /**
     * active tab
     */
    public string $active;

    /**
     * Module access
     */
    public ?ModuleAccessEntity $access;

    /**
     * Create a new component instance.
     */
    public function __construct(string $active = 'sales', ?ModuleAccessEntity $access = null)
    {
        $this->active = $active;
        $this->access = $access;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.sales');
    }
}
