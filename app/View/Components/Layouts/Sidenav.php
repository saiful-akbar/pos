<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Module\Entities\ModuleEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidenav extends Component
{
    /**
     * Daftar akses module.
     *
     * @var ModuleEntity[]
     */
    public array $modules;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->modules = modules();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.sidenav');
    }
}
