<?php

declare(strict_types=1);

namespace App\Model;

use PhpOffice\PhpSpreadsheet\Writer\IWriter;

interface ExportModelInterface
{
    public function getWriter(): IWriter;
}
