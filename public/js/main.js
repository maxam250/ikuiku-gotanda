// モーダル関連の処理
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');

    if (modal && openModalBtn && closeModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modal.showModal();
        });

        closeModalBtn.addEventListener('click', () => {
            modal.close();
        });
    }

    // カレンダーの月切り替え機能
    let currentCalendarDate = new Date();
    const currentMonthElement = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const calendarGrid = document.getElementById('calendarGrid');

    function updateCalendarDisplay() {
        const year = currentCalendarDate.getFullYear();
        const month = currentCalendarDate.getMonth() + 1;

        // 月表示を更新
        if (currentMonthElement) {
            currentMonthElement.textContent = `${year}年${month}月`;
        }

        // TODO: ここでAjaxリクエストを送ってカレンダーデータを更新
        // 現在は表示のみ更新（実際のスケジュールデータ更新は別途実装が必要）
    }

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
            updateCalendarDisplay();
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
            updateCalendarDisplay();
        });
    }

    // アイテムモーダル機能
    const itemModal = document.getElementById('itemModal');
    const closeItemModalBtn = document.getElementById('closeItemModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalCategory = document.getElementById('modalCategory');
    const modalPrice = document.getElementById('modalPrice');

    // コスチューム・オプションアイテムのクリック処理
    document.addEventListener('click', function(e) {
        const costumeItem = e.target.closest('.costume-item');
        const optionItem = e.target.closest('.option-item');

        if (costumeItem || optionItem) {
            const item = costumeItem || optionItem;
            const name = item.getAttribute('data-name');
            const image = item.getAttribute('data-image');
            const price = item.getAttribute('data-price');
            const category = item.getAttribute('data-category');

            modalTitle.textContent = name;
            modalImage.src = image;
            modalImage.alt = name;
            modalCategory.textContent = category;
            modalPrice.textContent = price > 0 ? `${parseInt(price).toLocaleString()}円` : '';

            itemModal.classList.remove('hidden');
        }
    });

    // アイテムモーダルを閉じる
    if (closeItemModalBtn) {
        closeItemModalBtn.addEventListener('click', function() {
            itemModal.classList.add('hidden');
        });
    }

    // アイテムモーダルの背景をクリックして閉じる
    if (itemModal) {
        itemModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }

    // ニューススライダーの初期化
    if (document.querySelector('.news-swiper')) {
        const newsSwiperElement = document.querySelector('.news-swiper');
        const newsSlideCount = newsSwiperElement.querySelectorAll('.swiper-slide').length;
        const newsSwiper = new Swiper('.news-swiper', {
            slidesPerView: 1,
            spaceBetween: 12,
            loop: newsSlideCount > 3, // スライドが3枚以上の場合のみループを有効化
            autoplay: false,
            pagination: {
                el: '.news-pagination-custom',
                clickable: true,
            },
            navigation: {
                nextEl: '.news-next-btn',
                prevEl: '.news-prev-btn',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
            },
            effect: 'slide',
            speed: 300,
            touchRatio: 1,
            threshold: 10,
            allowTouchMove: true,
            simulateTouch: true,
            watchSlidesProgress: true,
            on: {
                init: function() {
                    console.log('News swiper initialized with smooth navigation');
                }
            }
        });

        // ナビゲーションボタンはSwiper設定内で直接制御
    }

    // プロフィールページの画像カルーセル
    // 注意: 2枚だけのとき loop:true だと複製スライドで崩れやすい（Swiper仕様）。
    // 3枚以上で loop、2枚は rewind、1枚はオートプレイ・矢印なし。
    // fade は複数枚でラッパー高さが異常に伸びるケースがあるため slide を使用。
    if (document.querySelector('.profile-swiper')) {
        const profileSwiperElement = document.querySelector('.profile-swiper');
        const profileSlideCount = profileSwiperElement.querySelectorAll('.swiper-slide').length;
        if (profileSlideCount > 0) {
            const useLoop = profileSlideCount >= 3;

            function profVideoPlay(swiper) {
                var slide = swiper.slides[swiper.activeIndex];
                if (!slide) return;
                var v = slide.querySelector('video.prof-thumb-video-slide');
                if (!v) return;
                if (!v.src && v.dataset.src) {
                    v.src = v.dataset.src;
                    v.load();
                    v.addEventListener('canplay', function() { v.play().catch(function(){}); }, { once: true });
                } else {
                    var p = v.play();
                    if (p && typeof p.catch === 'function') p.catch(function(){});
                }
            }
            function profVideoPauseAll(swiper) {
                swiper.slides.forEach(function(slide) {
                    var v = slide.querySelector('video.prof-thumb-video-slide');
                    if (v) v.pause();
                });
            }

            new Swiper('.profile-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: useLoop,
                rewind: !useLoop && profileSlideCount > 1,
                speed: 450,
                autoplay: profileSlideCount > 1 ? {
                    delay: 5000,
                    disableOnInteraction: false,
                } : false,
                pagination: profileSlideCount > 1 ? {
                    el: '.profile-pagination',
                    clickable: true,
                    dynamicBullets: profileSlideCount > 3,
                } : false,
                navigation: profileSlideCount > 1 ? {
                    nextEl: '.profile-next',
                    prevEl: '.profile-prev',
                } : false,
                effect: 'slide',
                keyboard: {
                    enabled: true,
                },
                mousewheel: {
                    enabled: false,
                },
                watchOverflow: true,
                observer: true,
                observeParents: true,
                on: {
                    init: function(swiper) { profVideoPlay(swiper); },
                    slideChange: function(swiper) {
                        profVideoPauseAll(swiper);
                        profVideoPlay(swiper);
                    },
                },
            });
        }
    }

    // プロフィールページのタブ機能
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // すべてのタブボタンとコンテンツの状態をリセット
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('border-transparent', 'text-gray-500');
                btn.classList.remove('border-pink-500', 'text-gray-900');
            });

            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // アクティブなタブを設定
            this.classList.add('active');
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-pink-500', 'text-gray-900');

            // 対応するコンテンツを表示
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });

    // カレンダーのモバイル表示機能（プロフィールページ以外）
    const scheduleDays = document.querySelectorAll('.schedule-clickable');
    const scheduleModal = document.getElementById('scheduleModal');
    const modalDate = document.getElementById('modalDate');
    const modalTimes = document.getElementById('modalTimes');
    const closeScheduleModalBtn = document.getElementById('closeScheduleModal');

    // プロフィールページでは独自のモーダルシステムを使用するため、main.jsの機能を無効化
    const isProfilePage = window.location.pathname.includes('/prof/') || window.location.pathname.includes('/item/');
    if (scheduleModal && modalDate && modalTimes && !isProfilePage) {
        scheduleDays.forEach(day => {
            day.addEventListener('click', function() {
                if (window.innerWidth < 640) { // モバイルサイズの場合のみ
                    const date = this.getAttribute('data-date');
                    const times = this.getAttribute('data-times');

                    if (modalDate) modalDate.textContent = date;
                    if (modalTimes) modalTimes.innerHTML = '';

                    if (times && modalTimes) {
                        const timeArray = times.split('|');
                        timeArray.forEach(time => {
                            const timeDiv = document.createElement('div');
                            timeDiv.className = 'bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium';
                            timeDiv.textContent = time;
                            modalTimes.appendChild(timeDiv);
                        });
                    }

                    if (scheduleModal) scheduleModal.classList.remove('hidden');
                }
            });
        });
    }

    // モーダルを閉じる（プロフィールページ以外）
    if (closeScheduleModalBtn && scheduleModal && !isProfilePage) {
        closeScheduleModalBtn.addEventListener('click', function() {
            scheduleModal.classList.add('hidden');
        });
    }

    // モーダルの背景をクリックして閉じる（プロフィールページ以外）
    if (scheduleModal && !isProfilePage) {
        scheduleModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }

    // ウィンドウサイズが変更されたときにモーダルを閉じる（プロフィールページ以外）
    if (!isProfilePage) {
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 640 && scheduleModal) {
                scheduleModal.classList.add('hidden');
            }
        });
    }

    // ギャラリーモーダルのイベント設定
    const galleryModal = document.getElementById('galleryModal');
    if (galleryModal) {
        // キーボードでモーダルを閉じる（複数のキー対応）
        document.addEventListener('keydown', function(e) {
            if (!galleryModal.classList.contains('hidden')) {
                if (e.key === 'Escape' || e.key === ' ' || e.key === 'q' || e.key === 'Q') {
                    e.preventDefault();
                    closeGalleryModal();
                }
            }
        });

        // モーダル背景クリックで閉じる
        galleryModal.addEventListener('click', function(e) {
            // モーダルの背景部分をクリックした場合のみ閉じる
            if (e.target === galleryModal || e.target.classList.contains('gallery-modal-overlay')) {
                closeGalleryModal();
            }
        });

        // 画像ダブルクリックで閉じる
        galleryModal.addEventListener('dblclick', function(e) {
            closeGalleryModal();
        });
    }
});

// アコーディオン機能はfooter.blade.phpで定義済み

// ギャラリーモーダル機能
let gallerySwiper;

function handleModalBackgroundClick(event) {
    if (event.target === event.currentTarget) {
        closeGalleryModal();
    }
}

function openGalleryModal(startIndex) {
    startIndex = startIndex || 0;
    const modal = document.getElementById('galleryModal');
    const imageCount = parseInt(modal.getAttribute('data-image-count'));

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Swiperを初期化
    if (gallerySwiper) {
        gallerySwiper.destroy();
    }

    const gallerySwiperElement = document.querySelector('.gallery-swiper');
    const gallerySlideCount = gallerySwiperElement ? gallerySwiperElement.querySelectorAll('.swiper-slide').length : 0;

    gallerySwiper = new Swiper('.gallery-swiper', {
        initialSlide: startIndex,
        loop: gallerySlideCount > 1, // スライドが1枚以上の場合のみループを有効化
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.gallery-pagination',
            clickable: true,
        },
        keyboard: {
            enabled: true,
        },
        zoom: {
            maxRatio: 3,
        },
    });
}

function closeGalleryModal() {
    const modal = document.getElementById('galleryModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';

    if (gallerySwiper) {
        gallerySwiper.destroy();
    }

    // メインビジュアルスライダー初期化
    function initMainVisualSwipers() {
        console.log('Initializing main visual swipers...');

        // PC用スライダー初期化
        const swiperPC = document.querySelector('.main-visual-swiper-pc');
        console.log('PC Swiper element:', swiperPC);

        if (swiperPC) {
            console.log('Initializing PC swiper...');
            const pcSlideCount = swiperPC.querySelectorAll('.swiper-slide').length;
            new Swiper('.main-visual-swiper-pc', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: pcSlideCount > 1, // スライドが1枚以上の場合のみループを有効化
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.main-visual-swiper-pc .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.main-visual-swiper-pc .swiper-button-next',
                    prevEl: '.main-visual-swiper-pc .swiper-button-prev',
                },
                effect: 'slide',
                speed: 800,
            });
            console.log('PC swiper initialized');
        }

        // スマホ用スライダー初期化
        const swiperMobile = document.querySelector('.main-visual-swiper-mobile');
        console.log('Mobile Swiper element:', swiperMobile);

        if (swiperMobile) {
            console.log('Initializing mobile swiper...');
            const mobileSlideCount = swiperMobile.querySelectorAll('.swiper-slide').length;
            new Swiper('.main-visual-swiper-mobile', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: mobileSlideCount > 1, // スライドが1枚以上の場合のみループを有効化
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.main-visual-swiper-mobile .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.main-visual-swiper-mobile .swiper-button-next',
                    prevEl: '.main-visual-swiper-mobile .swiper-button-prev',
                },
                effect: 'slide',
                speed: 800,
            });
            console.log('Mobile swiper initialized');
        }
    }

    // 少し遅らせてメインビジュアルスライダーを初期化
    setTimeout(function() {
        initMainVisualSwipers();
    }, 100);
}


