<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetDailyStockOpnameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            'from_date' => [$required, 'date'],
            'to_date' => [$required, 'date'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'from_date' => 'periode awal',
            'to_date' => 'periode akhir',
        ];
    }
}
