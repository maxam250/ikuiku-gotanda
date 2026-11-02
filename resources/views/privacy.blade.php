@extends('layouts.app')
@section('breadcrumb_label', 'プライバシーポリシー')

@section('content')

<div class="titleArea">
    <h2>PRIVACY POLICY</h2>
    <div class="title">プライバシーポリシー</div>
</div>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="prose max-w-none">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">プライバシーポリシー</h3>

                <p class="text-gray-700 mb-6">
                    当社は、お客様の個人情報の保護を最重要事項と考え、適切な収集、利用、管理を行います。
                    以下のプライバシーポリシーに従って個人情報を保護いたします。
                </p>

                <div class="space-y-8">
                    <!-- 1. 個人情報の収集について -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">1. 個人情報の収集について</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社は、以下の目的で個人情報を収集いたします：</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>サービス提供のため</li>
                                <li>お客様からのお問い合わせへの対応</li>
                                <li>予約・申込の処理</li>
                                <li>求人応募の処理</li>
                                <li>サービス改善のための分析</li>
                                <li>法令に基づく対応</li>
                            </ul>
                            <p>収集する個人情報の種類：</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>氏名</li>
                                <li>メールアドレス</li>
                                <li>電話番号</li>
                                <!-- <li>住所</li> -->
                                <li>年齢</li>
                                <li>その他サービス提供に必要な情報</li>
                            </ul>
                        </div>
                    </section>

                    <!-- 2. 個人情報の利用目的 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">2. 個人情報の利用目的</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>収集した個人情報は、以下の目的でのみ利用いたします：</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>撮影会の予約・運営</li>
                                <li>お客様へのサービス提供</li>
                                <li>求人応募の処理・面接の実施</li>
                                <li>お客様からのお問い合わせへの対応</li>
                                <li>サービス改善のための分析</li>
                                <li>法令に基づく対応</li>
                                <li>その他、収集時に明示した目的</li>
                            </ul>
                        </div>
                    </section>

                    <!-- 3. 個人情報の管理 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">3. 個人情報の管理</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社は、お客様の個人情報を適切に管理し、以下の理由に該当する場合を除き、個人情報を第三者に開示いたしません。</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>お客様の同意がある場合</li>
                                <li>法令に基づき開示することが必要である場合</li>
                                <li>人の生命、身体または財産の保護のために必要な場合</li>
                                <li>公衆衛生の向上または児童の健全な育成の推進のために特に必要な場合</li>
                                <li>国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合</li>
                            </ul>
                        </div>
                    </section>

                    <!-- 4. 個人情報の安全対策 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">4. 個人情報の安全対策</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社は、個人情報の正確性及び安全性確保のために、セキュリティに万全の対策を講じ、個人情報の漏洩、滅失またはき損の防止、その他の個人情報の安全管理のために必要かつ適切な措置を講じます。</p>
                            <ul class="list-disc pl-6 space-y-2">
                                <li>SSL暗号化通信の採用</li>
                                <li>アクセス制御の実施</li>
                                <li>ウイルス対策ソフトの導入</li>
                                <li>定期的なセキュリティ監査の実施</li>
                                <li>従業員への個人情報保護教育の実施</li>
                            </ul>
                        </div>
                    </section>

                    <!-- 5. 決済情報の取り扱い -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">5. 決済情報の取り扱い</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>クレジットカード情報・銀行口座情報等の決済に関する情報は、外部の決済機関を通じて処理いたします。当店ではこれらの情報を一切保持・管理いたしません。</p>
                        </div>
                    </section>

                    <!-- 6. 個人情報の開示・訂正・利用停止 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">6. 個人情報の開示・訂正・利用停止</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>お客様ご本人からの個人情報の開示、訂正、利用停止のご要望に対しては、法令に基づき適切に対応いたします。</p>
                            <p>お問い合わせ先：</p>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold">{{$shops->shop_name}}</p>
                                <p>メール：{{$shops->shop_mail}}</p>
                                <p>電話：{{$shops->shop_tel}}</p>
                                <p>受付時間：{{$shops->shop_open}}</p>
                            </div>
                        </div>
                    </section>

                    <!-- 7. クッキー（Cookie）の使用 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">7. クッキー（Cookie）の使用</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社のウェブサイトでは、お客様により良いサービスを提供するためにクッキーを使用することがあります。クッキーは、お客様のコンピュータを識別するために使用されますが、お客様個人を特定できるものではありません。</p>
                            <p>お客様は、ブラウザの設定によりクッキーの使用を拒否することができますが、その場合、当社のウェブサイトの一部機能が正常に動作しない可能性があります。</p>
                        </div>
                    </section>

                    <!-- 8. アクセス解析ツールの使用 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">8. アクセス解析ツールの使用</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社のウェブサイトでは、Google Analyticsなどのアクセス解析ツールを使用しています。これらのツールは、トラフィックデータの収集のためにクッキーを使用します。</p>
                            <p>Google Analyticsの利用規約については、<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank" class="text-blue-600 hover:text-blue-800">Google Analytics利用規約</a>をご確認ください。</p>
                        </div>
                    </section>

                    <!-- 9. 外部リンク -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">9. 外部リンク</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社のウェブサイトからリンクしている外部サイトについては、当社は責任を負いかねます。外部サイトでの個人情報の取り扱いについては、各サイトのプライバシーポリシーをご確認ください。</p>
                        </div>
                    </section>

                    <!-- 10. プライバシーポリシーの変更 -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">10. プライバシーポリシーの変更</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>当社は、必要に応じてこのプライバシーポリシーを変更することがあります。重要な変更がある場合は、当社のウェブサイトでお知らせいたします。</p>
                        </div>
                    </section>

                    <!-- 11. お問い合わせ -->
                    <section>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">11. お問い合わせ</h4>
                        <div class="text-gray-700 space-y-3">
                            <p>このプライバシーポリシーに関するお問い合わせは、以下の窓口までお願いいたします。</p>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold">個人情報保護に関するお問い合わせ</p>
                                <p>{{$shops->shop_name}}</p>
                                <p>個人情報保護責任者</p>
                                <p>メール：{{$shops->shop_mail}}</p>
                                <p>電話：{{$shops->shop_tel}}</p>
                                <p>受付時間：{{$shops->shop_open}}</p>
                            </div>
                        </div>
                    </section>

                    <!-- 制定日・改定日 -->
                    <section class="border-t pt-6">
                        <div class="text-gray-600 text-sm">
                            <p>制定日：{{$shops->created_at}}</p>
                            <p>最終改定日：{{$shops->updated_at}}</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
