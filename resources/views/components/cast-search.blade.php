@props([
    'optionCates',
    'itemCategories' => null,
    'options' => null,
    'optionPays' => null,
    'action' => null,
])
@php
    $formAction        = $action ?? route('item.index');
    $nameVal           = request('name', '');
    $selectedCates     = array_map('intval', (array) request('option_cates', []));
    $selectedItemCates = array_map('intval', (array) request('item_category_ids', []));
    $selectedOptions   = array_map('intval', (array) request('options', []));
    $selectedPays      = array_map('intval', (array) request('option_pays', []));
    $options           = $options ?? collect();
    $optionPays        = $optionPays ?? collect();
    $hasName           = trim($nameVal) !== '';
    $optItemCount      = count($selectedOptions) + count($selectedPays);
    $cateCount         = count($selectedCates) + count($selectedItemCates) + $optItemCount;
    $activeTab         = ($cateCount > 0 && !$hasName) ? 'option' : 'name';
    $hasIndividualOpts = $options->isNotEmpty() || $optionPays->isNotEmpty();
@endphp

<div class="cast-search-wrap">

    {{-- タブナビゲーション --}}
    <div class="cs-tab-nav">
        <button type="button"
                id="cs-tab-btn-name"
                class="cs-tab-btn{{ $activeTab === 'name' ? ' active' : '' }}"
                onclick="switchCsTab('name')">
            <i class="fas fa-user fa-xs"></i> 名前
            <span class="cs-name-badge cs-tab-badge"
                  style="{{ $hasName ? '' : 'display:none' }}">1</span>
        </button>
        <button type="button"
                id="cs-tab-btn-option"
                class="cs-tab-btn{{ $activeTab === 'option' ? ' active' : '' }}"
                onclick="switchCsTab('option')">
            <i class="fas fa-tag fa-xs"></i> オプション
            <span class="cs-cate-badge cs-tab-badge"
                  style="{{ $cateCount > 0 ? '' : 'display:none' }}">{{ $cateCount ?: '' }}</span>
        </button>
    </div>
    <p class="cs-search-hint">
        項目を複数選択すると全てに一致する女の子だけを表示します
    </p>

    {{-- ① 名前タブ --}}
    <div id="cs-tab-name" class="cs-tab-content{{ $activeTab !== 'name' ? ' cs-tab-hidden' : '' }}">
        <form method="GET" action="{{ $formAction }}">
            <div class="cns-input-row">
                <span class="cns-icon"><i class="fas fa-search fa-xs"></i></span>
                <input class="cns-input"
                       type="text"
                       name="name"
                       value="{{ $nameVal }}"
                       placeholder="キャスト名を入力…"
                       autocomplete="off"
                       oninput="csNameSync(this)">
                <button type="button" class="cns-clear"
                        style="{{ $hasName ? '' : 'display:none' }}"
                        onclick="csNameClear(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cs-tab-actions">
                <a href="{{ $formAction }}" class="cast-search-reset">
                    <i class="fas fa-undo fa-xs"></i> リセット
                </a>
                <button type="submit" class="cast-search-btn">
                    <i class="fas fa-search"></i> 検索
                </button>
            </div>
        </form>
    </div>

    {{-- ② オプションタブ --}}
    <div id="cs-tab-option" class="cs-tab-content{{ $activeTab !== 'option' ? ' cs-tab-hidden' : '' }}">
        <form method="GET" action="{{ $formAction }}">

            {{-- キャストカテゴリ --}}
            @if($itemCategories && $itemCategories->isNotEmpty())
            <div class="ocf-section-label">タイプ</div>
            <div class="ocf-chips ocf-chips--category">
                @foreach($itemCategories as $cat)
                @php $isCatChecked = in_array($cat->id, $selectedItemCates); @endphp
                <label class="ocf-chip{{ $isCatChecked ? ' checked' : '' }}"
                       data-color="{{ $cat->color }}"
                       style="{{ $isCatChecked ? 'border-color:'.$cat->color.';background:'.$cat->color.';color:#fff;' : '' }}">
                    <input type="checkbox"
                           name="item_category_ids[]"
                           value="{{ $cat->id }}"
                           {{ $isCatChecked ? 'checked' : '' }}
                           onchange="csCateSyncAll(this)">
                    {{ $cat->name }}
                </label>
                @endforeach
            </div>
            @if($optionCates->isNotEmpty())
            <div class="ocf-section-label" style="margin-top:8px;">オプション</div>
            @endif
            @endif

            {{-- オプションカテゴリ --}}
            <div class="ocf-chips">
                @foreach($optionCates as $cate)
                <label class="ocf-chip{{ in_array($cate->id, $selectedCates) ? ' checked' : '' }}">
                    <input type="checkbox"
                           name="option_cates[]"
                           value="{{ $cate->id }}"
                           {{ in_array($cate->id, $selectedCates) ? 'checked' : '' }}
                           onchange="this.closest('label').classList.toggle('checked', this.checked);
                                     csCateSyncAll(this)">
                    {{ $cate->title }}
                </label>
                @endforeach
            </div>

            {{-- 個別オプション --}}
            @if($hasIndividualOpts)
            <div class="srch-accordion cs-opt-accordion{{ $optItemCount > 0 ? ' open' : '' }}" style="margin-top:12px;">
                <button type="button" class="srch-accordion-toggle" onclick="toggleSrchAccordion(this)">
                    <span class="srch-accordion-label">
                        <i class="fas fa-list fa-xs"></i> 個別オプション
                    </span>
                    <span class="srch-accordion-badge cs-opt-item-badge"
                          style="{{ $optItemCount > 0 ? '' : 'display:none' }}">{{ $optItemCount ?: '' }}</span>
                    <i class="fas fa-chevron-down srch-accordion-arrow"></i>
                </button>
                <div class="srch-accordion-body">
                    <div class="srch-accordion-inner">
                        @foreach($optionCates as $cate)
                            @php
                                $cateOptions = $options->where('cate', $cate->id);
                                $catePays    = $optionPays->where('cate', $cate->id);
                            @endphp
                            @if($cateOptions->isNotEmpty() || $catePays->isNotEmpty())
                            <div class="ocf-section-label">{{ $cate->title }}</div>
                            <div class="ocf-chips">
                                @foreach($cateOptions as $opt)
                                <label class="ocf-chip{{ in_array($opt->id, $selectedOptions) ? ' checked' : '' }}">
                                    <input type="checkbox"
                                           name="options[]"
                                           value="{{ $opt->id }}"
                                           {{ in_array($opt->id, $selectedOptions) ? 'checked' : '' }}
                                           onchange="this.closest('label').classList.toggle('checked', this.checked);
                                                     csCateSyncAll(this)">
                                    {{ $opt->title }}
                                </label>
                                @endforeach
                                @foreach($catePays as $opt)
                                <label class="ocf-chip{{ in_array($opt->id, $selectedPays) ? ' checked' : '' }}">
                                    <input type="checkbox"
                                           name="option_pays[]"
                                           value="{{ $opt->id }}"
                                           {{ in_array($opt->id, $selectedPays) ? 'checked' : '' }}
                                           onchange="this.closest('label').classList.toggle('checked', this.checked);
                                                     csCateSyncAll(this)">
                                    {{ $opt->title }}
                                </label>
                                @endforeach
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="cs-tab-actions">
                <a href="{{ $formAction }}" class="cast-search-reset">
                    <i class="fas fa-undo fa-xs"></i> リセット
                </a>
                <button type="submit" class="cast-search-btn">
                    <i class="fas fa-search"></i> 検索
                </button>
            </div>
        </form>
    </div>

</div>

<style>
.cast-search-wrap{
    width:800px;
    max-width:100%;
    margin:0 auto 3rem;
}
/* ── タブナビ ── */
.cs-tab-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
}
.cs-tab-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ui-text);
    background: #fff;
    border: 1px solid var(--ui-line-dark, #ffb7d5);
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    white-space: nowrap;
    letter-spacing: 0.02em;
}
.cs-tab-btn:hover {
    background: var(--ui-soft, #fff0f6);
    color: var(--ui-accent, #ff2d78);
    border-color: var(--ui-accent, #ff2d78);
}
.cs-tab-btn.active {
    background: var(--ui-accent, #ff2d78);
    color: #fff;
    border-color: var(--ui-accent, #ff2d78);
    box-shadow: none;
}
.cs-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    font-size: 0.7rem;
    font-weight: 700;
    background: #fff;
    color: #ec4899;
    border-radius: 9999px;
    line-height: 1;
}
.cs-tab-btn.active .cs-tab-badge {
    background: #fff;
    color: #ec4899;
}

/* ── タブコンテンツ ── */
.cs-tab-content {
    padding: 2px 0 4px;
}
.cs-tab-hidden {
    display: none;
}

/* ── 検索/リセットボタン行 ── */
.cs-tab-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid var(--ui-line, #ffe0ee);
}
</style>

<script>
function switchCsTab(tab) {
    document.querySelectorAll('.cs-tab-content').forEach(function(el) {
        el.classList.add('cs-tab-hidden');
    });
    document.querySelectorAll('.cs-tab-btn').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById('cs-tab-' + tab).classList.remove('cs-tab-hidden');
    document.getElementById('cs-tab-btn-' + tab).classList.add('active');
}

/* 名前検索バッジ */
function csNameSync(input) {
    var hasVal   = input.value.trim().length > 0;
    var clearBtn = input.closest('.cs-tab-content').querySelector('.cns-clear');
    var badge    = document.querySelector('.cs-name-badge');
    if (clearBtn) clearBtn.style.display = hasVal ? '' : 'none';
    if (badge)    badge.style.display    = hasVal ? '' : 'none';
}
function csNameClear(btn) {
    var input = btn.closest('.cs-tab-content').querySelector('.cns-input');
    if (input) { input.value = ''; csNameSync(input); }
}

/* オプション+カテゴリ検索バッジ */
function csCateSyncAll(checkbox) {
    var tabContent = checkbox.closest('.cs-tab-content');
    var count      = tabContent.querySelectorAll('input[type=checkbox]:checked').length;
    var badge      = document.querySelector('.cs-cate-badge');
    if (badge) { badge.textContent = count || ''; badge.style.display = count > 0 ? '' : 'none'; }
    var optItemCount = tabContent.querySelectorAll('input[name="options[]"]:checked, input[name="option_pays[]"]:checked').length;
    var optBadge     = tabContent.querySelector('.cs-opt-item-badge');
    if (optBadge) {
        optBadge.textContent = optItemCount || '';
        optBadge.style.display = optItemCount > 0 ? '' : 'none';
    }
    if (checkbox.name === 'item_category_ids[]') {
        var label = checkbox.closest('label');
        var color = label ? label.dataset.color : '';
        if (checkbox.checked && color) {
            label.style.borderColor = color;
            label.style.background  = color;
            label.style.color       = '#fff';
        } else {
            label.style.borderColor = '';
            label.style.background  = '';
            label.style.color       = '';
        }
        label.classList.toggle('checked', checkbox.checked);
    }
}
/* 後方互換 */
function csCateSync(checkbox) { csCateSyncAll(checkbox); }
function toggleSrchAccordion(btn) {
    btn.closest('.srch-accordion').classList.toggle('open');
}
</script>
