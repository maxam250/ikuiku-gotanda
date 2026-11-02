@props([
    'value' => request('name', ''),
])
@php $hasValue = !empty(trim($value)); @endphp

<div class="srch-accordion{{ $hasValue ? ' open' : '' }}" id="cns-accordion">
    <button type="button" class="srch-accordion-toggle" onclick="toggleSrchAccordion(this)">
        <span class="srch-accordion-label">
            <i class="fas fa-user fa-xs"></i> 名前で検索する
        </span>
        <span class="srch-accordion-badge" id="cns-badge" style="{{ $hasValue ? '' : 'display:none' }}">1</span>
        <i class="fas fa-chevron-down srch-accordion-arrow"></i>
    </button>
    <div class="srch-accordion-body">
        <div class="srch-accordion-inner">
            <div class="cns-input-row">
                <span class="cns-icon"><i class="fas fa-search"></i></span>
                <input class="cns-input"
                       type="text"
                       name="name"
                       id="cns-input"
                       value="{{ $value }}"
                       placeholder="キャスト名を入力…"
                       autocomplete="off"
                       oninput="cnsBadgeUpdate(this)">
                <button type="button" class="cns-clear" id="cns-clear-btn"
                        style="{{ $hasValue ? '' : 'display:none' }}"
                        onclick="cnsClear()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof toggleSrchAccordion === 'undefined') {
    function toggleSrchAccordion(btn) {
        btn.closest('.srch-accordion').classList.toggle('open');
    }
}
function cnsBadgeUpdate(input) {
    var badge = document.getElementById('cns-badge');
    var clearBtn = document.getElementById('cns-clear-btn');
    var hasVal = input.value.trim().length > 0;
    if (badge)    badge.style.display    = hasVal ? '' : 'none';
    if (clearBtn) clearBtn.style.display = hasVal ? '' : 'none';
}
function cnsClear() {
    var input = document.getElementById('cns-input');
    if (input) { input.value = ''; cnsBadgeUpdate(input); }
}
</script>
