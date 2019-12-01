<?php

declare(strict_types=1);

namespace App\Parser;

interface ParserInterface
{
    public function parse(): array;
}
