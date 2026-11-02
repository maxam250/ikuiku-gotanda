<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\File;

class Icon extends Component
{

    public $name;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $class = '')
    {
        //
        $this->name = $name;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $path = resource_path("svg/{$this->name}.svg");

        if (!File::exists($path)){
            return ''; //ファイルがない時は空文字を返す
        }

        $svg = File::get($path);

        // class属性を追加する（元のsvgにclassがない場合）
        $svg = preg_replace('/<svg /', '<svg class="' . e($this->class) . '" ',$svg, 1);

        return $svg;
        // return view('components.icon');
    }
}
