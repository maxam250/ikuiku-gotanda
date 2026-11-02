<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\Payment\PointPurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PointPurchaseController extends Controller
{
    public function __construct(
        private readonly PointPurchaseService $pointService
    ) {}

    public function index()
    {
        $packages = config('point_packages.packages', []);
        return view('member.points.purchase', compact('packages'));
    }

    public function confirm(Request $request)
    {
        $packageId = (int) $request->query('package');
        $packages  = config('point_packages.packages', []);
        $package   = collect($packages)->firstWhere('id', $packageId);

        if (!$package) {
            return redirect()->route('member.points.purchase')
                ->withErrors(['package' => 'パッケージが見つかりません。']);
        }

        $isDummy = config('fanclub.payments.driver') === 'dummy_square';

        return view('member.points.purchase_confirm', compact('package', 'isDummy'));
    }

    public function purchase(Request $request)
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'package_id'   => ['required', 'integer'],
            'agreed_terms' => ['required', 'accepted'],
        ]);

        $packages = config('point_packages.packages', []);
        $package  = collect($packages)->firstWhere('id', (int) $validated['package_id']);

        if (!$package) {
            return back()->withErrors(['package' => 'パッケージが見つかりません。']);
        }

        try {
            $redirectUrl = route('member.points.purchase.complete');
            $result = $this->pointService->initiateCheckout(
                $member,
                (int) $package['price'],
                (int) $package['points'],
                $redirectUrl
            );
        } catch (\Throwable $e) {
            Log::error('Point purchase checkout failed', [
                'member_id'  => $member->id,
                'package_id' => $validated['package_id'],
                'error'      => $e->getMessage(),
            ]);

            return back()->withErrors(['purchase' => '決済処理の開始に失敗しました。しばらく経ってから再度お試しください。']);
        }

        // Square チェックアウトページへリダイレクト
        if ($result['checkout_url']) {
            return redirect()->away($result['checkout_url']);
        }

        // Dummy モード: 即時完了のため完了ページへ直接遷移
        return redirect()->route('member.points.purchase.complete')
            ->with('success', number_format($package['points']) . 'ptの購入が完了しました。');
    }

    public function complete()
    {
        return view('member.points.purchase_complete');
    }
}
