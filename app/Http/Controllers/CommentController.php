<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        var_dump($comments);
        return view('welcome');
        // return view('user.profile', ['user' => User::findOrFail($id)]);
    }
}