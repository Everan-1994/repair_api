<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Storage;

class ArticlesController extends Controller
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function index(Request $request)
    {
        $article = $this->article->whereStatus($request->status)
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return ArticleResource::collection($article);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    public function views(Request $request)
    {
        $this->article->whereId($request->id)->increment('view_count');

        return response([
            'code' => 0,
            'msg'  => 'Success'
        ]);
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $article = $this->article->create([
                'title'   => $request->title,
                'user_id' => \Auth::id(),
                'content' => $request->content,
                'status'  => $request->status
            ]);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['msg' => $exception->getMessage()], 400);
        }

        return new ArticleResource($article);
    }

    public function update(Request $request)
    {
        \DB::beginTransaction();
        try {
            $this->article->whereId($request->id)
                ->update([
                    'title'      => $request->title,
                    'content'    => $request->content,
                    'status'     => $request->status,
                    'updated_at' => now()->toDateTimeString()
                ]);

            \DB::commit();

            return response([
                'code' => 0,
                'msg'  => '更新成功'
            ]);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['msg' => $exception->getMessage()], 400);
        }
    }

    public function changeStatus(Request $request)
    {
        $this->article->whereId($request->id)->update(['status' => $request->status]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function del(Article $article)
    {
        $this->authorize('destroy', $article);
        $article->delete();

        return response([
            'code' => 0,
            'msg'  => '删除成功'
        ]);
    }

    // 上传图片到又拍云
    public function upload(Request $request)
    {
        return Storage::disk('upyun')->put('/', $request->file('image'));
    }
}
