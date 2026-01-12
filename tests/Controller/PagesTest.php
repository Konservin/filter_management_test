<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PagesTest extends WebTestCase
{
    public function testFiltersListPageLoads(): void
    {
        $client = static::createClient();

        // Adjust this URL to your real list route:
        // e.g. '/', '/filters', '/filter/list'
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // Optional: assert some text exists that your list page always has:
        // $this->assertSelectorTextContains('h3', 'Filters List');
    }

    public function testCreateModalLoads(): void
    {
        $client = static::createClient();

        // Adjust URL to your "new filter" form/modal route if you have it
        // Example: '/filter/new/modal'
        $client->request('GET', '/filter/new');

        // If your create form is only reachable via AJAX, you might get 200 anyway,
        // or you might redirect (302). Accept both to be robust.
        $this->assertTrue(in_array($client->getResponse()->getStatusCode(), [200, 302], true));
    }
}
