<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use App\Domain\Setting\Entities\SettingEntity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Receipt extends Component
{
    /**
     * Title aplikasi
     */
    public string $title;

    /**
     * Pengaturan
     */
    public ?SettingEntity $setting;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title)
    {
        $this->setting = setting();
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.receipt');
    }
}
