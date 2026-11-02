<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\Equipment;
use App\Models\Costume;
use App\Services\ExternalDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ReservationController extends Controller
{
    /**
     * 本日以降の予約一覧を取得
     */
    public function getFutureReservations()
    {
        $model = ExternalDatabaseService::getReservationModel();

        $shop = Shop::first();
        $reservations = $model::where('shop_id', $shop->id ?? 1)
            ->whereDate('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get()
            ->map(function ($reservation) {
                // ローカルサーバーからcourseの情報を取得
                $courseName = '';
                if ($reservation->course_id) {
                    $course = \App\Models\Course::find($reservation->course_id);
                    $courseName = $course ? $course->title : '';
                }

                return [
                    'id' => $reservation->id,
                    'date' => $reservation->date->format('Y年m月d日'). '（' . $reservation->date->isoFormat('ddd') . '）',
                    'time' => $reservation->time_start->format('H:i'),
                    'cast_name' => $reservation->item_name,
                    'course_name' => $courseName,
                    'fee_total' => number_format($reservation->fee_total) . '円',
                    'status' => $reservation->status
                ];
            });

        return response()->json($reservations);
    }

    /**
     * 予約詳細情報を取得
     */
    public function getReservationDetails($id)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $reservation = $model::where('shop_id', 3)->findOrFail($id);

        // ローカルサーバーからitemとcourseの情報を取得
        if ($reservation->item_id) {
            $item = \App\Models\Item::find($reservation->item_id);
            $reservation->item = $item;
        }

        if ($reservation->course_id) {
            $course = \App\Models\Course::find($reservation->course_id);
            $reservation->course = $course;
        }

        // コース料金情報を取得
        $course_fees = CourseFee::all();
        $courses = Course::all();

        // オプション情報を取得
        $options = Option::all();
        $option_pays = OptionPay::all();
        $equipments = Equipment::all();
        $costumes = Costume::all();

        // 各種オプションをデコード
        $option_ar = json_decode($reservation->option, true);
        $option_pay_ar = json_decode($reservation->option_pay, true);
        $costume_ar = json_decode($reservation->costume, true);
        $myoption_ar = json_decode($reservation->myoption, true);
        $mycostume_ar = json_decode($reservation->mycostume, true);

        // オプション名を取得
        $options_text = collect($option_ar)->map(function($val) use ($options) {
            return $options->where('id', $val)->first()->title ?? '';
        })->filter()->implode(' / ');

        $option_pays_text = collect($option_pay_ar)->map(function($val) use ($option_pays) {
            return $option_pays->where('id', $val)->first()->title ?? '';
        })->filter()->implode(' / ');

        $costumes_text = collect($costume_ar)->map(function($val) use ($costumes) {
            return $costumes->where('id', $val)->first()->name ?? '';
        })->filter()->implode(' / ');

        // マイオプションとマイコスチュームの情報を整形
        $myoptions = [];
        $mycostumes = [];

        if ($myoption_ar) {
            foreach ($reservation->item->myOptions as $myOption) {
                if (in_array($myOption->id, $myoption_ar)) {
                    $myoptions[] = [
                        'name' => $myOption->name,
                        'price' => number_format($myOption->price),
                        'image' => asset('uploads/myoption/' . $myOption->filename)
                    ];
                }
            }
        }

        if ($mycostume_ar) {
            foreach ($reservation->item->myCostumes as $myCostume) {
                if (in_array($myCostume->id, $mycostume_ar)) {
                    $mycostumes[] = [
                        'name' => $myCostume->name,
                        'price' => number_format($myCostume->price),
                        'image' => asset('uploads/mycostume/' . $myCostume->filename)
                    ];
                }
            }
        }

        // コース情報
        $course_info = '';
        foreach ($courses as $course) {
            if ($course->id == $reservation->course_id) {
                $course_info = $course->title;
                break;
            }
        }

        foreach ($course_fees as $course_fee) {
            if ($course_fee->id == $reservation->course_time) {
                $course_info .= ' / ' . $course_fee->course . ' / ' . number_format($course_fee->fee) . '円';
                break;
            }
        }

        // スタジオ情報
        $studio = $equipments->where('id', $reservation->equipment_id)->first()->title ?? '';

        $details = [
            'datetime' => $reservation->date->format('Y年m月d日'). '（' . $reservation->date->isoFormat('ddd') . '） ' . $reservation->time_start->format('H:i'),
            'course' => $course_info,
            'cast_name' => $reservation->item_name,
            'options' => $options_text,
            'option_pays' => $option_pays_text,
            'studio' => $studio,
            'costumes' => $costumes_text,
            'myoptions' => $myoptions,
            'mycostumes' => $mycostumes,
            'area' => $reservation->area,
            'place' => $reservation->place,
            'meet' => $reservation->meet,
            'address' => $reservation->address,
            'comment' => $reservation->comment,
            'fees' => [
                'course' => number_format($reservation->fee_course),
                'option' => $reservation->fee_option > 0 ? number_format($reservation->fee_option) : null,
                'myoption' => $reservation->fee_myoption > 0 ? number_format($reservation->fee_myoption) : null,
                'mycostume' => $reservation->fee_mycostume > 0 ? number_format($reservation->fee_mycostume) : null,
                'total' => number_format($reservation->fee_total)
            ]
        ];

        return response()->json($details);
    }

    /**
     * 最近の予約一覧を取得
     */
    public function recent()
    {
        $model = ExternalDatabaseService::getReservationModel();
        $reservations = $model::with(['member'])
            ->where('shop_id', 3)
            ->orderBy('date', 'desc')
            ->orderBy('time_start', 'desc')
            ->take(10)
            ->get()
            ->map(function($reservation) {
                try {
                    $memberName = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('name')) : '不明';
                } catch (\Exception $e) {
                    $memberName = $reservation->member ? $reservation->member->name : '不明';
                }

                // ローカルサーバーからitemとcourseの情報を取得
                $courseName = '';
                $castName = $reservation->item_name;

                if ($reservation->course_id) {
                    $course = \App\Models\Course::find($reservation->course_id);
                    $courseName = $course ? $course->title : '';
                }

                if ($reservation->item_id) {
                    $item = \App\Models\Item::find($reservation->item_id);
                    $castName = $item ? $item->item_name : $reservation->item_name;
                }

                return [
                    'id' => $reservation->id,
                    'datetime' => $reservation->date . ' ' . $reservation->time_start,
                    'member_name' => $memberName,
                    'course' => $courseName,
                    'cast_name' => $castName
                ];
            });

        return response()->json($reservations);
    }

    /**
     * 予約の詳細情報を取得
     */
    public function details($id)
    {
        $model = ExternalDatabaseService::getReservationModel();
        $reservation = $model::where('shop_id', 3)->findOrFail($id);
        $reservation->load(['member', 'equipment']);

        // ローカルサーバーからitemとcourseの情報を取得
        if ($reservation->item_id) {
            $item = \App\Models\Item::find($reservation->item_id);
            $reservation->item = $item;
        }

        if ($reservation->course_id) {
            $course = \App\Models\Course::find($reservation->course_id);
            $reservation->course = $course;
        }

        try {
            $memberName = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('name')) : '不明';
            $memberEmail = $reservation->member ? Crypt::decryptString($reservation->member->getRawOriginal('email')) : '';
        } catch (\Exception $e) {
            $memberName = $reservation->member ? $reservation->member->name : '不明';
            $memberEmail = $reservation->member ? $reservation->member->email : '';
        }

        // オプション情報の取得
        $options = [];
        if ($reservation->option) {
            $optionIds = is_array($reservation->option) ? $reservation->option : json_decode($reservation->option, true);
            if ($optionIds) {
                $options = \App\Models\Option::whereIn('id', $optionIds)->pluck('title')->join('、');
            }
        }

        // 有料オプション情報の取得
        $optionPays = [];
        if ($reservation->option_pay) {
            $optionPayIds = is_array($reservation->option_pay) ? $reservation->option_pay : json_decode($reservation->option_pay, true);
            if ($optionPayIds) {
                $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->pluck('title')->join('、');
            }
        }

        // コスチューム情報の取得
        $costumes = [];
        if ($reservation->costume) {
            $costumeIds = is_array($reservation->costume) ? $reservation->costume : json_decode($reservation->costume, true);
            if ($costumeIds) {
                $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->pluck('name')->join('、');
            }
        }

        // マイオプション情報の取得
        $myoptions = [];
        if ($reservation->myoption) {
            $myoptionData = is_array($reservation->myoption) ? $reservation->myoption : json_decode($reservation->myoption, true);
            if ($myoptionData) {
                foreach ($myoptionData as $option) {
                    $myoptions[] = [
                        'name' => $option['name'] ?? '',
                        'price' => $option['price'] ?? 0
                    ];
                }
            }
        }

        // マイコスチューム情報の取得
        $mycostumes = [];
        if ($reservation->mycostume) {
            $mycostumeData = is_array($reservation->mycostume) ? $reservation->mycostume : json_decode($reservation->mycostume, true);
            if ($mycostumeData) {
                foreach ($mycostumeData as $costume) {
                    $mycostumes[] = [
                        'name' => $costume['name'] ?? '',
                        'price' => $costume['price'] ?? 0
                    ];
                }
            }
        }

        return response()->json([
            'datetime' => $reservation->date . ' ' . $reservation->time_start,
            'member_name' => $memberName,
            'member_email' => $memberEmail,
            'course' => $reservation->course ? $reservation->course->title : '',
            'cast_name' => $reservation->item ? $reservation->item->item_name : '',
            'options' => $options,
            'option_pays' => $optionPays,
            'studio' => $reservation->equipment ? $reservation->equipment->title : '',
            'costumes' => $costumes,
            'myoptions' => $myoptions,
            'mycostumes' => $mycostumes,
            'area' => $reservation->area,
            'place' => $reservation->place,
            'meet' => $reservation->meet,
            'address' => $reservation->address,
            'fees' => [
                'course' => $reservation->course ? $reservation->course->price : 0,
                'option' => $reservation->option_price,
                'myoption' => $reservation->myoption_price,
                'mycostume' => $reservation->mycostume_price,
                'total' => $reservation->fee_total
            ]
        ]);
    }

    /**
     * 料金計算API
     */
    public function calculateFees(Request $request)
    {
        $courseId = $request->input('course_id');
        $courseTime = $request->input('course_time');
        $itemId = $request->input('item_id');

        try {
            // 新しい料金計算システムを使用
            $courseFee = $this->calculateCourseFee($itemId, $courseId, $courseTime);

            // オプション料金は0（管理画面では手動入力）
            $optionFee = 0;
            $myoptionFee = 0;
            $mycostumeFee = 0;

            return response()->json([
                'success' => true,
                'fee_course' => $courseFee,
                'fee_option' => $optionFee,
                'fee_myoption' => $myoptionFee,
                'fee_mycostume' => $mycostumeFee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 新しい料金計算システム
     * item_course_feesテーブルから料金を取得
     */
    private function calculateCourseFee($itemId, $courseId, $courseTime)
    {
        try {
            // item_course_feesテーブルから料金設定を取得
            $feeSetting = \DB::table('item_course_fees')
                ->where('item_id', $itemId)
                ->where('course_id', $courseId)
                ->first();

            if (!$feeSetting) {
                return 0;
            }

            // 一律料金を使用する場合
            if ($feeSetting->use_uniform_rate && $feeSetting->uniform_rate) {
                $fee = $feeSetting->uniform_rate * ($courseTime / 30);
                return $fee;
            }

            // 180分までは個別料金を使用
            if ($courseTime <= 180) {
                $feeColumn = 'fee_' . $courseTime;
                if (property_exists($feeSetting, $feeColumn) && $feeSetting->$feeColumn !== null) {
                    return $feeSetting->$feeColumn;
                }
            }

            // 210分以上は延長料金を上乗せ
            if ($courseTime >= 210) {
                $baseFee = 0;
                if (property_exists($feeSetting, 'fee_180') && $feeSetting->fee_180 !== null) {
                    $baseFee = $feeSetting->fee_180;
                }
                $extensionMinutes = $courseTime - 180;
                $extensionUnits = ceil($extensionMinutes / 30);
                $extensionFee = 0;
                if ($feeSetting->fee_extension !== null) {
                    $extensionFee = $feeSetting->fee_extension * $extensionUnits;
                }
                $totalFee = $baseFee + $extensionFee;
                return $totalFee;
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
