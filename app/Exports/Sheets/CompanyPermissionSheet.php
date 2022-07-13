<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class CompanyPermissionSheet implements FromCollection, WithTitle
{
    private $data;
    private $title;

    public function __construct(array $data, string $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        return new Collection($this->data);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}
