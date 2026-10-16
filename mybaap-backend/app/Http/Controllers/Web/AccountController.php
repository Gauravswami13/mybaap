<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\AppNotification;
use App\Models\Faq;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\AccountService;
use App\Services\CatalogService;
use App\Services\PlaybackService;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptions,
        private readonly PlaybackService $playback,
        private readonly CatalogService $catalog,
        private readonly RazorpayService $razorpay,
        private readonly AccountService $accounts,
    ) {}

    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('site.account', [
            'user' => $user,
            'subscription' => $this->subscriptions->current($user),
        ]);
    }

    public function history(Request $request): View
    {
        return view('site.history', [
            'items' => $this->subscriptions->history($request->user(), 15),
        ]);
    }

    public function notifications(Request $request): View
    {
        $userId = $request->user()->id;

        $items = AppNotification::query()
            ->where(function ($query) use ($userId): void {
                $query->where('audience', 'all')
                    ->orWhere(function ($query) use ($userId): void {
                        $query->where('audience', 'one')->where('user_id', $userId);
                    });
            })
            ->latest()
            ->limit(50)
            ->get();

        return view('site.notifications', ['items' => $items]);
    }

    public function faq(): View
    {
        return view('site.faq', [
            'faqs' => Faq::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('site.profile', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->name = $request->string('name')->toString();
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('profiles', 'public');
        }
        $user->save();

        return redirect()->route('account')->with('status', 'Profile updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['required', 'accepted']]);
        /** @var User $user */
        $user = $request->user();
        $this->accounts->delete($user);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Account deleted.');
    }

    public function watch(Request $request, int $id): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        try {
            $url = $this->playback->signedUrl($user, $id);
        } catch (ApiException $exception) {
            return redirect()->route('plans')->with('error', $exception->getMessage());
        }

        $video = $this->catalog->video($id);

        return view('site.watch', [
            'url' => $url,
            'title' => $video->title,
        ]);
    }

    public function checkout(Request $request, SubscriptionPlan $plan): View|RedirectResponse
    {
        if (! $plan->is_active) {
            abort(404);
        }

        try {
            $order = $this->razorpay->createOrder($request->user(), $plan);
        } catch (ApiException $exception) {
            return redirect()->route('plans')->with('error', $exception->getMessage());
        }

        return view('site.checkout', [
            'order' => $order,
            'plan' => $plan,
        ]);
    }

    public function verifyPayment(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $data = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        try {
            $this->razorpay->verify(
                $request->user(),
                $plan,
                $data['razorpay_order_id'],
                $data['razorpay_payment_id'],
                $data['razorpay_signature'],
            );
        } catch (ApiException $exception) {
            return redirect()->route('plans')->with('error', $exception->getMessage());
        }

        return redirect()->route('account')->with('status', 'Plan is active.');
    }
}
