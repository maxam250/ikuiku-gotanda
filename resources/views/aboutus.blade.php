

<section id="userGuide" class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="flex items-center gap-4 mb-8 lg:mb-12">
        <i class="fas fa-info-circle text-3xl lg:text-4xl text-slate-400 group-hover:text-blue-500 transition-colors duration-300"></i>
            <span class="text-2xl lg:text-3xl font-bold">About us</span>

    </div>

    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden" id="conceptAccordion">


        <div class="p-6 lg:p-8 max-h-96 overflow-y-auto custom-scrollbar" id="conceptContent">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
                <div class="space-y-4">
                    <p class="text-slate-700 leading-relaxed">
                        {{ $shops->shop_name }}はさまざまな活動を通して成長していくキャストを応援する育成応援サービスコンテンツです。
                    </p>

                    <p class="text-slate-700 leading-relaxed">
                        キャストは活動の一環として独自のコンテンツを配信したり、秋葉原の街を中心として一緒にお散歩したり遊んだりできます！<br>
                        キャストの活動を応援して一緒に楽しんでいただくことでキャストは成長させてください！
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
