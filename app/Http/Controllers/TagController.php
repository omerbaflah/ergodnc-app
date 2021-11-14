<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return TagResource::collection(
            Tag::all()
        );
    }
}
