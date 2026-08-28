<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormFloatingInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public string $name,
        public string $id,
        public bool $error = false,
        public ?string $helperText = null,
        public bool $required = false,
        public string $type = 'text',
        public null|string|int $value = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form-floating-input');
    }
}
