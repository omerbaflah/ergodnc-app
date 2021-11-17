<?php

namespace Tests\Feature\Controllers;

use App\Models\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OfficesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected int $appVersion;
    protected string $baseEndpoint;
    protected string $resourceEndpoint;
    protected string $uri;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->appVersion = env('APP_VERSION',1);
        $this->resourceEndpoint = '/offices';
        $this->baseEndpoint = "/api/v{$this->appVersion}";
        $this->uri = "{$this->baseEndpoint}{$this->resourceEndpoint}";
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @test
     */
    public function itListsAllOfficesInPaginatedWay()
    {
        $this->withoutExceptionHandling();

        Office::factory()->count(3)->create();

        $this->get($this->uri)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ]
            ],
            'links' => [
                '*' => [

                ]
            ],
            'meta' => [
                '*' => [

                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function itOnlyListsOfficesThatAreNotHidden()
    {
        $this->withoutExceptionHandling();

        Office::factory()->count(3)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $visibleOfficesCount = Office::query()
            ->where('approval_status',Office::APPROVAL_APPROVED)
            ->where('hidden',Office::VISIBLE)
            ->count();

        $this->get($this->uri)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ]
            ],
            'links' => [
                '*' => [

                ]
            ],
            'meta' => [
                '*' => [

                ]
            ]
        ])->assertJsonCount($visibleOfficesCount,'data');
    }

    /**
     * @test
     */
    public function itOnlyListsOfficesThatAreNotApproved()
    {
        $this->withoutExceptionHandling();

        Office::factory()->count(3)->create();

        Office::factory()->count(3)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $notApprovedOfficesCount = Office::query()
            ->where('approval_status',Office::APPROVAL_PENDING)
            ->count();

        $this->get($this->uri)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ]
            ],
            'links' => [
                '*' => [

                ]
            ],
            'meta' => [
                '*' => [

                ]
            ]
        ])->assertJsonCount($notApprovedOfficesCount,'data');
    }
}
