<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageController extends Controller
{
    public function index()
    {
        // 希望するドライバーで新しいマネージャー インスタンスを作成する
        $manager = new ImageManager(new Driver());

        // 画像を垂直に反転し、別名で保存する例
        $imgPath = storage_path('app/public/file/hoge.png');
        $img = $manager->read($imgPath);
        $img->flop();
        $img->save(storage_path('app/public/file/hoge-flip.png'));
    }
}
