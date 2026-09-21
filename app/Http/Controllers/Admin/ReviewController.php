<?php

namespace App\Http\Controllers\Admin;

use App\Const\ReviewConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\RejectReviewRequest;
use App\Services\Admin\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    public function index(Request $request)
    {
        $stats = $this->reviewService->stats();

        return view('admin.pages.reviews.index', [
            'reviews' => $this->reviewService->paginate($request->only(['status', 'keyword'])),
            'stats' => $stats,
            'pending' => $stats[ReviewConst::STATUS_PENDING],
            'approved' => $stats[ReviewConst::STATUS_APPROVED],
        ]);
    }

    public function approve(string $id): RedirectResponse
    {
        return $this->respond($this->reviewService->approve($id));
    }

    public function reject(RejectReviewRequest $request, string $id): RedirectResponse
    {
        return $this->respond($this->reviewService->reject($id, $request->validated('reason')));
    }

    public function destroy(string $id): RedirectResponse
    {
        return $this->respond($this->reviewService->delete($id));
    }

    protected function respond(array $result): RedirectResponse
    {
        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
