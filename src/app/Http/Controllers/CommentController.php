<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id){
        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $item_id,
            'comment' => $request->input('comment'),
        ]);

        return back();
    }
}
