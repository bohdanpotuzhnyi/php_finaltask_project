<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller {

    // The key used to store the language in the session
    public static string $sessionkey = 'currentapplocale';

    // List of supported languages
    public static array $officiallanguages = ['de', 'fr', 'en'];

    /**
     * Accept new language and update session
     *
     * @param string $newlang The desired new language
     *
     * @return RedirectResponse
     */
    public function gotoLang(string $newlang): RedirectResponse {

        // Check if desired language is an "official" one
        if (in_array($newlang, self::$officiallanguages, true)) {
            Log::debug('Language switched to new value ' . $newlang . ' (previous language was ' . Session::get('applocale') . ')');
            Session::put(self::$sessionkey, $newlang);
        } else {
            Log::debug('Language ' . $newlang . ' is not an official language, so it cannot be set.');
            abort(400);
        }

        return Redirect::back();
    }

}
