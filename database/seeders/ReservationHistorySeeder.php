<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Member;
use App\Models\Item;
use App\Models\Course;
use App\Models\CourseFee;
use Carbon\Carbon;

class ReservationHistorySeeder extends Seeder
{
    public function run()
    {
        // 会員、キャスト、コースのデータを取得
        $members = Member::all();
        $items = Item::all();
        $courses = Course::all();
        $courseFees = CourseFee::all();

        // 過去30日分の予約履歴を作成
        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // 各日付に3-5件の予約を作成
            $reservationCount = rand(3, 5);
            
            for ($j = 0; $j < $reservationCount; $j++) {
                $member = $members->random();
                $item = $items->random();
                $course = $courses->random();
                $courseFee = $courseFees->random();
                
                // ランダムな時間を生成（10:00-22:00の間）
                $startHour = rand(10, 21);
                $startMinute = rand(0, 3) * 15; // 15分単位
                $timeStart = Carbon::createFromTime($startHour, $startMinute);
                $timeEnd = $timeStart->copy()->addHours(rand(1, 3));
                
                // 料金を計算（コース料金 + オプション料金）
                $courseFeeAmount = $courseFee->fee ?? 0;
                $optionFeeAmount = rand(0, 3) * 1000; // 0-3000円のオプション料金
                $feeTotal = $courseFeeAmount + $optionFeeAmount;
                
                // 予約データを作成
                Reservation::create([
                    'member_id' => $member->id,
                    'name' => $member->name,
                    'tel' => $member->tel,
                    'date' => $date,
                    'time_start' => $timeStart,
                    'time_end' => $timeEnd,
                    'item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'course_id' => $course->id,
                    'course_time' => $courseFee->id,
                    'option' => json_encode([]),
                    'option_pay' => json_encode([]),
                    'memo' => 'シーダーで作成された予約履歴です。',
                    'memo_share' => '',
                    'area' => '東京都',
                    'place' => '新宿区',
                    'address' => '新宿1-1-1',
                    'fee_total' => $feeTotal,
                    'flag' => 4, // キャンセル済み
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
} 