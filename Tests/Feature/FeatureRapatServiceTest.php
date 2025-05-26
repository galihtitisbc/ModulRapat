<?php
namespace Modules\Rapat\Tests\Feature;

use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FeatureRapatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        Queue::fake();
    }

}
