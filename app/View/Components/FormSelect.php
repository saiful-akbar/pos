<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormSelect extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $id,
        public ?string $label = null,
        public bool $error = false,
        public bool $required = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form-select');
    }
}
