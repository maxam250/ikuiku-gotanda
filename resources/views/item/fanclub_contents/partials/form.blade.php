{{-- 後方互換: チャンネルフォームのみ --}}
@include('item.fanclub_contents.partials.parent_form', ['content' => $content ?? null, 'categories' => $categories ?? []])
