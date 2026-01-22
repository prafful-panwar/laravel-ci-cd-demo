<?php

// Simple browser test - just checks if the page loads and contains expected text
test('frontend loads and shows Task Manager', function () {
    $page = visit(config('app.url'));

    $page->assertSee('Task Manager');
});
