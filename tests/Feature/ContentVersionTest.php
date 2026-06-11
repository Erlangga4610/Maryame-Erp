<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContentVersionTest extends TestCase
{
    public function test_version_and_adjustment_models_exist(): void
    {
        $this->assertTrue(class_exists('App\Content\Models\ContentVersion'));
        $this->assertTrue(class_exists('App\Content\Models\AdjustmentLog'));
    }

    public function test_content_has_versions_and_adjustments_relations(): void
    {
        $content = new \App\Content\Models\Content;
        $this->assertTrue(method_exists($content, 'versions'));
        $this->assertTrue(method_exists($content, 'adjustmentLogs'));
    }
}
