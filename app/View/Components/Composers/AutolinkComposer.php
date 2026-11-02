<?php

namespace App\View\Components\Composers;

use Illuminate\View\View;

class AutolinkComposer
{
    public function compose(View $view)
    {
        $view->with('autolink', function ($text) {
            return autolink($text);
        });
    }
}
