<?php

namespace App\Http\Controllers;

use App\Models\Comment;
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
