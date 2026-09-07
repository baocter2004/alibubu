<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Question\AnswerQuestionRequest;
use App\Models\ProductQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public const PER_PAGE = 15;

    public function index(Request $request)
    {
        $status = $request->query('status');

        $questions = ProductQuestion::query()
            ->with(['product', 'user', 'admin'])
            ->when($status === 'pending', fn ($query) => $query->whereNull('answer'))
            ->when($status === 'answered', fn ($query) => $query->whereNotNull('answer'))
            ->when($status === 'hidden', fn ($query) => $query->where('is_published', false))
            ->latest('id')
            ->paginate(self::PER_PAGE);

        return view('admin.pages.questions.index', [
            'questions' => $questions,
            'pendingCount' => ProductQuestion::whereNull('answer')->count(),
        ]);
    }

    public function answer(AnswerQuestionRequest $request, string $id): RedirectResponse
    {
        $question = ProductQuestion::find($id);

        if (! $question) {
            return back()->with('error', __('admin/question.messages.not_found'));
        }

        $question->update([
            'answer' => $request->validated()['answer'],
            'answered_by' => Auth::guard('admin')->id(),
            'answered_at' => now(),
            'is_published' => true,
        ]);

        return back()->with('success', __('admin/question.messages.answered'));
    }

    public function toggle(string $id): RedirectResponse
    {
        $question = ProductQuestion::find($id);

        if (! $question) {
            return back()->with('error', __('admin/question.messages.not_found'));
        }

        $question->update(['is_published' => ! $question->is_published]);

        return back()->with('success', __('admin/question.messages.visibility_updated'));
    }

    public function destroy(string $id): RedirectResponse
    {
        ProductQuestion::whereKey($id)->delete();

        return back()->with('success', __('admin/question.messages.deleted'));
    }
}
