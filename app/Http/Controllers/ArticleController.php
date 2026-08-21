<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\Schema;

class ArticleController extends Controller
{
    public function index()
    {
        $this->seo()
            ->title('دانش فنی — مقالات تخصصی سفال و ساختمان')
            ->description('مقالات فنی درباره عایق‌کاری حرارتی، مبحث ۱۹، مقایسه مصالح، اجرای دیوار سفالی و صرفه‌جویی انرژی.')
            ->breadcrumbs([['خانه', route('home')], ['دانش فنی', null]]);

        return view('pages.articles.index', [
            'articles' => Article::query()->published()->paginate(9),
        ]);
    }

    public function show(Article $article)
    {
        abort_if($article->published_at === null || $article->published_at->isFuture(), 404);

        $this->seo()
            ->title($article->title)
            ->description($article->excerpt)
            ->image($article->cover_image)
            ->type('article')
            ->breadcrumbs([
                ['خانه', route('home')],
                ['دانش فنی', route('articles.index')],
                [$article->title, null],
            ])
            ->schema(Schema::article($article));

        return view('pages.articles.show', [
            'article' => $article,
            'more' => Article::query()->published()->where('id', '!=', $article->id)->take(3)->get(),
        ]);
    }
}
