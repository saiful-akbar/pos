<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'button',
        public string $color = 'secondary',
        public ?string $startIcon = null,
        public ?string $endIcon = null,
        public ?string $size = null,
        public bool $fullWidth = false,
        public bool $outline = false,
        public ?string $title = null,
        public bool $disabled = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
