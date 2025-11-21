// إنشاء النجوم في الخلفية
function createStars() {
    const starsContainer = document.getElementById('stars-container');
    const starCount = 100;

    for (let i = 0; i < starCount; i++) {
        const star = document.createElement('div');
        star.classList.add('star');

        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const size = Math.random() * 2 + 1;
        const opacity = Math.random() * 0.5 + 0.3;
        const duration = Math.random() * 3 + 2;

        star.style.left = `${x}%`;
        star.style.top = `${y}%`;
        star.style.width = `${size}px`;
        star.style.height = `${size}px`;
        star.style.setProperty('--opacity', opacity);
        star.style.setProperty('--duration', `${duration}s`);

        starsContainer.appendChild(star);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    createStars();
});

// University Slider
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('#university-slider > div');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    let currentIndex = 0;
    let intervalId;

    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });

        // Deactivate all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });

        // Show current slide
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(currentIndex);
    }

    function startAutoSlide() {
        intervalId = setInterval(nextSlide, 5000); // Change every 5 seconds
    }

    function resetAutoSlide() {
        clearInterval(intervalId);
        startAutoSlide();
    }

    // Dot click events
    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            currentIndex = parseInt(this.dataset.index);
            showSlide(currentIndex);
            resetAutoSlide();
        });
    });

    // Navigation buttons
    prevBtn.addEventListener('click', function () {
        prevSlide();
        resetAutoSlide();
    });

    nextBtn.addEventListener('click', function () {
        nextSlide();
        resetAutoSlide();
    });

    // Initialize slider
    showSlide(currentIndex);
    startAutoSlide();

    // Pause on hover
    const sliderContainer = document.querySelector('.relative.w-full');
    sliderContainer.addEventListener('mouseenter', () => {
        clearInterval(intervalId);
    });

    sliderContainer.addEventListener('mouseleave', () => {
        startAutoSlide();
    });
});


// مشغل الفيديو
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('university-video');
    const playBtn = document.getElementById('play-btn');
    const muteBtn = document.getElementById('mute-btn');
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const progressBar = document.getElementById('progress-bar');
    const currentTimeEl = document.getElementById('current-time');
    const durationEl = document.getElementById('duration');
    const autoplayOverlay = document.getElementById('autoplay-overlay');

    // تحميل الفيديو
    video.addEventListener('loadedmetadata', function () {
        durationEl.textContent = formatTime(video.duration);
    });

    // تحديث شريط التقدم
    video.addEventListener('timeupdate', function () {
        const percent = (video.currentTime / video.duration) * 100;
        progressBar.style.width = percent + '%';
        currentTimeEl.textContent = formatTime(video.currentTime);
    });

    // تشغيل/إيقاف
    playBtn.addEventListener('click', function () {
        if (video.paused) {
            video.play();
            playBtn.innerHTML = '<i class="fas fa-pause"></i>';
            autoplayOverlay.classList.add('hidden');
        } else {
            video.pause();
            playBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
    });

    // كتم الصوت
    muteBtn.addEventListener('click', function () {
        video.muted = !video.muted;
        muteBtn.innerHTML = video.muted ?
            '<i class="fas fa-volume-mute"></i>' :
            '<i class="fas fa-volume-up"></i>';
    });

    // ملء الشاشة
    fullscreenBtn.addEventListener('click', function () {
        if (video.requestFullscreen) {
            video.requestFullscreen();
        } else if (video.webkitRequestFullscreen) {
            video.webkitRequestFullscreen();
        }
    });

    // النقر على شريط التقدم
    progressBar.parentElement.addEventListener('click', function (e) {
        const percent = e.offsetX / this.offsetWidth;
        video.currentTime = percent * video.duration;
    });

    // التشغيل التلقائي عند النقر على الطبقة
    autoplayOverlay.addEventListener('click', function () {
        video.play();
        playBtn.innerHTML = '<i class="fas fa-pause"></i>';
        autoplayOverlay.classList.add('hidden');
    });

    // إظهار الطبقة عند إيقاف الفيديو
    video.addEventListener('pause', function () {
        if (!video.ended) {
            autoplayOverlay.classList.remove('hidden');
        }
    });

    // إعادة التشغيل عند الانتهاء
    video.addEventListener('ended', function () {
        playBtn.innerHTML = '<i class="fas fa-play"></i>';
        autoplayOverlay.classList.remove('hidden');
    });

    // تنسيق الوقت
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // محاولة التشغيل التلقائي (مع التعامل مع سياسات المتصفح)
    const playPromise = video.play();
    if (playPromise !== undefined) {
        playPromise.catch(() => {
            autoplayOverlay.classList.remove('hidden');
        });
    }
});