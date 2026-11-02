<style>
#conceptContent {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: all 0.4s ease-in-out;
    padding-top: 0;
    padding-bottom: 0;
}

#conceptContent.concept-content-show {
    max-height: 1200px;
    opacity: 1;
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}

@media (min-width: 1024px) {
    #conceptContent.concept-content-show {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
}

.toggle-icon {
    transition: transform 0.3s ease-in-out;
}

.concept-toggle-active .toggle-icon {
    transform: rotate(180deg);
}

.concept-toggle {
    position: relative;
    overflow: hidden;
}

.concept-toggle::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.concept-toggle:hover::before {
    left: 100%;
}
</style>

<section id="userGuide" class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="flex items-center gap-4 mb-8 lg:mb-12">
        <i class="fas fa-info-circle text-3xl lg:text-4xl text-slate-400 group-hover:text-blue-500 transition-colors duration-300"></i>
            <span class="text-2xl lg:text-3xl font-bold">About us</span>

    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden" id="conceptAccordion">
        <div class="p-6 lg:p-8 border-b border-slate-200/50 text-center">
            <p class="text-slate-700 text-base lg:text-lg leading-relaxed mb-8 max-w-3xl mx-auto">
                当店{{ $shops->shop_name }}はモデルと1対1で撮影を楽しめる個人撮影会スタジオです。
                所属するモデルは一般応募のモデルさんから芸能活動を行っているモデルさんまで様々...
            </p>
            <button class="concept-toggle inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-pink-300 to-pink-300 text-white font-semibold rounded-full hover:from-pink-600 hover:to-pink-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl" onclick="toggleConcept()">
                <span class="toggle-text">詳細を見る</span>
                <i class="fas fa-chevron-down transition-transform duration-300 toggle-icon"></i>
            </button>
        </div>

        <div class="p-6 lg:p-8" id="conceptContent">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
                <div class="space-y-4">
                    <p class="text-slate-700 leading-relaxed">
                        当店{{ $shops->shop_name }}はモデルと1対1で撮影を楽しめる個人撮影会スタジオです。<br>
                        <br>
                        所属するモデルは一般応募のモデルさんから芸能活動を行っているモデルさんまで様々なモデルさんが所属しております。<br>
                        スタジオは定常光、レフ板、カポックなど基本的な撮影機材は無料でご利用いただけます。<br>
                        <br>
                        オプションでモノブロックストロボやディフューザーやバックペーパー（各種カラー）などハイクオリティな撮影に
                        対応した機材も揃っております。
                    </p>

                    <p class="text-slate-700 leading-relaxed">
                        撮影コースは【私服撮影】【水着撮影】【屋外撮影】で、モデルの魅力を存分に堪能でき、
                        素敵な作品撮影をお楽しみいただけます。<br>
                        水着やコスチュームは常時100着前後を用意しております。<br>
                        屋外ロケでは自然や街中などのフォトジェニックなスポットでモデルと一緒に作品作り。
                        首都圏やちょっと遠いエリアでも、お気軽にご相談ください。<br>
                        <br>
                        {{ $shops->shop_name }}は魅力的な写真撮影と、モデルとのひと時を楽しんでいただけることをモットーとしたモデル撮影会です。
                    </p>
                </div>

                <div class="space-y-6">
                    <figure class="studio">
                        <img src="{%thumb file="images/index/concept/studio.jpg" hint="false" addgreytohint="false" link="false" offset_w="" longside="620" width="" height="" shortside="" quality="98"%}" alt="撮影スタジオ" class="w-full rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    </figure>
                    <figure class="course">
                        <div class="grid grid-cols-3 gap-4 course-images">
                            <div class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                <img src="{{ asset("images/index/concept/shihuku.jpg") }}" alt="私服で撮影会" class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent text-white text-sm font-medium p-3 text-center">私服撮影</span>
                            </div>
                            <div class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                <img src="{{ asset("images/index/concept/mizugi.jpg") }}" alt="水着で撮影会" class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent text-white text-sm font-medium p-3 text-center">水着撮影</span>
                            </div>
                            <div class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                <img src="{{ asset("images/index/concept/okugai.jpg") }}" alt="屋外で撮影会" class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent text-white text-sm font-medium p-3 text-center">屋外撮影</span>
                            </div>
                        </div>
                    </figure>
                </div>
            </div>
        </div>
    </div>
</section><!--/userGuide-->
