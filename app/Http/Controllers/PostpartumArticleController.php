<?php

namespace App\Http\Controllers;

use App\Models\PostpartumArticle;

class PostpartumArticleController extends Controller
{
    public function index()
    {
        return PostpartumArticle::all();
    }

    public function show($id)
    {
        return PostpartumArticle::findOrFail($id);
    }
}
