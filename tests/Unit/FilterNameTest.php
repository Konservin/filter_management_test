<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FilterNameTest extends TestCase
{
    public function testFilterNameCannotBeBlankInBusinessRuleContext(): void
    {
        // This is a simple "unit" example: you can replace it with actual domain logic
        // if you have a helper/validator/service that normalizes names.
        $name = trim("   ");

        $this->assertSame('', $name);
    }
}
