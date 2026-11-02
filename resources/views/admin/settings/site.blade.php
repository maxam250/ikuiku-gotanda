<x-admin-layout>
    <x-slot name="header">サイト全般設定</x-slot>

    <section class="MessageArea">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section class="contentArea">
        <form method="POST" action="{{ route('admin.site-settings.update') }}">
            @csrf
            @method('PUT')

            <table class="edit">
                <tr>
                    <th>出勤日の切り替わり時間</th>
                    <td>
                        <input type="number"
                               name="schedule_switch_hour"
                               value="{{ old('schedule_switch_hour', $setting->schedule_switch_hour ?? 6) }}"
                               min="0" max="23"
                               style="width:5rem; text-align:center;"> 時
                        <p class="text-sm text-gray-500 mt-1">指定した時間以降を当日として扱います。例：6 → 翌朝6時（30時）に日付が切り替わります。</p>
                    </td>
                </tr>
                <tr>
                    <th>TinyMCE エディタ</th>
                    <td>
                        <label style="margin-right:1.5rem;">
                            <input type="radio" name="tinymce_skin" value="oxide"
                                {{ old('tinymce_skin', $setting->tinymce_skin ?? 'oxide') === 'oxide' ? 'checked' : '' }}>
                            ☀ ライトモード
                        </label>
                        <label>
                            <input type="radio" name="tinymce_skin" value="oxide-dark"
                                {{ old('tinymce_skin', $setting->tinymce_skin ?? 'oxide') === 'oxide-dark' ? 'checked' : '' }}>
                            🌙 ダークモード
                        </label>
                        <p class="text-sm text-gray-500 mt-2">管理画面内の全TinyMCEエディタの配色を切り替えます。</p>
                    </td>
                </tr>
                <tr>
                    <th>料金制</th>
                    <td>
                        <label style="margin-right:1.5rem;">
                            <input type="radio" name="pricing_type" value="unified"
                                {{ old('pricing_type', $setting->pricing_type ?? 'unified') === 'unified' ? 'checked' : '' }}>
                            統一料金制（systemページにコース料金を表示）
                        </label>
                        <label>
                            <input type="radio" name="pricing_type" value="individual"
                                {{ old('pricing_type', $setting->pricing_type ?? 'unified') === 'individual' ? 'checked' : '' }}>
                            キャスト個別料金制（各キャストページに料金を表示）
                        </label>

                    </td>
                </tr>
                <tr>
                    <th>Square決済リンクURL</th>
                    <td>
                        <input type="url"
                               name="square_payment_url"
                               value="{{ old('square_payment_url', $setting->square_payment_url) }}"
                               class="form-control"
                               placeholder="https://...">
                        <p class="text-sm text-gray-500 mt-2">ファンクラブ入会確認ページで表示します。</p>
                    </td>
                </tr>
                <tr>
                    <th>入会規約文</th>
                    <td>
                        <textarea name="fanclub_join_terms"
                                  rows="10"
                                  class="form-control"
                                  placeholder="入会規約の説明文を入力">{{ old('fanclub_join_terms', $setting->fanclub_join_terms) }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>支払い・決済の説明</th>
                    <td>
                        <textarea name="payment_description"
                                  rows="8"
                                  class="form-control"
                                  placeholder="支払い・決済に関する説明を入力">{{ old('payment_description', $setting->payment_description) }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>特商法（本文）</th>
                    <td>
                        <textarea name="specified_commercial_transactions"
                                  rows="10"
                                  class="form-control"
                                  placeholder="特定商取引法に基づく表記の本文を入力">{{ old('specified_commercial_transactions', $setting->specified_commercial_transactions) }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>退会確認メッセージ</th>
                    <td>
                        <textarea name="fanclub_withdraw_message"
                                  rows="6"
                                  class="form-control"
                                  placeholder="退会時に表示するメッセージを入力">{{ old('fanclub_withdraw_message', $setting->fanclub_withdraw_message) }}</textarea>
                        <p class="text-sm text-gray-500 mt-2">退会は即時退会として案内されます。</p>
                    </td>
                </tr>
            </table>

            <div class="submit mt-4">
                <button type="submit" class="btn btn-primary">更新する</button>
            </div>
        </form>
    </section>
</x-admin-layout>
