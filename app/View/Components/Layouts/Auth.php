<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Module\Entities\ModuleAccessEntity;
use App\Domain\Setting\Entities\SettingEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Auth extends Component
{
    /**
     * Data pengaturan
     */
    public ?SettingEntity $setting;

    /**
     * Module access
     */
    public ?ModuleAccessEntity $access;

    /**
     * Title aplikasi
     */
    public string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, ?ModuleAccessEntity $access = null)
    {
        $this->setting = setting();
        $this->access = $access;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.auth');
    }
}
