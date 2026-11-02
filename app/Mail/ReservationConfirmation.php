<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\ExternalReservation;
use App\Services\ExternalDatabaseService;
use App\Models\Shop;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;
use App\Models\CourseCate;
use App\Models\Course;
use App\Models\CourseFee;
use App\Models\Equipment;
use App\Models\Costume;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $shopEmail;
    public $isShop;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation, $shopEmail, $isShop = false)
    {
        // 外部DBが有効な場合はExternalReservation、そうでなければReservationを使用
        $reservationModel = ExternalDatabaseService::getReservationModel();
        
        // 渡された$reservationが適切なモデルのインスタンスかチェック
        if (!$reservation instanceof $reservationModel) {
            throw new \InvalidArgumentException('Invalid reservation model type');
        }
        
        $this->reservation = $reservation;
        $this->shopEmail = $shopEmail;
        $this->isShop = $isShop;
    }

    public function build()
    {
        $shops = Shop::where('shop_active', '=', '1')->get();
        $items = Item::orderBy('item_name', 'asc')->get();
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $courses = Course::orderBy('sort', 'asc')->get();
        $course_fees = CourseFee::orderBy('sort', 'asc')->get();
        $equipments = Equipment::orderBy('sort', 'asc')->get();
        $costumes = Costume::where('active', 1)->orderBy('sort', 'asc')->get();

        return $this->view('emails.reservation_confirmation', compact('items', 'options', 'option_pays', 'courses' ,'course_fees', 'shops','equipments','costumes'))
                    ->subject('予約確認');
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // システム内で唯一の店舗情報を取得
        $shop = Shop::first();
        $shopName = $shop?->shop_name ?? '';

        return new Envelope(
            from: new Address($this->shopEmail, $shopName),
            subject: '【'.$shopName.'】 予約申し込み自動返信',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
