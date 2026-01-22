<?php

test('frontend loads and shows Task Manager', function () {
    $page = visit(config('app.url'));

    // Verify page title
    $page->assertSee('Task Manager');

    // Verify no JavaScript errors
    $page->assertNoJavascriptErrors();

    // Verify CSS and JS assets loaded successfully
    $page->assertNoConsoleLogs();
});

test('landing page has app div and is rendered', function () {
    $page = visit(config('app.url'));

    // Verify the Vue app root element exists
    $page->assertPresent('#app');

    // Wait a moment for Vue to mount
    sleep(1);

    // Verify the page loaded without errors
    $page->assertNoJavascriptErrors();
});
