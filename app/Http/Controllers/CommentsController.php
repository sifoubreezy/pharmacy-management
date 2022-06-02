<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CommentsService;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    /**
     * $commentsService
     *
     * @var undefined
     */
    private $commentsService;

    /**
     * CommentsController constructor.
     * @param CommentsService $commentsService
     */
    public function __construct(CommentsService $commentsService)
    {
        $this->middleware('auth');
        $this->commentsService = $commentsService;
    }


    /**
     * delete
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $postId = $request->input('post_id');
        try {
            if (Auth::user()->getAuthIdentifier() != $request->input('user_id')) {
                throw new Exception('Unauthorized');
            }
            $this->commentsService->delete($request->input('id'));
            return redirect("/Posts/$postId")->with('success', 'L\'élément a été supprimé');
        } catch (Exception | Throwable $e) {
            $response->setStatusCode(406, 'une erreur s\'est produite');
            return redirect("/Posts/$postId")->withErrors('une erreur s\'est produite');
        }
    }

    public function createPost(Request $request, Response $response)
    {
        $postId = $request->input('post_id');
        try {
            $this->commentsService->createComment(
                [
                    'content' => $request->input('comment'),
                    'user_id' => Auth::user()->getAuthIdentifier(),
                    'post_id' => $postId,
                ]
            );
            $response->setStatusCode(201);
            return redirect("/Posts/$postId")->with('success', 'Votre commentaire a été posté');
        } catch (Exception | Throwable $e) {
            $response->setStatusCode(406, 'une erreur s\'est produite');
            return redirect("/Posts/$postId")->withErrors('une erreur s\'est produite');
        }
    }

}
