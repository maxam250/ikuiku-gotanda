import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();


/**
 * FullCalendar
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import jaLocale from '@fullcalendar/core/locales/ja';
import googleCalendarPlugin from '@fullcalendar/google-calendar';

// カレンダーインスタンスを保存するグローバル変数
window.calendarInstances = {};

window.renderScheduleCalendar = function () {
    // 複数のカレンダー要素をサポート
    const calendarConfigs = [
        { id: 'calendar-current', initialDate: new Date() },
        { id: 'calendar-next', initialDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1) },
        { id: 'calendar-sp-current', initialDate: new Date() },
        { id: 'calendar-sp-next', initialDate: new Date() } // スマホ用は同じ月で初期化
    ];

    calendarConfigs.forEach(config => {
        const calendarEl = document.getElementById(config.id);
    if (!calendarEl) return;

    let selectedDateEl = null; // クリックされた日を保存する変数

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin, googleCalendarPlugin],
        initialView: 'dayGridMonth',
            initialDate: config.initialDate,
        locale: jaLocale,
        contentHeight: 'auto', // テーブルの縦幅を設定
            headerToolbar: false, // ヘッダーツールバーを非表示（カスタムナビゲーションを使用）
            height: 'auto',

        googleCalendarApiKey: 'AIzaSyBdMj5O9po4Kbjjr6nh48YK8hbsjVpuks8',  // ←Google APIキーが必要
        events: {
            googleCalendarId: 'ja.japanese#holiday@group.v.calendar.google.com',
            display: 'background', // 祝日を背景色で表示
            className: 'fc-holiday-bg', // 背景にクラスをつける（あとでCSSで色設定）
        },
        eventClick: function(info) {
            // Googleカレンダーリンク無効化
            if (info.event.source && info.event.source.googleCalendarId) {
                info.jsEvent.preventDefault(); // クリック無効にする
            }
        },
        dayCellContent: function(arg) {
            return { html: arg.dayNumberText.replace('日', '') };
        },
        dateClick: function(info) {
            const clickedDate = info.dateStr; // yyyy-mm-dd 形式で取れる
            const params = new URLSearchParams(window.location.search);
            const categoryId = params.get('category_id');
            const query = categoryId ? `?category_id=${categoryId}` : '';
            window.location.href = `/schedule/${clickedDate}${query}`;
            },
            datesSet: function(info) {
                // カレンダーの月が変更されたときに月表示を更新
                updateCalendarDisplay();
        }
        // dateClick: function (info) {
        //     const clickedDate = info.dateStr;

        //     // ① 前回選択した日付のクラスをリセット
        //     if (selectedDateEl) {
        //         selectedDateEl.classList.remove('fc-day-selected');
        //     }

        //     // ② 今回クリックした日付のセルを取得
        //     const currentDateCell = document.querySelector(`.fc-daygrid-day[data-date="${clickedDate}"]`);

        //     if (currentDateCell) {
        //         currentDateCell.classList.add('fc-day-selected');
        //         selectedDateEl = currentDateCell;
        //     }

        //     // ③ クリックされた日付の出勤者リスト取得
        //     fetch(`/schedule-data/${clickedDate}`)
        //         .then((response) => response.text())
        //         .then((html) => {
        //             document.getElementById('ListArea').innerHTML = html;
        //         });
        // },
    });

    calendar.render();
        
        // カレンダーインスタンスを保存
        window.calendarInstances[config.id] = calendar;
    });
};

// カレンダーの月変更関数
window.changeCalendarMonth = function(direction) {
    const calendarElements = [
        'calendar-current',
        'calendar-next', 
        'calendar-sp-current',
        'calendar-sp-next'
    ];

    calendarElements.forEach(elementId => {
        const calendar = window.calendarInstances[elementId];
        if (calendar) {
            if (direction > 0) {
                calendar.next();
            } else {
                calendar.prev();
            }
        }
    });
    
    // 月表示の更新
    updateCalendarDisplay();
};

// 月表示の更新関数
window.updateCalendarDisplay = function() {
    // PC用表示更新
    const currentCalendar = window.calendarInstances['calendar-current'];
    const nextCalendar = window.calendarInstances['calendar-next'];
    
    if (currentCalendar && nextCalendar) {
        const currentDate = currentCalendar.getDate();
        const nextDate = nextCalendar.getDate();
        
        const currentMonthStr = currentDate.toLocaleDateString('ja-JP', { year: 'numeric', month: 'long' });
        const nextMonthStr = nextDate.toLocaleDateString('ja-JP', { year: 'numeric', month: 'long' });
        
        const currentMonthPc = document.getElementById('currentMonthPc');
        const nextMonthPc = document.getElementById('nextMonthPc');
        if (currentMonthPc) currentMonthPc.textContent = currentMonthStr;
        if (nextMonthPc) nextMonthPc.textContent = nextMonthStr;
    }
    
    // SP用表示更新
    const spCurrentCalendar = window.calendarInstances['calendar-sp-current'];
    if (spCurrentCalendar) {
        const currentDate = spCurrentCalendar.getDate();
        const currentMonthStr = currentDate.toLocaleDateString('ja-JP', { year: 'numeric', month: 'long' });
        
        const currentMonthSp = document.getElementById('currentMonthSp');
        if (currentMonthSp) currentMonthSp.textContent = currentMonthStr;
    }
};

/**
 * flatpickr
*/
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Japanese } from "flatpickr/dist/l10n/ja.js";

// DOM読み込み後にflatpickrを共通初期化
document.addEventListener("DOMContentLoaded", function () {
    // date: 日付入力
    document.querySelectorAll('input[type="date"]:not([data-picker-ignore])').forEach((el) => {
        flatpickr(el, {
            dateFormat: "Y-m-d",
            allowInput: true,
            locale: Japanese,
            disableMobile: true,
        });
    });

    // datetime-local: 日時入力
    document.querySelectorAll('input[type="datetime-local"]:not([data-picker-ignore])').forEach((el) => {
        flatpickr(el, {
            enableTime: true,
            time_24hr: true,
            dateFormat: "Y-m-d\\TH:i",
            altInput: true,
            altFormat: "Y-m-d H:i",
            allowInput: true,
            locale: Japanese,
            disableMobile: true,
        });
    });

    // time: 時刻入力
    document.querySelectorAll('input[type="time"]:not([data-picker-ignore])').forEach((el) => {
        flatpickr(el, {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            dateFormat: "H:i",
            allowInput: true,
            locale: Japanese,
            disableMobile: true,
        });
    });
});
