<section class="toPageArea">
    <a href="{{ route('admin.newsletter.subscribers.index') }}" class="{{ request()->routeIs('admin.newsletter.subscribers.*') ? 'active' : '' }}">購読者管理</a>
    <a href="{{ route('admin.newsletter.templates.index') }}" class="{{ request()->routeIs('admin.newsletter.templates.*') ? 'active' : '' }}">テンプレート管理</a>
    <a href="{{ route('admin.newsletter.campaigns.index') }}" class="{{ request()->routeIs('admin.newsletter.campaigns.*') ? 'active' : '' }}">配信管理</a>
</section>

<style>
.toPageArea a.active {
    border-bottom-color: #57add0 !important;
    color: #3f3f6f;
    font-weight: bold;
}
</style>

