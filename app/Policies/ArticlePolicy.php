<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function destroy(User $user,  Article $article)
    {
        return $user->id == $article->user_id;
    }
}
