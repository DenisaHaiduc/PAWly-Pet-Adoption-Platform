$(document).ready(function() {
    // === 1. SLIDER VERTICAL INTELIGENT  ===
    let timerSlider;
    const $lista = $('.slider-lista');
    const inaltimeElement = 400;

    function pornesteSlider() {
        const secunde = $('#secunde-schimb').val() * 1000;
        clearInterval(timerSlider);
        timerSlider = setInterval(() => mutaInSus(), secunde);
    }

    function mutaInSus() {
        // Tranziție în care imaginile merg în sus 
        $lista.animate({ marginTop: -inaltimeElement }, 800, function() {
            // Cea mai de sus imagine dispare si apare la coada
            //ista se deplaseaza în sus cu o distanta egala cu h unei imagini.
            $lista.append($lista.find('li:first')).css('marginTop', 0);
            gestioneazaMedia();
        });
    }

    function mutaInJos() {
        $lista.prepend($lista.find('li:last')).css('marginTop', -inaltimeElement);
        $lista.animate({ marginTop: 0 }, 800, gestioneazaMedia);
    }

    function gestioneazaMedia() {
        $('video').each(function() { this.pause(); });
        let $videoVizibil = $lista.find('li:first video');
        if ($videoVizibil.length) $videoVizibil[0].play();
    }

    // Evenimente pentru săgeți (dacă există)
    $('.sageata.jos').on('click', mutaInSus);
    $('.sageata.sus').on('click', mutaInJos);

    // === 2. SORTARE TABEL REUTILIZABILĂ  ===
    $('.tabel-stilizat th').on('click', function() {
        const $tabel = $(this).closest('table');
        const indexColoana = $(this).index();
        const $randuri = $tabel.find('tbody tr').toArray();
        let esteAsc = $(this).data('asc');

        $randuri.sort((a, b) => {
            const valA = $(a).children('td').eq(indexColoana).text().toUpperCase();
            const valB = $(b).children('td').eq(indexColoana).text().toUpperCase();
            return esteAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });

        $(this).data('asc', !esteAsc);
        $tabel.find('tbody').append($randuri);
    });

    // === 3. CĂUTARE LIVE ÎN SELECT  ===
    const $searchInput = $('#live-search-input');
    const $searchList = $('#live-search-list');

    $searchInput.on('focus', () => $searchList.fadeIn(200));
    $searchInput.on('keyup', function() {
        const query = $(this).val().toLowerCase();
        $searchList.find('li').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1);
        });
    });

    $searchList.on('click', 'li', function() {
        const selectie = $(this).text();
        $searchInput.val(selectie);
        $searchList.fadeOut(200);
        
        // Filtrare carduri inn functie d selectie
        $('.card-anunt').each(function() {
            const textCard = $(this).text().toLowerCase();
            $(this).toggle(selectie === "Toate" || textCard.indexOf(selectie.toLowerCase()) > -1);
        });
    });

    // === 4. QUIZ INTELIGENT (5 ÎNTREBĂRI) ===
    // Afișare/Ascundere Quiz la click pe Logo
    $('#logo-site').on('click', function() {
        const $quiz = $('.creative-quiz');
        if ($quiz.is(':visible')) {
            $quiz.fadeOut(400);
        } else {
            $quiz.css('display', 'flex').hide().fadeIn(400);
        }
    });

    // Închidere quiz dacă dai click pe overlay (fundal)
    $(document).on('click', '.creative-quiz', function(e) {
        if ($(e.target).hasClass('creative-quiz')) {
            $(this).fadeOut(400);
        }
    });

    const TOTAL_STEPS = 5;
    let quizAnswers = {};

    // Actualizare bară progres
    function updateProgress(step) {
        const percent = (step / TOTAL_STEPS) * 100;
        $('#quiz-progress-bar').css('width', percent + '%');
        if (step <= TOTAL_STEPS) {
            $('#quiz-progress-text').text('Întrebarea ' + step + ' din ' + TOTAL_STEPS);
        }
    }

    // Logica butoanelor quiz
    $('.quiz-card').on('click', '.quiz-btn:not(.restart-btn)', function() {
        const currentDiv = $(this).closest('.quiz-step');
        const step = currentDiv.data('step');
        const val = $(this).data('val');

        quizAnswers[step] = val;

        const nextStep = step + 1;
        const nextDiv = $(`.quiz-step[data-step="${nextStep}"]`);

        currentDiv.fadeOut(300, function() {
            if (nextStep <= TOTAL_STEPS && nextDiv.length > 0) {
                nextDiv.fadeIn(300);
                updateProgress(nextStep);
            } else {
                // Am terminat toate cele 5 întrebări
                $('#quiz-progress-bar').css('width', '100%');
                $('#quiz-progress-text').text('✅ Completat!');
                finalizareQuiz();
            }
        });
    });

    // Generare sclipici / confetti
    function genereazaSclipici() {
        const container = $('#sparkle-container');
        container.empty();
        const culori = ['#955c8f', '#f0883f', '#FFD700', '#FF69B4', '#7B68EE', '#00CED1'];
        for (let i = 0; i < 40; i++) {
            const spark = $('<div class="sparkle"></div>');
            spark.css({
                left: Math.random() * 100 + '%',
                top: (60 + Math.random() * 40) + '%',
                width: (4 + Math.random() * 8) + 'px',
                height: (4 + Math.random() * 8) + 'px',
                background: culori[Math.floor(Math.random() * culori.length)],
                animationDelay: (Math.random() * 1.5) + 's',
                animationDuration: (1.5 + Math.random() * 1.5) + 's'
            });
            container.append(spark);
        }
    }

    // Algoritm de recomandare (bazat pe 5 răspunsuri)
    function finalizareQuiz() {
        let recomandare = '';
        let mesaj = '';
        let emoji = '';

        const loc = quizAnswers[1];      // apartament / curte
        const timp = quizAnswers[2];     // putin / mult
        const exp = quizAnswers[3];      // primul / experimentat
        const energie = quizAnswers[4];  // lenes / activ
        const copii = quizAnswers[5];    // da_copii / nu_copii

        // Logică detaliată de decizie
        if (loc === 'curte' && timp === 'mult' && energie === 'activ') {
            if (copii === 'da_copii') {
                recomandare = 'Golden Retriever';
                mesaj = 'Golden Retriever-ul este blând, loial și excelent cu copiii. Cu curtea ta mare și timpul disponibil, vei avea un partener de joacă perfect!';
                emoji = '🐕';
            } else {
                recomandare = 'Husky Siberian';
                mesaj = 'Husky-ul este energic, aventuros și foarte inteligent. Cu spațiul și timpul tău, va fi un companion extraordinar pentru drumeții!';
                emoji = '🐺';
            }
        } else if (loc === 'apartament' && timp === 'putin' && energie === 'lenes') {
            if (copii === 'da_copii') {
                recomandare = 'Iepure Pitic';
                mesaj = 'Iepurașul pitic este blând, silențios și adorat de copii. Nu necesită plimbări și se simte bine și în apartament!';
                emoji = '🐰';
            } else {
                recomandare = 'Pește Betta';
                mesaj = 'Un Betta colorat este hipnotizant și nu necesită multă îngrijire. Perfect pentru apartament și program aglomerat — un colț de liniște!';
                emoji = '🐠';
            }
        } else if (loc === 'apartament' && energie === 'lenes') {
            recomandare = 'Pisică Siameză';
            mesaj = 'Siameza este elegantă, afectuoasă și vocală. Se atașează puternic de stăpân și se simte minunat într-un apartament confortabil!';
            emoji = '😺';
        } else if (loc === 'curte' && exp === 'experimentat' && energie === 'lenes') {
            recomandare = 'Capră Saanen';
            mesaj = 'Capra Saanen este prietenoasă, inteligentă și produce lapte delicios. Cu experiența ta și curtea disponibilă, va fi o companie minunată!';
            emoji = '🐐';
        } else if (loc === 'curte' && exp === 'experimentat') {
            recomandare = 'Papagal Ara';
            mesaj = 'Papagalul Ara este spectaculos, inteligent și poate învăța să vorbească! Cu experiența ta, va fi un prieten pe viață!';
            emoji = '🦜';
        } else if (copii === 'da_copii' && energie === 'lenes') {
            recomandare = 'Pisică Britanică';
            mesaj = 'Pisica Britanică este calmă, independentă și adorabilă. Se adaptează perfect și este excelentă cu copiii!';
            emoji = '🐱';
        } else if (timp === 'mult' && energie === 'activ') {
            recomandare = 'Labrador Retriever';
            mesaj = 'Labradorul este jucăuș, prietenos și ușor de dresat. Cu timpul tău generos, veți forma o echipă de neuitat!';
            emoji = '🦮';
        } else {
            recomandare = 'Pisică Persană';
            mesaj = 'Persana este regală, calmă și perfectă pentru orice stil de viață. Cu blana ei luxoasă și personalitatea relaxată, va transforma casa ta într-un loc mai cald!';
            emoji = '🐈';
        }

        $('#result-emoji').text(emoji);
        $('#animal-recomandat').text(recomandare);
        $('#mesaj-personalizat').text(mesaj);
        $('#quiz-result-panel').fadeIn(600);
        genereazaSclipici();

        // Filtrare carduri anunțuri (caută specia generală)
        const specieGenerala = recomandare.toLowerCase();
        $('.card-anunt').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.indexOf(specieGenerala) > -1 ||
                text.indexOf(emoji) > -1 ||
                (specieGenerala.includes('pisic') && text.indexOf('pisica') > -1) ||
                (specieGenerala.includes('retriever') && text.indexOf('caine') > -1) ||
                (specieGenerala.includes('husky') && text.indexOf('caine') > -1) ||
                (specieGenerala.includes('labrador') && text.indexOf('caine') > -1)) {
                $(this).show(400);
            } else {
                $(this).hide(400);
            }
        });
    }

    // Restart quiz
    $('#restart-quiz').on('click', function() {
        $('#quiz-result-panel').hide();
        $('#sparkle-container').empty();
        $('.quiz-step').hide();
        $('.quiz-step[data-step="1"]').fadeIn(300);
        quizAnswers = {};
        updateProgress(1);
        $('.card-anunt').fadeIn(400);
        $('#live-search-input').val('');
    });
});
