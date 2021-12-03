<?php

namespace Tests\Feature\Controllers;

use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
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

    /**
     * @test
     */
    public function itEagerLoadingRelationsForOfficeModel()
    {
        $user = User::factory()->create();

        $office = Office::factory()->for($user)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $tagIds = Tag::all()->pluck('id');

        $office->images()->create([
            'path' => 'image.png'
        ]);

        $office->tags()->sync($tagIds);

        $response = $this->get(
            $this->uri . '?host_id=' . $user->id
        )->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'user',
                    'images',
                    'tags'
                ],
            ],
            'links',
            'meta'
        ]);

        $this->assertIsArray($response->json('data')[0]['user']);

        $this->assertIsArray($response->json('data')[0]['images']);

        $this->assertIsArray($response->json('data')[0]['tags']);

        $this->assertEquals($office->id,$response->json('data')[0]['id']);

        $this->assertEquals($user->id,$response->json('data')[0]['user']['id']);
    }

    /**
     * @test
     */
    public function itFiltersByHostId()
    {
        Office::factory()->count(3)->create();

        $host = User::factory()->create();

        $office = Office::factory()->for($host)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $response = $this->get(
            $this->uri . '?host_id=' . $host->id
        )->assertOk()->assertJsonCount(1,'data');

        $this->assertEquals($office->id,$response->json('data')[0]['id']);
    }

    /**
     * @test
     */
    public function itFiltersByVisitorId()
    {
        Office::factory()->count(3)->create();

        $visitor = User::factory()->create();

        $office = Office::factory()->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        Reservation::factory()->for($office)->for($visitor)->create();

        $response = $this->get(
            $this->uri . '?visitor_id=' . $visitor->id
        )->assertOk()->assertJsonCount(1,'data');

        $this->assertEquals($office->id,$response->json('data')[0]['id']);

        //TODO check how to create the factory of a model with their relationships
    }

    /**
     * @test
     */
    public function itReturnsTheNumberOfActiveReservations()
    {
        $user = User::factory()->create();

        $office = Office::factory()->for($user)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        Reservation::factory()->count(3)->for($office)->create();

        Reservation::factory()->for($office)->create([
            'status' => Reservation::STATUS_CANCELED
        ]);

        $response = $this->get($this->uri . '?host_id=' . $user->id)
            ->assertOk()
            ->assertSee('reservations_count');

        $this->assertEquals(3,$response->json('data')[0]['reservations_count']);
    }

    /**
     * @test
     */
    public function itOrdersByDistanceWhenCoordinatesAreProvided()
    {
        //lisbon coordinates
        //lat 38.720661384644046
        //lng -9.16044783453807

        $lat = 38.720661384644046;
        $lng = -9.16044783453807;

        $user = User::factory()->create();

        //far office
        $leiria = Office::factory()->for($user)->create([
            'title' => 'Leiria',
            'lat' => 39.74051727562952,
            'lng' => -8.770375324893696,
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        //closer office
        $torres_vedras = Office::factory()->for($user)->create([
            'title' => 'Torres Vedras',
            'lat' => 39.07753883078113,
            'lng' => -9.281266331143293,
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $response = $this->get(
            $this->uri . "?host_id=" . $user->id . "&lat=" . $lat . "&lng=" . $lng
        )->assertOk();

        $this->assertEquals($torres_vedras->title,$response->json('data')[0]['title']);
        $this->assertEquals($leiria->title,$response->json('data')[1]['title']);

        $response = $this->get(
            $this->uri . "?host_id=" . $user->id
        )->assertOk();

        $this->assertEquals($leiria->title,$response->json('data')[0]['title']);
        $this->assertEquals($torres_vedras->title,$response->json('data')[1]['title']);
    }

    /**
     * @test
     */
    public function itShowsTheOffice()
    {
        $user = User::factory()->create();

        $office = Office::factory()->for($user)->create([
            'approval_status' => Office::APPROVAL_APPROVED
        ]);

        $tagIds = Tag::all()->pluck('id');

        $office->images()->create([
            'path' => 'image.png'
        ]);

        $office->tags()->sync($tagIds);

        Reservation::factory()->count(3)->for($office)->create();

        Reservation::factory()->for($office)->create([
            'status' => Reservation::STATUS_CANCELED
        ]);

        $response = $this->get($this->uri . '/' . $office->id)
            ->assertOk()
            ->assertSee('reservations_count')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'user',
                    'images',
                    'tags'
                ]
            ]);

        $this->assertIsArray($response->json('data')['user']);

        $this->assertIsArray($response->json('data')['images']);

        $this->assertIsArray($response->json('data')['tags']);

        $this->assertEquals($office->id,$response->json('data')['id']);

        $this->assertEquals($user->id,$response->json('data')['user']['id']);

        $this->assertEquals(3,$response->json('data')['reservations_count']);
    }
}
