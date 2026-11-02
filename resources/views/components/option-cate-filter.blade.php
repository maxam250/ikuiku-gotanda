@props([
    'optionCates',
    'selected' => array_map('intval', (array) request('option_cates', [])),
])
@php $selectedCount = count($selected); @endphp

<div class="srch-accordion{{ $selectedCount > 0 ? ' open' : '' }}" id="ocf-accordion">
    <button type="button" class="srch-accordion-toggle" onclick="toggleSrchAccordion(this)">
        <span class="srch-accordion-label">
            <i class="fas fa-tag fa-xs"></i> オプション検索
        </span>
        <span class="srch-accordion-badge" id="ocf-badge"
              style="{{ $selectedCount > 0 ? '' : 'display:none' }}">{{ $selectedCount ?: '' }}</span>
        <i class="fas fa-chevron-down srch-accordion-arrow"></i>
    </button>
    <div class="srch-accordion-body">
        <div class="srch-accordion-inner">
            <div class="ocf-chips">
                @foreach($optionCates as $cate)
                <label class="ocf-chip{{ in_array($cate->id, $selected) ? ' checked' : '' }}">
                    <input type="checkbox"
                           name="option_cates[]"
                           value="{{ $cate->id }}"
                           {{ in_array($cate->id, $selected) ? 'checked' : '' }}
                           onchange="this.closest('label').classList.toggle('checked', this.checked); ocfBadgeUpdate()">
                    {{ $cate->title }}
                </label>
                @endforeach
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
function ocfBadgeUpdate() {
    var count = document.querySelectorAll('#ocf-accordion input[type=checkbox]:checked').length;
    var badge = document.getElementById('ocf-badge');
    if (!badge) return;
    badge.textContent = count;
    badge.style.display = count > 0 ? '' : 'none';
}
</script>
