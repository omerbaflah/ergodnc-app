<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\Integer;
use Tests\TestCase;

class TagsControllerTest extends TestCase
{
    protected int $appVersion;
    protected string $baseEndpoint;
    protected string $resourceEndpoint;
    protected string $uri;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->appVersion = env('APP_VERSION',1);
        $this->resourceEndpoint = '/tags';
        $this->baseEndpoint = "/api/v{$this->appVersion}";
        $this->uri = "{$this->baseEndpoint}{$this->resourceEndpoint}";
        parent::__construct($name, $data, $dataName);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_tags_list()
    {
        $this->withoutExceptionHandling();
        //$response = $this->get('/api/v1/tags')->assertOk()->assertJsonStructure([
        $this->get($this->uri)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);
    }
}
