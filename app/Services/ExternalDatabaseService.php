<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ExternalReservation;
use App\Models\Member;
use App\Models\ExternalMember;
use App\Models\Item;
use App\Models\ExternalItem;
use App\Models\Course;
use App\Models\ExternalCourse;
use App\Models\Equipment;
use App\Models\ExternalEquipment;
use App\Models\Schedule;
use App\Models\ExternalSchedule;
use App\Models\Staff;
use App\Models\ExternalStaff;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExternalDatabaseService
{
    /**
     * 外部サーバー接続が有効かどうかを確認
     */
    public static function isExternalEnabled(): bool
    {
        return env('USE_EXTERNAL_DATABASE', false) === true;
    }

    /**
     * 外部サーバー接続をテスト
     * 外部DBが無効な場合はfalseを返す（接続を試みない）
     * 結果は30秒間キャッシュされる
     */
    public static function testExternalConnection(): bool
    {
        // 外部DBが無効な場合は接続を試みない
        if (!self::isExternalEnabled()) {
            return false;
        }
        
        // キャッシュから取得（30秒間キャッシュ）
        $cacheKey = 'external_db_connection_test';
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        try {
            DB::connection('external_mysql')->getPdo();
            $result = true;
        } catch (\Exception $e) {
            \Log::debug('External DB connection test failed', [
                'error' => $e->getMessage()
            ]);
            $result = false;
        }
        
        // 結果をキャッシュ（30秒間）
        Cache::put($cacheKey, $result, 30);
        
        return $result;
    }

    /**
     * 適切なReservationモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getReservationModel()
    {
        return Reservation::class;
    }

    /**
     * 適切なMemberモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getMemberModel()
    {
        return Member::class;
    }

    /**
     * 適切なItemモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getItemModel()
    {
        return Item::class;
    }

    /**
     * 適切なCourseモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getCourseModel()
    {
        return Course::class;
    }

    /**
     * 適切なEquipmentモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getEquipmentModel()
    {
        return Equipment::class;
    }

    /**
     * 適切なScheduleモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getScheduleModel()
    {
        return Schedule::class;
    }

    /**
     * 適切なStaffモデルを取得
     * 常にローカルモデルを返す（外部DBは使用しない）
     */
    public static function getStaffModel()
    {
        return Staff::class;
    }

    /**
     * 予約を作成（外部サーバーまたはローカルサーバー）
     */
    public static function createReservation(array $data)
    {
        $model = self::getReservationModel();
        return $model::create($data);
    }

    /**
     * 予約を更新（外部サーバーまたはローカルサーバー）
     */
    public static function updateReservation($id, array $data)
    {
        $model = self::getReservationModel();
        $reservation = $model::findOrFail($id);
        $reservation->update($data);
        return $reservation;
    }

    /**
     * 予約を削除（外部サーバーまたはローカルサーバー）
     */
    public static function deleteReservation($id)
    {
        $model = self::getReservationModel();
        $reservation = $model::findOrFail($id);
        return $reservation->delete();
    }

    /**
     * 予約を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getReservation($id)
    {
        $model = self::getReservationModel();
        return $model::find($id);
    }

    /**
     * 予約一覧を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getReservations($conditions = [])
    {
        $model = self::getReservationModel();
        $query = $model::query();

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                // 配列の場合は演算子を指定
                $operator = $value[0];
                $val = $value[1];
                $query->where($key, $operator, $val);
            } else {
                // 単純な値の場合は等価比較
                $query->where($key, $value);
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * 会員情報を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getMember($id)
    {
        $model = self::getMemberModel();
        return $model::find($id);
    }

    /**
     * アイテム情報を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getItem($id)
    {
        $model = self::getItemModel();
        return $model::find($id);
    }

    /**
     * コース情報を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getCourse($id)
    {
        $model = self::getCourseModel();
        return $model::find($id);
    }

    /**
     * 機器情報を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getEquipment($id)
    {
        $model = self::getEquipmentModel();
        return $model::find($id);
    }

    /**
     * スケジュール情報を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getSchedule($id)
    {
        $model = self::getScheduleModel();
        return $model::find($id);
    }

    /**
     * 機器を作成（外部サーバーまたはローカルサーバー）
     */
    public static function createEquipment(array $data)
    {
        $model = self::getEquipmentModel();
        return $model::create($data);
    }

    /**
     * 機器を更新（外部サーバーまたはローカルサーバー）
     */
    public static function updateEquipment($id, array $data)
    {
        $model = self::getEquipmentModel();
        $equipment = $model::findOrFail($id);
        $equipment->update($data);
        return $equipment;
    }

    /**
     * 機器を削除（外部サーバーまたはローカルサーバー）
     */
    public static function deleteEquipment($id)
    {
        $model = self::getEquipmentModel();
        $equipment = $model::findOrFail($id);
        return $equipment->delete();
    }

    /**
     * 機器一覧を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getEquipments($conditions = [])
    {
        $model = self::getEquipmentModel();
        $query = $model::query();

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                // 配列の場合は演算子を指定
                $operator = $value[0];
                $val = $value[1];
                $query->where($key, $operator, $val);
            } else {
                // 単純な値の場合は等価比較
                $query->where($key, $value);
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * 会員の予約情報を詳細に取得（外部サーバーまたはローカルサーバー）
     */
    public static function getMemberReservations($memberId, $limit = 10)
    {
        if (self::isExternalEnabled()) {
            // 外部DBの場合：reservationsテーブルから直接取得
            return self::getExternalMemberReservations($memberId, $limit);
        } else {
            // 内部DBの場合：従来の処理
            return self::getInternalMemberReservations($memberId, $limit);
        }
    }

    /**
     * 特定の会員の今日以降の予約を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getFutureReservations($memberId)
    {
        if (self::isExternalEnabled()) {
            // 外部DBの場合：reservationsテーブルから直接取得
            return self::getExternalFutureReservations($memberId);
        } else {
            // 内部DBの場合：従来の処理
            return self::getInternalFutureReservations($memberId);
        }
    }

    /**
     * 特定のキャストの今日以降の予約を取得（外部サーバーまたはローカルサーバー）
     */
    public static function getCastFutureReservations($itemId)
    {
        if (self::isExternalEnabled()) {
            // 外部DBの場合：reservationsテーブルから直接取得
            return self::getExternalCastFutureReservations($itemId);
        } else {
            // 内部DBの場合：従来の処理
            return self::getInternalCastFutureReservations($itemId);
        }
    }

    /**
     * 特定の非会員（メールアドレス）の今日以降の予約を取得
     */
    public static function getGuestFutureReservations($email)
    {
        if (self::isExternalEnabled()) {
            // 外部DBの場合：reservationsテーブルから直接取得
            return self::getExternalGuestFutureReservations($email);
        } else {
            // 内部DBの場合：従来の処理
            return self::getInternalGuestFutureReservations($email);
        }
    }

    /**
     * 外部DBから会員の予約情報を取得
     */
    private static function getExternalMemberReservations($memberId, $limit = 10)
    {
        $reservationModel = self::getReservationModel();

        $reservations = $reservationModel::where('member_id', $memberId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        return $reservations->map(function($reservation) {
            // 外部DBのreservationsテーブルから直接会員情報を取得
            $memberName = $reservation->name ?? '不明';
            $memberEmail = $reservation->email ?? '';

            // アイテム情報を取得（ローカルDBから取得）
            $item = \App\Models\Item::find($reservation->item_id);
            $castName = $item ? $item->item_name : ($reservation->item_name ?? '');

            // コース情報を取得（ローカルDBから取得）
            $course = \App\Models\Course::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // スタジオ情報を取得（外部DBから取得）
            $studio = '';
            if ($reservation->equipment_id) {
                // 外部DBのequipmentsテーブルから直接取得
                $externalEquipment = \App\Models\ExternalEquipment::find($reservation->equipment_id);
                if ($externalEquipment) {
                    $studio = $externalEquipment->name ?? '';
                } else {
                    // 外部DBにない場合はローカルDBから取得
                    $equipment = \App\Models\Equipment::find($reservation->equipment_id);
                    $studio = $equipment ? $equipment->title : '';
                }
            }

            // オプション情報の処理（ローカルDBから取得）
            $optionNames = [];
            if ($reservation->options) {
                $optionIds = is_string($reservation->options) ?
                    json_decode($reservation->options, true) :
                    $reservation->options;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理（ローカルDBから取得）
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理（ローカルDBから取得）
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;

            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course ?? 0),
                'fee_option' => number_format($reservation->fee_option ?? 0),
                'fee_myoption' => number_format($reservation->fee_myoption ?? 0),
                'fee_mycostume' => number_format($reservation->fee_mycostume ?? 0),
                'fee_adjust' => number_format($reservation->fee_adjust ?? 0),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total ?? 0). '円',
                'fee_total_tax' => '税込 '. number_format(($reservation->fee_total ?? 0) * config('tax.intax')). '円'
            ];
        });
    }

    /**
     * 外部DBから特定の会員の今日以降の予約を取得
     */
    private static function getExternalFutureReservations($memberId)
    {
        $reservationModel = self::getReservationModel();
        $today = now()->format('Y-m-d');

        $reservations = $reservationModel::where('member_id', $memberId)
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $reservations->map(function($reservation) {
            // 外部DBのreservationsテーブルから直接会員情報を取得
            $memberName = $reservation->name ?? '不明';
            $memberEmail = $reservation->email ?? '';

            // アイテム情報を取得（ローカルDBから取得）
            $item = \App\Models\Item::find($reservation->item_id);
            $castName = $item ? $item->item_name : ($reservation->item_name ?? '');

            // コース情報を取得（ローカルDBから取得）
            $course = \App\Models\Course::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得（外部DBから取得）
            $studio = '';
            if ($reservation->equipment_id) {
                // 外部DBのequipmentsテーブルから直接取得
                $externalEquipment = \App\Models\ExternalEquipment::find($reservation->equipment_id);
                if ($externalEquipment) {
                    $studio = $externalEquipment->title ?? '';
                } else {
                    // 外部DBにない場合はローカルDBから取得
                    $equipment = \App\Models\Equipment::find($reservation->equipment_id);
                    $studio = $equipment ? $equipment->title : '';
                }
            }

            // オプション情報の処理（ローカルDBから取得）
            $optionNames = [];
            if ($reservation->options) {
                $optionIds = is_string($reservation->options) ?
                    json_decode($reservation->options, true) :
                    $reservation->options;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理（ローカルDBから取得）
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理（ローカルDBから取得）
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;

            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_id' => $reservation->member_id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course ?? 0),
                'fee_option' => number_format($reservation->fee_option ?? 0),
                'fee_myoption' => number_format($reservation->fee_myoption ?? 0),
                'fee_mycostume' => number_format($reservation->fee_mycostume ?? 0),
                'fee_adjust' => number_format($reservation->fee_adjust ?? 0),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total ?? 0). '円',
                'fee_total_tax' => '税込 '.number_format(($reservation->fee_total ?? 0) * config('tax.intax')). '円'
            ];
        });
    }

    /**
     * 外部DBから特定のキャストの今日以降の予約を取得
     */
    private static function getExternalCastFutureReservations($itemId)
    {
        $reservationModel = self::getReservationModel();
        $today = now()->format('Y-m-d');

        $reservations = $reservationModel::where('item_id', $itemId)
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();

        return $reservations->map(function($reservation) {
            // 外部DBのreservationsテーブルから直接会員情報を取得
            $memberName = $reservation->name ?? '不明';
            $memberEmail = $reservation->email ?? '';

            // アイテム情報を取得（ローカルDBから取得）
            $item = \App\Models\Item::find($reservation->item_id);
            $castName = $item ? $item->item_name : ($reservation->item_name ?? '');

            // コース情報を取得（ローカルDBから取得）
            $course = \App\Models\Course::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得（外部DBから取得）
            $studio = '';
            if ($reservation->equipment_id) {
                // 外部DBのequipmentsテーブルから直接取得
                $externalEquipment = \App\Models\ExternalEquipment::find($reservation->equipment_id);
                if ($externalEquipment) {
                    $studio = $externalEquipment->title ?? '';
                } else {
                    // 外部DBにない場合はローカルDBから取得
                    $equipment = \App\Models\Equipment::find($reservation->equipment_id);
                    $studio = $equipment ? $equipment->title : '';
                }
            }

            // オプション情報の処理（ローカルDBから取得）
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_string($reservation->option) ?
                    json_decode($reservation->option, true) :
                    $reservation->option;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理（ローカルDBから取得）
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理（ローカルDBから取得）
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;

            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_id' => $reservation->member_id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course ?? 0),
                'fee_option' => number_format($reservation->fee_option ?? 0),
                'fee_myoption' => number_format($reservation->fee_myoption ?? 0),
                'fee_mycostume' => number_format($reservation->fee_mycostume ?? 0),
                'fee_adjust' => number_format($reservation->fee_adjust ?? 0),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total ?? 0),
                'fee_total_tax' => '税込 '. number_format(($reservation->fee_total ?? 0) * config('tax.intax')). '円'
            ];
        });
    }

    /**
     * 内部DBから特定会員の今日以降の予約を取得
     */
    private static function getInternalFutureReservations($memberId, $limit = 50)
    {
        $reservationModel = self::getReservationModel();
        $memberModel = self::getMemberModel();
        $itemModel = self::getItemModel();
        $courseModel = self::getCourseModel();
        $equipmentModel = self::getEquipmentModel();
        $today = now()->format('Y-m-d');

        $reservations = $reservationModel::where('member_id', $memberId)
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->take($limit)
            ->get();

        return $reservations->map(function($reservation) use ($memberModel, $itemModel, $courseModel, $equipmentModel) {
            // 会員情報を取得
            $member = $memberModel::find($reservation->member_id);
            $memberName = '不明';
            $memberEmail = '';

            if ($member) {
                try {
                    $memberName = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('name'));
                    $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('email'));
                } catch (\Exception $e) {
                    \Log::error('Member decryption error: ' . $e->getMessage());
                    try {
                        $memberName = $member->name;
                        $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->email);
                    } catch (\Exception $e2) {
                        \Log::error('Secondary member decryption error: ' . $e2->getMessage());
                    }
                }
            }

            // アイテム情報を取得
            $item = $itemModel::find($reservation->item_id);
            $castName = $item ? $item->item_name : '';

            // コース情報を取得
            $course = $courseModel::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得
            $equipment = $equipmentModel::find($reservation->equipment_id);
            $studio = $equipment ? $equipment->title : '';

            // オプション情報の処理
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_string($reservation->option) ?
                    json_decode($reservation->option, true) :
                    $reservation->option;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;
            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_id' => $reservation->member_id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course),
                'fee_option' => number_format($reservation->fee_option),
                'fee_myoption' => number_format($reservation->fee_myoption),
                'fee_mycostume' => number_format($reservation->fee_mycostume),
                'fee_adjust' => number_format($reservation->fee_adjust),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total),
                'fee_total_tax' => number_format($reservation->fee_total * config('tax.intax'))
            ];
        });
    }

    /**
     * 内部DBから会員の予約情報を取得
     */
    private static function getInternalMemberReservations($memberId, $limit = 10)
    {
        $reservationModel = self::getReservationModel();
        $memberModel = self::getMemberModel();
        $itemModel = self::getItemModel();
        $courseModel = self::getCourseModel();
        $equipmentModel = self::getEquipmentModel();

        $reservations = $reservationModel::where('member_id', $memberId)
            ->orderBy('date', 'desc')
            ->orderBy('time_start', 'desc')
            ->take($limit)
            ->get();

        return $reservations->map(function($reservation) use ($memberModel, $itemModel, $courseModel, $equipmentModel) {
            // 会員情報を取得
            $member = $memberModel::find($reservation->member_id);
            $memberName = '不明';
            $memberEmail = '';

            if ($member) {
                try {
                    $memberName = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('name'));
                    $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('email'));
                } catch (\Exception $e) {
                    \Log::error('Member decryption error: ' . $e->getMessage());
                    try {
                        $memberName = $member->name;
                        $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->email);
                    } catch (\Exception $e2) {
                        \Log::error('Secondary member decryption error: ' . $e2->getMessage());
                    }
                }
            }

            // アイテム情報を取得
            $item = $itemModel::find($reservation->item_id);
            $castName = $item ? $item->item_name : '';

            // コース情報を取得
            $course = $courseModel::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得
            $equipment = $equipmentModel::find($reservation->equipment_id);
            $studio = $equipment ? $equipment->title : '';

            // オプション情報の処理
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_string($reservation->option) ?
                    json_decode($reservation->option, true) :
                    $reservation->option;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;
            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course),
                'fee_option' => number_format($reservation->fee_option),
                'fee_myoption' => number_format($reservation->fee_myoption),
                'fee_mycostume' => number_format($reservation->fee_mycostume),
                'fee_adjust' => number_format($reservation->fee_adjust),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total),
                'fee_total_tax' => number_format($reservation->fee_total * config('tax.intax'))
            ];
        });
    }

    /**
     * 外部DBから特定の非会員（メールアドレス）の今日以降の予約を取得
     */
    private static function getExternalGuestFutureReservations($email)
    {
        $reservationModel = self::getReservationModel();
        $today = now()->format('Y-m-d');

        // 暗号化されたメールアドレスで検索
        $reservations = $reservationModel::where('member_id', 0)
            ->where('date', '>=', $today)
            ->get()
            ->filter(function ($reservation) use ($email) {
                try {
                    return \Illuminate\Support\Facades\Crypt::decryptString($reservation->getRawOriginal('email')) === $email;
                } catch (\Exception $e) {
                    return $reservation->getRawOriginal('email') === $email;
                }
            })
            ->sortBy('date')
            ->sortBy('time_start');

        return $reservations->map(function($reservation) {
            // 非会員の場合、予約テーブルから直接名前とメールを取得
            $memberName = $reservation->name ?? '不明';
            try {
                $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($reservation->getRawOriginal('email'));
            } catch (\Exception $e) {
                $memberEmail = $reservation->getRawOriginal('email') ?? '';
            }

            // アイテム情報を取得（ローカルDBから取得）
            $item = \App\Models\Item::find($reservation->item_id);
            $castName = $item ? $item->item_name : ($reservation->item_name ?? '');

            // コース情報を取得（ローカルDBから取得）
            $course = \App\Models\Course::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得
            $studio = '';
            if ($reservation->equipment_id) {
                $externalEquipment = \App\Models\ExternalEquipment::find($reservation->equipment_id);
                if ($externalEquipment) {
                    $studio = $externalEquipment->title ?? '';
                } else {
                    $equipment = \App\Models\Equipment::find($reservation->equipment_id);
                    $studio = $equipment ? $equipment->title : '';
                }
            }

            // 日時の処理
            $date = $reservation->date ? $reservation->date->format('Y/m/d') : '';
            $timeStart = $reservation->time_start ? $reservation->time_start->format('H:i') : '';
            $timeEnd = $reservation->time_end ? $reservation->time_end->format('H:i') : '';
            $time = $timeStart && $timeEnd ? $timeStart . '-' . $timeEnd : '';

            $datetimeForParse = $reservation->date && $reservation->time_start
                ? $reservation->date->format('Y-m-d') . ' ' . $reservation->time_start->format('H:i:s')
                : '';

            $date_mdw = $reservation->date
                ? \Carbon\Carbon::parse($reservation->date)->isoFormat('MM月DD日(ddd) ')
                : '';

            // オプション情報の処理（ローカルDBから取得）
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_array($reservation->option) ? $reservation->option : json_decode($reservation->option, true);
                if ($optionIds) {
                    foreach ($optionIds as $optionId) {
                        $option = \App\Models\Option::find($optionId);
                        if ($option) {
                            $optionNames[] = $option->title;
                        }
                    }
                }
            }

            // オプション料金情報の処理
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_array($reservation->option_pay) ? $reservation->option_pay : json_decode($reservation->option_pay, true);
                if ($optionPayIds) {
                    foreach ($optionPayIds as $optionPayId) {
                        $optionPay = \App\Models\OptionPay::find($optionPayId);
                        if ($optionPay) {
                            $optionPayNames[] = $optionPay->title;
                        }
                    }
                }
            }

            // コスチューム情報の処理
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_array($reservation->costume) ? $reservation->costume : json_decode($reservation->costume, true);
                if ($costumeIds) {
                    foreach ($costumeIds as $costumeId) {
                        $costume = \App\Models\Costume::find($costumeId);
                        if ($costume) {
                            $costumeNames[] = $costume->title;
                        }
                    }
                }
            }

            return [
                'id' => $reservation->id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => $reservation->myoption ?? '（なし）',
                'mycostumes' => $reservation->mycostume ?? '（なし）',
                'fee_course' => $reservation->fee_course ?? 0,
                'fee_option' => $reservation->fee_option ?? 0,
                'fee_myoption' => $reservation->fee_myoption ?? 0,
                'fee_mycostume' => $reservation->fee_mycostume ?? 0,
                'fee_adjust' => $reservation->fee_adjust ?? 0,
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => $reservation->fee_total ?? 0,
                'fee_total_tax' => ($reservation->fee_total ?? 0) * config('tax.intax', 1.1)
            ];
        });
    }

    /**
     * 内部DBから特定の非会員（メールアドレス）の今日以降の予約を取得
     */
    private static function getInternalGuestFutureReservations($email)
    {
        $reservationModel = self::getReservationModel();
        $today = now()->format('Y-m-d');

        // 暗号化されたメールアドレスで検索
        $reservations = $reservationModel::where('member_id', 0)
            ->where('date', '>=', $today)
            ->get()
            ->filter(function ($reservation) use ($email) {
                try {
                    return \Illuminate\Support\Facades\Crypt::decryptString($reservation->getRawOriginal('email')) === $email;
                } catch (\Exception $e) {
                    return $reservation->getRawOriginal('email') === $email;
                }
            })
            ->sortBy('date')
            ->sortBy('time_start');

        return $reservations->map(function($reservation) {
            // 非会員の場合、予約テーブルから直接名前とメールを取得
            $memberName = $reservation->name ?? '不明';
            try {
                $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($reservation->getRawOriginal('email'));
            } catch (\Exception $e) {
                $memberEmail = $reservation->getRawOriginal('email') ?? '';
            }

            // アイテム情報を取得
            $item = \App\Models\Item::find($reservation->item_id);
            $castName = $item ? $item->item_name : ($reservation->item_name ?? '');

            // コース情報を取得
            $course = \App\Models\Course::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得
            $equipment = \App\Models\Equipment::find($reservation->equipment_id);
            $studio = $equipment ? $equipment->title : '';

            // 日時の処理
            $date = $reservation->date ? $reservation->date->format('Y/m/d') : '';
            $timeStart = $reservation->time_start ? $reservation->time_start->format('H:i') : '';
            $timeEnd = $reservation->time_end ? $reservation->time_end->format('H:i') : '';
            $time = $timeStart && $timeEnd ? $timeStart . '-' . $timeEnd : '';

            $datetimeForParse = $reservation->date && $reservation->time_start
                ? $reservation->date->format('Y-m-d') . ' ' . $reservation->time_start->format('H:i:s')
                : '';

            $date_mdw = $reservation->date
                ? \Carbon\Carbon::parse($reservation->date)->isoFormat('MM月DD日(ddd) ')
                : '';

            // オプション情報の処理
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_array($reservation->option) ? $reservation->option : json_decode($reservation->option, true);
                if ($optionIds) {
                    foreach ($optionIds as $optionId) {
                        $option = \App\Models\Option::find($optionId);
                        if ($option) {
                            $optionNames[] = $option->title;
                        }
                    }
                }
            }

            // オプション料金情報の処理
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_array($reservation->option_pay) ? $reservation->option_pay : json_decode($reservation->option_pay, true);
                if ($optionPayIds) {
                    foreach ($optionPayIds as $optionPayId) {
                        $optionPay = \App\Models\OptionPay::find($optionPayId);
                        if ($optionPay) {
                            $optionPayNames[] = $optionPay->title;
                        }
                    }
                }
            }

            // コスチューム情報の処理
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_array($reservation->costume) ? $reservation->costume : json_decode($reservation->costume, true);
                if ($costumeIds) {
                    foreach ($costumeIds as $costumeId) {
                        $costume = \App\Models\Costume::find($costumeId);
                        if ($costume) {
                            $costumeNames[] = $costume->title;
                        }
                    }
                }
            }

            return [
                'id' => $reservation->id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => $reservation->myoption ?? '（なし）',
                'mycostumes' => $reservation->mycostume ?? '（なし）',
                'fee_course' => $reservation->fee_course ?? 0,
                'fee_option' => $reservation->fee_option ?? 0,
                'fee_myoption' => $reservation->fee_myoption ?? 0,
                'fee_mycostume' => $reservation->fee_mycostume ?? 0,
                'fee_adjust' => $reservation->fee_adjust ?? 0,
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => $reservation->fee_total ?? 0,
                'fee_total_tax' => ($reservation->fee_total ?? 0) * config('tax.intax', 1.1)
            ];
        });
    }

    /**
     * 内部DBから特定のキャストの今日以降の予約を取得
     */
    private static function getInternalCastFutureReservations($itemId)
    {
        $reservationModel = self::getReservationModel();
        $memberModel = self::getMemberModel();
        $itemModel = self::getItemModel();
        $courseModel = self::getCourseModel();
        $equipmentModel = self::getEquipmentModel();
        $today = now()->format('Y-m-d');

        $reservations = $reservationModel::where('item_id', $itemId)
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();

        return $reservations->map(function($reservation) use ($memberModel, $itemModel, $courseModel, $equipmentModel) {
            // 会員情報を取得
            $member = $memberModel::find($reservation->member_id);
            $memberName = '不明';
            $memberEmail = '';

            if ($member) {
                try {
                    $memberName = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('name'));
                    $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('email'));
                } catch (\Exception $e) {
                    \Log::error('Member decryption error: ' . $e->getMessage());
                    try {
                        $memberName = $member->name;
                        $memberEmail = \Illuminate\Support\Facades\Crypt::decryptString($member->email);
                    } catch (\Exception $e2) {
                        \Log::error('Secondary member decryption error: ' . $e2->getMessage());
                    }
                }
            }

            // アイテム情報を取得
            $item = $itemModel::find($reservation->item_id);
            $castName = $item ? $item->item_name : '';

            // コース情報を取得
            $course = $courseModel::find($reservation->course_id);
            $courseName = $course ? $course->title : '';
            $courseTime = !empty($reservation->course_time) ? $reservation->course_time . '分' : '';
            $courseInfo = trim($courseName . ($courseTime ? '（' . $courseTime . '）' : ''));

            // 機器情報を取得
            $equipment = $equipmentModel::find($reservation->equipment_id);
            $studio = $equipment ? $equipment->title : '';

            // オプション情報の処理
            $optionNames = [];
            if ($reservation->option) {
                $optionIds = is_string($reservation->option) ?
                    json_decode($reservation->option, true) :
                    $reservation->option;

                if (is_array($optionIds)) {
                    $options = \App\Models\Option::whereIn('id', $optionIds)->get();
                    $optionNames = $options->pluck('title')->toArray();
                }
            }

            // 有料オプション情報の処理
            $optionPayNames = [];
            if ($reservation->option_pay) {
                $optionPayIds = is_string($reservation->option_pay) ?
                    json_decode($reservation->option_pay, true) :
                    $reservation->option_pay;

                if (is_array($optionPayIds)) {
                    $optionPays = \App\Models\OptionPay::whereIn('id', $optionPayIds)->get();
                    $optionPayNames = $optionPays->pluck('title')->toArray();
                }
            }

            // 衣装情報の処理
            $costumeNames = [];
            if ($reservation->costume) {
                $costumeIds = is_string($reservation->costume) ?
                    json_decode($reservation->costume, true) :
                    $reservation->costume;

                if (is_array($costumeIds)) {
                    $costumes = \App\Models\Costume::whereIn('id', $costumeIds)->get();
                    $costumeNames = $costumes->pluck('name')->toArray();
                }
            }

            // マイオプション情報の取得
            $myoptions = [];
            if ($reservation->myoption && !empty($reservation->myoption)) {
                try {
                    $myoptionData = null;
                    if (is_string($reservation->myoption)) {
                        $decoded = json_decode($reservation->myoption, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $myoptionData = $decoded;
                        }
                    } else {
                        $myoptionData = $reservation->myoption;
                    }

                    if (is_array($myoptionData)) {
                        foreach ($myoptionData as $option) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($option)) {
                                // IDからマイオプション情報を取得
                                $myoptionModel = \App\Models\Myoption::find($option);
                                if ($myoptionModel) {
                                    $myoptions[] = $myoptionModel->name . ': ' . number_format($myoptionModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $option['name'] ?? $option['title'] ?? $option['option_name'] ?? '';
                                $price = $option['price'] ?? $option['amount'] ?? $option['cost'] ?? 0;

                                if (!empty($name) && $price > 0) {
                                    $myoptions[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Myoption parse error: ' . $e->getMessage());
                }
            }

            // マイコスチューム情報の取得
            $mycostumes = [];
            if ($reservation->mycostume && !empty($reservation->mycostume)) {
                try {
                    $mycostumeData = null;
                    if (is_string($reservation->mycostume)) {
                        $decoded = json_decode($reservation->mycostume, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $mycostumeData = $decoded;
                        }
                    } else {
                        $mycostumeData = $reservation->mycostume;
                    }

                    if (is_array($mycostumeData)) {
                        foreach ($mycostumeData as $costume) {
                            // ID配列の場合（数値のみの場合）
                            if (is_numeric($costume)) {
                                // IDからマイコスチューム情報を取得
                                $mycostumeModel = \App\Models\Mycostume::find($costume);
                                if ($mycostumeModel) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $mycostumeModel->name;
                                    // $mycostumes[] = $mycostumeModel->name . ': ' . number_format($mycostumeModel->price) . '円';
                                }
                            } else {
                                // オブジェクト配列の場合（従来の処理）
                                $name = $costume['name'] ?? $costume['title'] ?? $costume['costume_name'] ?? '';
                                // $price = $costume['price'] ?? $costume['amount'] ?? $costume['cost'] ?? 0;

                                if (!empty($name)) {
                                    // マイコスチュームは料金表示なし（名前のみ）
                                    $mycostumes[] = $name;
                                    // $mycostumes[] = $name . ': ' . number_format($price) . '円';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Mycostume parse error: ' . $e->getMessage());
                }
            }

            // 日付と時間を適切にフォーマット
            $reservationDate = $reservation->date ?? $reservation->created_at;
            $date = \Carbon\Carbon::parse($reservationDate)->isoFormat('YYYY年MM月DD日(ddd) / ');
            $date_mdw = \Carbon\Carbon::parse($reservationDate)->isoFormat('MM月DD日(ddd) ');
            $time = \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i');
            $datetimeForParse = \Carbon\Carbon::parse($reservationDate)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($reservation->time_start ?? $reservation->created_at)->format('H:i:s');

            return [
                'id' => $reservation->id,
                'member_id' => $reservation->member_id,
                'member_name' => $memberName,
                'member_email' => $memberEmail,
                'reservation_datetime' => $date . ' ' . $time,
                'reservation_datetime_parse' => $datetimeForParse,
                'reservation_date' => $date,
                'reservation_date_mdw' => $date_mdw,
                'reservation_time' => $time,
                'course_info' => $courseInfo,
                'course_name' => $courseName,
                'course_time' => $courseTime,
                'cast_name' => $castName,
                'options' => implode('、', $optionNames),
                'option_pays' => implode('、', $optionPayNames),
                'studio' => $studio,
                'costumes' => implode('、', $costumeNames),
                'area' => $reservation->area ?? '',
                'place' => $reservation->place ?? '',
                'meet' => $reservation->meet ?? '',
                'address' => $reservation->address ?? '',
                'comment' => $reservation->comment ?? '',
                'memo' => $reservation->memo ?? '',
                'memo_share' => $reservation->memo_share ?? '',
                'myoptions' => empty($myoptions) ? '（なし）' : implode("\n", $myoptions),
                'mycostumes' => empty($mycostumes) ? '（なし）' : implode("\n", $mycostumes),
                'fee_course' => number_format($reservation->fee_course),
                'fee_option' => number_format($reservation->fee_option),
                'fee_myoption' => number_format($reservation->fee_myoption),
                'fee_mycostume' => number_format($reservation->fee_mycostume),
                'fee_adjust' => number_format($reservation->fee_adjust),
                'fee_adjust_memo' => $reservation->fee_adjust_memo ?? '',
                'fee_total' => number_format($reservation->fee_total),
                'fee_total_tax' => number_format($reservation->fee_total * config('tax.intax'))
            ];
        });
    }
}
