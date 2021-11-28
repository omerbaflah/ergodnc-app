<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $offices = Office::query()
            ->approved()
            ->visible()
            ->when($request->get('host_id'), function (Builder $builder) use ($request) {
                $builder->where('user_id', $request->get('host_id'));
            })
            ->when($request->get('user_id'), function (Builder $builder) use ($request) {
                $builder->whereRelation(
                    'reservations',
                    'user_id',
                    '=',
                    $request->get('user_id')
                );
            })
            ->when($request->get('lat') && $request->get('lng'),
                function (Builder $builder) use ($request) {
                    $builder->nearestTo($request->get('lat'), $request->get('lng'));
                },
                function (Builder $builder) {
                    $builder->oldest();
                })
            ->with([
                'user',
                'tags',
                'images'
            ])
            ->withCount(['reservations' => function (Builder $builder) {
                $builder->where('status', '=', Reservation::STATUS_ACTIVE);
            }])
            ->paginate(20);

        return OfficeResource::collection(
            $offices
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Office $office
     * @return OfficeResource
     */
    public function show(Office $office)
    {
        $office->load([
            'user',
            'tags',
            'images'
        ])->loadCount(['reservations' => function (Builder $builder) {
            $builder->where('status', '=', Reservation::STATUS_ACTIVE);
        }]);

        return OfficeResource::make($office);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Office $office
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Office $office)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Office $office
     * @return \Illuminate\Http\Response
     */
    public function destroy(Office $office)
    {
        //
    }
}
