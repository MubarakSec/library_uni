// بيانات الأسئلة لكل تحدي
const challenges = {
    'python': {
        title: 'تحدي Python الأساسي',
        questions: [
            {
                question: 'ما هي النتيجة التي سيتم طباعتها من الكود التالي؟',
                code: 'x = 5\ny = 2\nprint(x ** y)',
                options: ['25', '10', '7', '3'],
                correct: 0,
                explanation: 'العامل ** في Python يمثل عملية الأس، لذا 5 أس 2 يساوي 25.'
            },
            {
                question: 'ما هي نتيجة الكود التالي؟',
                code: 'print("Hello" + "World")',
                options: ['HelloWorld', 'Hello World', 'Hello+World', 'خطأ'],
                correct: 0,
                explanation: 'عامل + بين النصوص يقوم بدمجها معاً بدون مسافات.'
            }
        ],
        color: 'from-blue-600 to-blue-400'
    },
    'javascript': {
        title: 'تحدي JavaScript الأساسي',
        questions: [
            {
                question: 'ما هي النتيجة التي سيتم طباعتها من الكود التالي؟',
                code: 'let x = 5;\nlet y = 10;\nlet z = x + y * 2;\nconsole.log(z);',
                options: ['25', '30', '15', '20'],
                correct: 0,
                explanation: 'يتم تنفيذ عملية الضرب أولاً (10 * 2 = 20) ثم الجمع (5 + 20 = 25).'
            },
            {
                question: 'ما هو نوع البيانات الذي سيعود به التعبير التالي؟',
                code: 'typeof null',
                options: ['object', 'null', 'undefined', 'string'],
                correct: 0,
                explanation: 'هذا خطأ معروف في JavaScript حيث يعود typeof null بقيمة "object".'
            }
        ],
        color: 'from-yellow-500 to-yellow-300'
    },
    'java': {
        title: 'تحدي Java الأساسي',
        questions: [
            {
                question: 'ما هي الكلمة المفتاحية المستخدمة لتعريف صنف في Java؟',
                code: '',
                options: ['class', 'Class', 'className', 'type'],
                correct: 0,
                explanation: 'الكلمة المفتاحية "class" تستخدم لتعريف صنف جديد في Java.'
            }
        ],
        color: 'from-blue-800 to-blue-600'
    },
    'csharp': {
        title: 'تحدي #C الأساسي',
        questions: [
            {
                question: 'ما هي الكلمة المفتاحية المستخدمة لتعريف متغير ثابت في C#؟',
                code: '',
                options: ['const', 'static', 'final', 'readonly'],
                correct: 0,
                explanation: 'الكلمة المفتاحية "const" تستخدم لتعريف متغير ثابت في C#.'
            }
        ],
        color: 'from-purple-800 to-purple-600'
    },
    'cpp': {
        title: 'تحدي ++C الأساسي',
        questions: [
            {
                question: 'ما هو المشغل المستخدم للحصول على عنوان المتغير في C++؟',
                code: '',
                options: ['&', '*', '->', '::'],
                correct: 0,
                explanation: 'المشغل & يستخدم للحصول على عنوان المتغير في الذاكرة.'
            }
        ],
        color: 'from-blue-900 to-blue-700'
    },


    'python-advanced': {
        title: 'تحدي Python المتقدم',
        questions: [
            {
                question: 'ما هي نتيجة الكود التالي؟',
                code: 'print([x for x in range(10) if x % 2 == 0])',
                options: ['[0, 2, 4, 6, 8]', '[1, 3, 5, 7, 9]', '[0, 1, 2, 3, 4]', 'خطأ'],
                correct: 0,
                explanation: 'هذا كود لإنشاء قائمة بالأعداد الزوجية من 0 إلى 9.'
            }
        ],
        color: 'from-blue-600 to-blue-400'
    },
    'python-data': {
        title: 'تحدي معالجة البيانات مع Python',
        questions: [
            {
                question: 'ما هي الدالة المستخدمة في Pandas لقراءة ملف CSV؟',
                code: '',
                options: ['read_csv()', 'open_csv()', 'load_csv()', 'import_csv()'],
                correct: 0,
                explanation: 'الدالة read_csv() هي الدالة المستخدمة في Pandas لقراءة ملفات CSV.'
            }
        ],
        color: 'from-indigo-600 to-purple-400'
    }
};

// تهيئة التحدي
function startChallenge(challengeId) {
    const challenge = challenges[challengeId];
    if (!challenge) return;

    // تحديث معلومات التحدي
    document.getElementById('challenge-title').textContent = challenge.title;
    document.getElementById('progress-bar').className = `progress-fill bg-gradient-to-r ${challenge.color}`;
    document.getElementById('total-questions').textContent = challenge.questions.length;

    // إخفاء الأقسام الرئيسية وإظهار قسم التحدي
    document.querySelectorAll('main > section').forEach(section => {
        section.classList.add('hidden');
    });
    document.getElementById('challenge-section').classList.remove('hidden');

    // تحميل السؤال الأول
    loadQuestion(challengeId, 0);
}

// تحميل سؤال معين
function loadQuestion(challengeId, questionIndex) {
    const challenge = challenges[challengeId];
    const question = challenge.questions[questionIndex];

    // تحديث شريط التقدم
    const progressPercent = ((questionIndex + 1) / challenge.questions.length) * 100;
    document.getElementById('progress-bar').style.width = `${progressPercent}%`;
    document.getElementById('current-question').textContent = questionIndex + 1;

    // تحديث نقاط التقدم
    updateProgressDots(challenge.questions.length, questionIndex);

    // تحديث أزرار التنقل
    document.getElementById('prev-btn').disabled = questionIndex === 0;
    document.getElementById('next-btn').disabled = questionIndex === challenge.questions.length - 1;

    // إنشاء عناصر السؤال
    const questionContainer = document.getElementById('question-container');
    questionContainer.innerHTML = `
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-6">
                    <h3 class="text-xl font-bold mb-4">${question.question}</h3>
                    ${question.code ? `
                    <div class="code-block">
                        <pre>${question.code}</pre>
                    </div>
                    ` : ''}
                    <div class="space-y-3 mt-6">
                        ${question.options.map((option, i) => `
                        <div class="option rounded-lg p-4 cursor-pointer" data-option="${i}">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 flex items-center justify-center ml-3">
                                    <div class="option-check w-3 h-3 rounded-full bg-indigo-500 hidden"></div>
                                </div>
                                <div>${option}</div>
                            </div>
                        </div>
                        `).join('')}
                    </div>
                    <div class="explanation mt-4 hidden">
                        <h4 class="font-bold mb-2">التفسير:</h4>
                        <p>${question.explanation}</p>
                    </div>
                </div>
            `;

    // إضافة أحداث النقر على الخيارات
    document.querySelectorAll('#question-container .option').forEach(option => {
        option.addEventListener('click', function () {
            const options = this.parentElement.querySelectorAll('.option');

            // إزالة التحديد من جميع الخيارات
            options.forEach(opt => {
                opt.classList.remove('selected', 'correct', 'incorrect');
                opt.querySelector('.option-check').classList.add('hidden');
            });

            // تحديد الخيار المختار
            this.classList.add('selected');
            this.querySelector('.option-check').classList.remove('hidden');

            // التحقق من الإجابة الصحيحة
            if (parseInt(this.dataset.option) === question.correct) {
                this.classList.add('correct');
            } else {
                this.classList.add('incorrect');
                // تحديد الإجابة الصحيحة
                options[question.correct].classList.add('correct');
            }

            // إظهار التفسير
            this.closest('.bg-gray-800').querySelector('.explanation').classList.remove('hidden');
        });
    });
}

// تحديث نقاط التقدم
function updateProgressDots(totalQuestions, currentIndex) {
    const dotsContainer = document.getElementById('progress-dots');
    dotsContainer.innerHTML = '';

    for (let i = 0; i < totalQuestions; i++) {
        const dot = document.createElement('span');
        dot.className = `w-3 h-3 rounded-full ${i <= currentIndex ? 'bg-indigo-500' : 'bg-gray-600'}`;
        dotsContainer.appendChild(dot);
    }
}

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

// تهيئة الصفحة عند التحميل
document.addEventListener('DOMContentLoaded', function () {
    createStars();

    // أحداث ألسنة اللغات
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const language = this.dataset.language;
            document.querySelectorAll('.challenge-card').forEach(card => {
                card.style.display = (language === 'all' || card.dataset.language === language) ? 'block' : 'none';
            });
        });
    });

    // أحداث أزرار بدء التحدي
    document.querySelectorAll('.start-challenge').forEach(btn => {
        btn.addEventListener('click', function () {
            startChallenge(this.dataset.challenge);
        });
    });

    // زر بدء التحدي العام
    document.getElementById('start-general-challenge').addEventListener('click', function () {
        startChallenge('python');
    });

    // زر الخروج من التحدي
    document.getElementById('exit-challenge').addEventListener('click', function () {
        document.getElementById('challenge-section').classList.add('hidden');
        document.querySelectorAll('main > section').forEach(section => {
            section.classList.remove('hidden');
        });
    });

    // أحداث أزرار التنقل بين الأسئلة
    let currentChallenge = '';
    let currentQuestionIndex = 0;

    document.getElementById('prev-btn').addEventListener('click', function () {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            loadQuestion(currentChallenge, currentQuestionIndex);
        }
    });

    document.getElementById('next-btn').addEventListener('click', function () {
        const challenge = challenges[currentChallenge];
        if (currentQuestionIndex < challenge.questions.length - 1) {
            currentQuestionIndex++;
            loadQuestion(currentChallenge, currentQuestionIndex);
        }
    });

    // تحديث المتغيرات عند بدء التحدي
    const originalStartChallenge = startChallenge;
    startChallenge = function (challengeId) {
        currentChallenge = challengeId;
        currentQuestionIndex = 0;
        originalStartChallenge(challengeId);
    };
});

