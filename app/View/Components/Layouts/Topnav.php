<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Setting\Entities\SettingEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Topnav extends Component
{
    /**
     * Daftar akses module.
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
        return view('components.layouts.topnav');
    }
}
