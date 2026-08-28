<?php

namespace App\Http\Controllers\Admin;

use App\Const\CouponConst;
use App\Const\GlobalConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coupon\GetCouponRequest;
use App\Http\Requests\Admin\Coupon\PostCouponRequest;
use App\Models\Category;
use App\Services\Admin\CouponService;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    public function index(GetCouponRequest $request)
    {
        session()->forget('coupon_data');

        return view('admin.pages.coupons.index', [
            'coupons' => $this->couponService->search(array_merge($request->validated(), ['relates' => ['restriction']])),
            'types' => CouponConst::types(),
            'statuses' => GlobalConst::statuses(),
        ]);
    }

    public function trash(GetCouponRequest $request)
    {
        return view('admin.pages.coupons.trash', [
            'coupons' => $this->couponService->searchTrashed($request->validated()),
        ]);
    }

    public function create()
    {
        return view('admin.pages.coupons.create', array_merge($this->formOptions(), [
            'data' => session()->get('coupon_data'),
        ]));
    }

    public function edit(int|string $id)
    {
        $coupon = $this->couponService->filter(['relates' => ['restriction']])->find($id);

        abort_if(! $coupon, 404);

        return view('admin.pages.coupons.edit', array_merge($this->formOptions(), [
            'coupon' => $coupon,
        ]));
    }

    public function confirm(PostCouponRequest $request, $id = null)
    {
        session()->put('coupon_data', $this->couponService->prepareConfirmData($request->validated(), $id));

        return redirect()->route('admin.coupons.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('coupon_data');

        if (! $data) {
            return redirect()->route('admin.coupons.create');
        }

        return view('admin.pages.coupons.confirms.form-confirm', array_merge($this->formOptions(), [
            'data' => $data,
        ]));
    }

    public function save()
    {
        $data = session()->get('coupon_data');

        if (! $data) {
            return redirect()->route('admin.coupons.create');
        }

        if (! empty($data['id'])) {
            $this->couponService->update($data['id'], $data);
            $message = __('admin/coupon.messages.updated');
        } else {
            $this->couponService->create($data);
            $message = __('admin/coupon.messages.created');
        }

        session()->forget('coupon_data');

        return redirect()->route('admin.coupons.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $coupon = $this->couponService->filter(['relates' => ['restriction', 'users']])->find($id);

        abort_if(! $coupon, 404);

        return view('admin.pages.coupons.show', array_merge($this->formOptions(), [
            'coupon' => $coupon,
        ]));
    }

    public function destroy(int|string $id)
    {
        $result = $this->couponService->delete($id);

        return redirect()
            ->route('admin.coupons.index')
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->couponService->forceDelete($id)) {
            return redirect()
                ->route('admin.coupons.trash')
                ->with('error', __('admin/coupon.messages.not_found'));
        }

        return redirect()
            ->route('admin.coupons.trash')
            ->with('success', __('admin/coupon.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->couponService->restore($id);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('admin/coupon.messages.restored'));
    }

    protected function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
