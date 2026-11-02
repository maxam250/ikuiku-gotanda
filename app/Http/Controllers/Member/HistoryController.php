<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\shop;
use App\Models\Equipment;
use App\Models\Costume;
use App\Services\ExternalDatabaseService;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmation;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class HistoryController extends Controller
{
    //
    public function index()
    {
        // 現在ログインしているメンバーのIDを取得
        $memberId = Auth::id();

        // 外部DBサービスを使用して過去の予約情報を取得
        $reservations = ExternalDatabaseService::getReservations([
            'member_id' => $memberId,
            'date' => ['<', now()->format('Y-m-d')]
        ]);

        // 各予約オブジェクトに対してJSONデコードを行う
        $reservations = $reservations->map(function ($reservation) {
            $reservation->option = json_decode($reservation->option, true);
            $reservation->option_pay = json_decode($reservation->option_pay, true);
            return $reservation;
        });

        //
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = Option::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::orderBy('sort', 'asc')->get();

        return view('/member/history', compact('items', 'options', 'option_pays', 'courses' ,'course_fees','reservations', 'costumes', 'equipments'));
    }
}
