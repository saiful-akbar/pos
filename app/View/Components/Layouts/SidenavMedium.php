<?php

namespace App\View\Components\Layouts;

use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Setting\Entities\SettingEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidenavMedium extends Component
{
    /**
     * Daftar akses module.
     *
     * @var ModuleEntity[]
     */
    public array $modules;

    /**
     * Data pengaturan.
     *
     * @var SettingEntity|null
     */
    public ?SettingEntity $setting;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->modules = modules();
        $this->setting = setting();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.sidenav-medium');
    }
}
