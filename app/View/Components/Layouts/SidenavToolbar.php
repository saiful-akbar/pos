<?php

namespace App\View\Components\Layouts;

use App\Domain\Setting\Entities\SettingEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidenavToolbar extends Component
{
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
        $this->setting = setting();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.sidenav-toolbar');
    }
}
