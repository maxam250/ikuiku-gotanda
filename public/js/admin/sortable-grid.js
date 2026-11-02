/**
 * 管理画面：カード一覧（.sortable-list > li）のドラッグ並び替え（SortableJS）
 *
 * jQuery UI sortable は折り返しのあるカード一覧だと位置判定が揺れて「ふわふわ」するため、
 * SortableJS で他のカードがアニメーションで滑らかに入れ替わるようにする。
 *
 * 使い方:
 *   initSortableGrid({
 *       selector: '.sortable-list',      // 並び替える ul
 *       idAttr:   'schedule-id',         // li の data-* 属性名（data-schedule-id）
 *       paramName: 'scheduleOrder',      // サーバーへ送る配列名 [{id, sort}, ...]
 *       url:      '/kanrigamen/schedules/sort',
 *       token:    'CSRFトークン',
 *       onSaved:  function () {},        // 保存成功時（任意）
 *   });
 */
(function (window) {
    'use strict';

    function initSortableGrid(opts) {
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js が読み込まれていません');
            return;
        }

        document.querySelectorAll(opts.selector).forEach(function (list) {
            Sortable.create(list, {
                animation: 150,                 // 入れ替わるカードを滑らかに移動
                easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                direction: 'horizontal',        // 折り返しのある横並び（自動判定だと行の境目で揺れる）
                swapThreshold: 0.65,            // カードの中央を十分越えてから入れ替える（境目でのガタつき防止）
                ghostClass: 'sortable-ghost',   // 挿入位置（カードと同じ大きさの空き枠＋ライン）
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                forceFallback: true,            // ブラウザ標準のドラッグ画像を使わず、カードをそのまま追従させる
                fallbackOnBody: true,           // 追従するカードは body 直下に置き、一覧のレイアウトに影響させない
                fallbackTolerance: 3,           // 3px 動かすまではクリック扱い（削除ボタン等の誤操作防止）
                filter: 'button, a, input, select, textarea',
                preventOnFilter: false,
                scroll: true,
                scrollSensitivity: 80,
                scrollSpeed: 12,
                onEnd: function (evt) {
                    if (evt.oldIndex === evt.newIndex) return;
                    save(list, opts);
                },
            });
        });
    }

    function save(list, opts) {
        var order = [];
        list.querySelectorAll(':scope > li').forEach(function (li, index) {
            order.push({ id: li.dataset[toCamel(opts.idAttr)], sort: index + 1 }); // 並び順は 1 から
        });

        var body = {};
        body[opts.paramName] = order;

        fetch(opts.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': opts.token,
            },
            body: JSON.stringify(body),
        })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (json && json.success) {
                    if (typeof opts.onSaved === 'function') opts.onSaved();
                } else {
                    alert('並び替えに失敗しました');
                }
            })
            .catch(function () {
                alert('エラーが発生しました');
            });
    }

    // 'schedule-id' → 'scheduleId'（dataset のキー名）
    function toCamel(s) {
        return s.replace(/-([a-z])/g, function (_, c) { return c.toUpperCase(); });
    }

    window.initSortableGrid = initSortableGrid;
})(window);
