<?php

declare(strict_types=1);

namespace App\Domain\Module\Entities;

use App\Base\Entity;
use App\Domain\Module\ValueObjects\DetailModuleValue;
use InvalidArgumentException;
use Override;

class ModuleEntity extends Entity
{
    /**
     * ID
     */
    public readonly string $id;

    /**
     * Nama
     */
    public readonly string $name;

    /**
     * Icon
     */
    public readonly ?string $icon;

    /**
     * Path url
     */
    public readonly string $path;

    /**
     * Route name
     */
    public readonly string $route;

    /**
     * Daftar detail module
     *
     * @var DetailModuleValue[]
     */
    public readonly array $details;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $id,
        string $name,
        ?string $icon,
        string $path,
        string $route,
        array $details,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->path = $path;
        $this->route = $route;
        $this->details = $this->detailsValidator($details);
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'path' => $this->path,
            'route' => $this->route,
            'details' => array_map(fn ($detail) => $detail->toArray(), $this->details),
        ];
    }

    /**
     * Validasi untuk properti details
     *
     * @return DetailModuleValue[]
     */
    private function detailsValidator(array $details): array
    {
        $result = [];

        foreach ($details as $detail) {
            if ($detail instanceof DetailModuleValue) {
                $result[] = $detail;

                continue;
            }

            throw new InvalidArgumentException(
                message: sprintf('Item harus berupa instance dari %s', DetailModuleValue::class),
            );
        }

        return $result;
    }
}
