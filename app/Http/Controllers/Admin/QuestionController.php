<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Question\AnswerQuestionRequest;
use App\Services\Admin\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function __construct(protected QuestionService $questionService) {}

    public function index(Request $request)
    {
        return view('admin.pages.questions.index', [
            'questions' => $this->questionService->paginate($request->query('status')),
            'pendingCount' => $this->questionService->pendingCount(),
        ]);
    }

    public function answer(AnswerQuestionRequest $request, string $id): RedirectResponse
    {
        return $this->respond($this->questionService->answer(
            $id,
            $request->validated('answer'),
            Auth::guard('admin')->user()
        ));
    }

    public function toggle(string $id): RedirectResponse
    {
        return $this->respond($this->questionService->toggle($id));
    }

    public function destroy(string $id): RedirectResponse
    {
        return $this->respond($this->questionService->delete($id));
    }

    protected function respond(array $result): RedirectResponse
    {
        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
