/**
 * Nexusソフトテニススクール サイトのJavaScript
 */

document.addEventListener("DOMContentLoaded", function() {
    // ナビゲーション - アクティブなナビゲーションアイテムのマーク
    const currentLocation = location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        // ホームページのチェック
        if (currentLocation === '/' || currentLocation === '/index.html') {
            if (link.getAttribute('href') === 'index.html') {
                link.classList.add('active');
            }
        }
        // その他のページのチェック
        else if (link.getAttribute('href') && currentLocation.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
    
    // ナビゲーション - スクロールでの背景変更
    function updateNavbar() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.background = 'white';
            navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        }
    }
    
    window.addEventListener('scroll', updateNavbar);
    updateNavbar(); // 初期ロード時に実行
    
    // ハンバーガーメニュークリック時にメニューを閉じる
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        const menuLinks = navbarCollapse.querySelectorAll('a');
        
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            });
        });
    }
    
    // スムーススクロール
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const offset = 80; // ナビゲーションバーの高さを考慮
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // フォームバリデーション
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // FAQタブ
    const faqTabs = document.querySelectorAll('#faqTab button');
    
    if (faqTabs.length > 0) {
        faqTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document.querySelector(tab.dataset.bsTarget);
                
                // アクティブなタブを設定
                document.querySelectorAll('#faqTab button').forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
                
                // タブコンテンツを表示
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                target.classList.add('show', 'active');
            });
        });
    }
    
    // 日付入力フィールドの最小日付を現在日に設定
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    if (dateInputs.length > 0) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const formattedDate = `${yyyy}-${mm}-${dd}`;
        
        dateInputs.forEach(input => {
            input.setAttribute('min', formattedDate);
        });
    }
    
    // 動画サムネイルのクリックイベント（実際のビデオ機能はここで実装されます）
    const videoThumbnails = document.querySelectorAll('.video-play-button');
    
    videoThumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // 実際の実装では、ここにビデオプレーヤーを表示するコードが入ります
            alert('ビデオ再生機能は実際のサイトで実装されます。');
        });
    });
});
