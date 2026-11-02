@props(['href', 'text' => '一覧に戻る'])

<a href="{{ $href }}" class="back-to-list-btn inline-flex items-center gap-2 transition-all duration-200 mb-6">
    <i class="fas fa-arrow-left"></i>
    <span>{{ $text }}</span>
</a>

<style>
.back-to-list-btn {
    background: white;
    color: #64748b; /* slate-500 */
    border: 1px solid #d1d5db; /* gray-300 */
    padding: 0.75rem 1.25rem; /* py-3 px-5 */
    border-radius: 0.5rem; /* rounded-lg */
    font-weight: 500;
    text-decoration: none;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.back-to-list-btn:hover {
    background: #f8fafc; /* slate-50 */
    color: #475569; /* slate-600 */
    border-color: #9ca3af; /* gray-400 */
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.back-to-list-btn i {
    color: #94a3b8; /* slate-400 */
    transition: color 0.2s ease;
}

.back-to-list-btn:hover i {
    color: #64748b; /* slate-500 */
}
</style>
