<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Module\Entities\ModuleAccessEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StockOpname extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $active,
        public ?ModuleAccessEntity $access
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.stock-opname');
    }
}
