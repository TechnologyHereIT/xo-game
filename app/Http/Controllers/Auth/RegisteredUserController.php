<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $countries = [
            'SA' => 'السعودية',
            'EG' => 'مصر',
            'AE' => 'الإمارات',
            'KW' => 'الكويت',
            'QA' => 'قطر',
            'BH' => 'البحرين',
            'OM' => 'عمان',
            'JO' => 'الأردن',
            'LB' => 'لبنان',
            'SY' => 'سوريا',
            'IQ' => 'العراق',
            'DZ' => 'الجزائر',
            'MA' => 'المغرب',
            'TN' => 'تونس',
            'SD' => 'السودان',
            'YE' => 'اليمن',
            'LY' => 'ليبيا',
            'PS' => 'فلسطين',
            'SO' => 'الصومال',
            'MR' => 'موريتانيا',
            'DJ' => 'جيبوتي',
            'KM' => 'جزر القمر'
        ];

        return view('auth.register', compact('countries'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country, // تم التصحيح هنا
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}