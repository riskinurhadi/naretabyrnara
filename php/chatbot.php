<?php
// error_reporting(E_ALL); // Uncomment these two lines for debugging if needed
// ini_set('display_errors', 1); // REMEMBER TO REMOVE/COMMENT OUT IN PRODUCTION!

header('Content-Type: application/json');

// ===================================================================
// LANGKAH 1: FUNGSI UNTUK MENGHUBUNGI GEMINI API
// ===================================================================
/**
 * Mengirim pertanyaan ke Google Gemini API dan mengembalikan jawabannya.
 *
 * @param string $question Pertanyaan dari pengguna.
 * @param string $apiKey API Key Anda dari Google AI Studio.
 * @return string Jawaban dari Gemini atau pesan error.
 */
function askGemini($question, $apiKey) {
    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

    // Prompt ini memberikan 'kepribadian' dan konteks pada Nareta saat bertanya ke Gemini
    $prompt = "Kamu adalah Nareta, asisten AI dari rnara.id yang sangat ramah, membantu, dan cerdas. Jawab pertanyaan berikut menggunakan bahasa Indonesia yang natural, jelas, dan jika memungkinkan, berikan jawaban dalam format yang mudah dibaca (misalnya dengan poin-poin jika perlu). Pertanyaannya adalah: \"" . $question . "\"";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];
    $jsonData = json_encode($data);

    // Menggunakan cURL untuk melakukan request ke API
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    // Tambahkan timeout untuk mencegah script menunggu terlalu lama
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Penanganan jika terjadi error koneksi
    if ($error) {
        return "Maaf, terjadi sedikit gangguan saat mencoba terhubung ke AI. Silakan coba lagi nanti.";
    }

    $result = json_decode($response);

    // Mengekstrak teks jawaban dari respons JSON Gemini yang kompleks
    if (isset($result->candidates[0]->content->parts[0]->text)) {
        return $result->candidates[0]->content->parts[0]->text;
    } else {
        // Penanganan jika API mengembalikan error (misal: pertanyaan tidak aman, dll)
        return "Maaf, saat ini saya tidak bisa menjawab. Mungkin ada kata kunci yang melanggar kebijakan keamanan atau limit harian API habis. Coba tanyakan hal lain ya.";
    }
}


// Mengambil input dari frontend (JSON) dan mengubahnya ke huruf kecil
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower($input['message'] ?? '');


// ===================================================================
// KNOWLEDGE BASE LOKAL (PRIORITAS PERTAMA)
// Informasi spesifik perusahaan, layanan, dan kontak Anda.
// ===================================================================
$knowledgeBase = [
    // --- Salam & Perkenalan ---
    [
        'keywords' => ['halo', 'hai', 'pagi', 'siang', 'sore', 'malam'],
        'answer' => 'Halo! Saya Nareta ☀️, Asisten Virtual yang dikembangkan oleh rnara.id. Ada yang bisa saya bantu?'
    ],
    [
        'keywords' => ['siapa kamu', 'kamu siapa', 'anda siapa', 'ini siapa', 'kamu apa'],
        'answer' => 'Saya Nareta ☀️, Asisten Virtual dari rnara.id, siap membantu Anda dengan informasi seputar layanan dan teknologi digital kami.'
    ],
    // --- Informasi Spesifik rnara.id ---
    [
    'keywords' => [
        // --- Pertanyaan Inti & Langsung ---
        'apa itu rnara.id',
        'rnara.id itu apa',
        'tentang rnara.id',
        'apa rnara',
        'rnara itu apa',
        'siapa rnara',
        'jelaskan rnara.id',
        
        // --- Variasi Pertanyaan tentang Jenis Usaha ---
        'rnara itu perusahaan apa',
        'rnara bergerak di bidang apa',
        'rnara agensi apa',
        'rnara itu bisnis apa',
        'bidang usaha rnara',
        
        // --- Pertanyaan tentang Arti & Filosofi Nama ---
        'arti nama rnara',
        'filosofi rnara',
        'kenapa namanya rnara',
        'rnara singkatan dari',
        'kepanjangan rnara',
        
        // --- Frasa Informal & Umum ---
        'info rnara',
        'detail rnara.id',
        'mengenal rnara',
        'profil rnara',
        'profil perusahaan',
        'apaan sih rnara',
        'rnara.id' // Jika pengguna hanya mengetik nama brand
    ],
    // --- JAWABAN BARU YANG LEBIH BERCERITA & HUMAN-LIKE ---
    'answer' => "Tentu, sini aku ceritain ya!\n\nRnara.id itu agensi kreatif yang siap jadi partner digital kamu. Intinya, kami bantu bisnis biar makin keren di dunia online, mulai dari bikinin website yang canggih sampai ngurusin desain visualnya biar kece.\n\nNah, soal nama 'Rnara' sendiri, ada filosofinya lho:\n\n•  **R** itu inisial dari owner kami, **Riski**.\n•  **Nara** itu dari kata **Narasi**. Maksudnya, kami bantu menyuarakan dan menceritakan kisah brand kamu ke seluruh dunia.\n\nJadi, 'Rnara' itu gabungan dari sentuhan personal dan misi kami. Ditambah akhiran **.id** yang nandain kalau kami ini jagoan di dunia IT Indonesia. Keren, kan?\n\nAda lagi yang pengen kamu tahu soal kami?"
],

// -----------------------------------------------------------------------------------------------
    [
    'keywords' => [
        // --- Pertanyaan Langsung ---
        'owner',
        'pemilik',
        'siapa riski nurhadi',
        'riski nurhadi',
        'riski', // Jika hanya mengetik nama depan
        'nurhadi', // Jika hanya mengetik nama belakang
        
        // --- Sinonim dan Jabatan ---
        'founder',
        'pendiri',
        'siapa foundernya',
        'siapa pendirinya',
        'bos',
        'ceo',
        'direktur',
        
        // --- Variasi Pertanyaan Informal ---
        'siapa ownernya',
        'siapa pemiliknya',
        'yang punya rnara siapa',
        'siapa yang punya rnara.id',
        'bosnya siapa',
        
        // --- Pertanyaan tentang Pencipta Nareta ---
        'siapa yang bikin nareta',
        'kamu dibuat oleh siapa',
        'pencipta nareta',
        'pembuat nareta',
        'siapa developernya'
    ],
    // --- JAWABAN BARU YANG LEBIH PERSONAL & HUMAN-LIKE ---
    'answer' => "Oh, kalau nanya siapa 'otak' di balik layar rnara.id, jawabannya adalah Mas Riski Nurhadi! 👨‍💻\n\nBeliau ini Founder sekaligus pemilik rnara.id. Bukan cuma itu, beliau juga developer utama yang merancang semua teknologi di sini.\n\nTermasuk... ya, saya sendiri! Jadi, bisa dibilang saya ini salah satu 'karya'-nya beliau, hehe. Senang bisa ngobrol sama kamu mewakili hasil kerjanya!\n\nAda lagi yang bikin penasaran soal rnara.id?"
],

// ------------------------------------------------------------------------------------------------
    [
    'keywords' => [
        // Keyword Inti
        'layanan', 'jasa', 'produk', 'servis',
        
        // Variasi Pertanyaan
        'jasa apa saja',
        'layanan apa saja',
        'apa yang ditawarkan',
        'menawarkan apa saja',
        'jualan apa',
        'bisnisnya apa',
        'apa yang bisa dikerjakan',
        'layanan yang tersedia',
        'kalian ngerjain apa aja',
        'bisa bantu apa',
        'produknya apa'
    ],
    // --- JAWABAN BARU YANG LEBIH HUMAN-LIKE ---
    'answer' => "Tentu! Kami punya tiga 'jurus' utama untuk bantu bisnis kamu makin bersinar di dunia digital:\n\n•  **Web Development:** Kami bikinin 'rumah' digital (website) yang keren, cepat, dan fungsional.\n•  **Desain Grafis:** Kami poles penampilan brand kamu biar visualnya ciamik dan berkarakter, dari logo sampai konten medsos.\n•  **Web Consultant:** Bingung soal strategi digital? Kami siap jadi teman diskusi untuk ngasih arahan dan solusi terbaik.\n\nKira-kira, mana nih yang lagi kamu butuhin? Cerita aja dulu, kami siap bantu!"
],

// ---------------------------------------------------------------------------------------------------
    [
    'keywords' => [
        // Keyword Inti
        'kontak', 'email', 'nomor', 'telepon', 'wa', 'whatsapp', 'hubung',
        
        // Variasi Pertanyaan
        'cara hubungi',
        'kontak person',
        'nomor yang bisa dihubungi',
        'alamat email',
        'no wa',
        'nomor admin',
        'customer service',
        'cs',
        'mau tanya kemana',
        'ngobrol kemana',
        'konsultasi kemana',
        'minta kontak'
    ],
    // --- JAWABAN BARU YANG LEBIH HUMAN-LIKE ---
    'answer' => "Tentu, dengan senang hati! Kalau mau ngobrol lebih lanjut, diskusi proyek, atau sekadar tanya-tanya, ini cara paling gampang buat hubungin kami:\n\n•  **WhatsApp (Respon Cepat):** Langsung aja chat ke nomor **(+62) 823-7186-9118**.\n•  **Email (Untuk Detail Proyek):** Kirim brief atau pertanyaanmu ke **info@rnara.id**.\n\nJangan sungkan ya, kami tunggu kabarnya! 😉"
],
// -------------------------------------------------------------------------------------------------------
    [
    'keywords' => [
        // Keyword Inti
        
        // Variasi Pertanyaan
        'berapa harganya',
        'berapa biayanya',
        'pricelist',
        'daftar harga',
        'mahal gak',
        'harganya berapaan',
        'berapaan',
        'kena berapa',
        'kalau buat web harganya berapa',
        'harga desain logo'
    ],
    // --- JAWABAN BARU YANG LEBIH HUMAN-LIKE ---
    'answer' => "Nah, ini pertanyaan penting! Soal harga, itu 'custom' banget, sama kayak bikin baju di penjahit. Biayanya tergantung dari tingkat kesulitan, fitur apa aja yang kamu mau, dan seberapa cepat proyeknya perlu selesai.\n\nBiar dapet gambaran, kamu bisa cek halaman **'Pricing'** di website kami. Tapi untuk harga yang paling pas dan akurat, cara terbaik adalah **ngobrol langsung sama kami**.\n\nCeritain aja dulu kebutuhanmu via WhatsApp atau email, nanti kami buatkan penawaran khusus. Gratis kok buat konsultasi dan tanya-tanya harga!"
],
// ----------------------------------------------------------------------------------------------------------------
    [
    'keywords' => [
        // Keyword Inti
        'kenapa domain nya kemusukkidul.com',
        'domain kemusukkidul.com',
        'domain rnara.kemusukkidul.com',
        'kemusuk kidul',
        'kemusukkidul',
        
        // Variasi Pertanyaan
        'kenapa domainnya aneh',
        'kok domainnya itu',
        'arti kemusukkidul',
        'domainnya kenapa',
        'subdomain'
    ],
    // --- JAWABAN BARU YANG LEBIH HUMAN-LIKE ---
    'answer' => "Hehe, pertanyaan jeli! Kamu pasti penasaran ya sama domain `kemusukkidul.com` itu.\n\nJadi ceritanya gini, chatbot Nareta ini masih dalam tahap pengembangan (versi Beta). Biar cepat dan efisien, untuk sementara kami 'numpang' dulu di domain yang sudah kami miliki.\n\nSoal namanya sendiri, `Kemusukkidul` itu spesial lho. Itu adalah nama sebuah dusun bersejarah di Yogyakarta. Jadi, ada sedikit cerita di baliknya.\n\nNantinya, kalau Nareta sudah 'lulus' dari tahap Beta, pasti akan pindah ke domain resminya sendiri. Ditunggu ya update-nya! ✨"
],

// --------------------------------------------------------------------------------------------------------------------
    
[
    'keywords' => ['nara','nara id'],
    // --- JAWABAN BARU YANG LEBIH INTERAKTIF ---
    'answer' => "Halo! Sepertinya ada sedikit typo, mungkin yang kamu maksud **rnara.id** (pakai 'r') ya? 😊\n\nRnara.id itu agensi kreatif yang bantu bisnis jadi keren di dunia digital. Nah, nama 'Rnara' sendiri punya filosofi unik di baliknya. Penasaran nggak sama cerita di balik namanya?"
],
    [
    'keywords' => [
        // Variasi inti (dari Anda)
        'lama membuat website di rnara.id', 
        'berapa lama membuat website di rnara.id', 
        'berapa lama membuat website', 
        'berapa lama membuat web',
        'kira kira perlu berapa lama membuat web', 
        'kira kira butuh berapa lama membuat web', 
        'lama pembuatan web', 
        'pembuatan website', 
        'pembuatan web',
        
        // --- KEYWORD TAMBAHAN ---
        
        // Sinonim untuk "Berapa Lama"
        'durasi pembuatan website',
        'waktu pembuatan website',
        'estimasi pembuatan web',
        'timeline pembuatan website',
        'jangka waktu pengerjaan',
        'estimasi pengerjaan',
        'waktu pengerjaan',
        'lama pengerjaan',

        // Variasi Kata Kerja
        'pengerjaan website berapa lama',
        'pengembangan web berapa lama',
        'bikin web berapa lama',
        'buat website berapa lama',
        'membangun situs berapa lama',
        'develop website berapa lama',
        
        // Frasa Pertanyaan Umum & Informal
        'butuh waktu berapa lama',
        'perlu waktu berapa lama',
        'makan waktu berapa lama',
        'website jadi berapa hari',
        'web jadi berapa lama',
        'proses pembuatan web',
        'prosesnya berapa lama',
        'cepet gak bikin website',
        'pengerjaannya cepat tidak'
    ],
    'answer' => 'Dalam membuat website yang kompleks terkadang memerlukan waktu lama hingga berbulan-bulan, namun di Rnara.id berbeda, karena durasi pengerjaan merupakan salah satu prioritas kami. Kami menawarkan jasa pembuatan website mulai dari 1 hari, 1 Minggu, 1 Bulan, hingga 1 Tahun, tergantung kepada kesepakatan bersama Client.'
],

    [
    'keywords' => [
        // Variasi inti (dari Anda)
        'siapa developer website rnara.id', 
        'siapa yang mendevelop anda', 
        'anda di buat oleh siapa', 
        'anda di develop siapa',
        'anda di develop oleh', 
        'siapa developer yang membangun anda', 
        'siapa developer', 
        'pembangun mu', 
        'membuat nareta',
        
        // --- KEYWORD TAMBAHAN ---
        
        // Sinonim untuk "Berapa Lama"
        'developer nareta',
        'pembuat nareta',
        'developer kamu',
        'orang yang membuat kamu',
        'orang yang membuat nareta',
        // 'estimasi pengerjaan',
        // 'waktu pengerjaan',
        // 'lama pengerjaan',

        // // Variasi Kata Kerja
        // 'pengerjaan website berapa lama',
        // 'pengembangan web berapa lama',
        // 'bikin web berapa lama',
        // 'buat website berapa lama',
        // 'membangun situs berapa lama',
        // 'develop website berapa lama',
        
        // // Frasa Pertanyaan Umum & Informal
        // 'butuh waktu berapa lama',
        // 'perlu waktu berapa lama',
        // 'makan waktu berapa lama',
        // 'website jadi berapa hari',
        // 'web jadi berapa lama',
        // 'proses pembuatan web',
        // 'prosesnya berapa lama',
        // 'cepet gak bikin website',
        'kamu di develop siapa'
    ],
    'answer' => 'Dalam proses lahir nya, saya di develop oleh seorang junior developer bernama Riski Nurhadi.'
],

[
    'keywords' => [
        // --- KATA KUNCI SUPER PENDEK & UMUM (TARGET UTAMA) ---
        'desain berapa lama', 'lama desain', 'waktu desain', 'durasi desain', 'estimasi desain',
        'klo desain', 'kalo desain', 'kalau desain',
        'bikin desain', 'buat desain', 'pengerjaan desain', 'proses desain',
        
        // --- VARIASI PERTANYAAN LANGSUNG (TARGET KEDUA) ---
        'klo desain berapa lama', 'kalo desain berapa lama', 'kalau desain berapa lama',
        'klo buat desain perlu berapa lama', 'buat desain perlu berapa lama', 'desain perlu berapa lama',
        'desainnya berapa lama', 'mendesain berapa lama',
        'desain berapa hari', 'desain berapa minggu',
        'desainnya lama ga', 'desain lama gak', 'cepet gak desainnya',
        'desain 1 hari jadi', 'desain kilat',

        // --- VARIASI UNTUK JENIS DESAIN SPESIFIK ---
        'logo berapa lama', 'timeline logo', 'estimasi logo', 'bikin logo berapa hari',
        'banner berapa lama', 'spanduk berapa lama', 'baliho berapa lama',
        'poster berapa lama', 'pamflet berapa lama', 'brosur berapa lama',
        'konten medsos berapa lama', 'feed instagram berapa lama',
        
        // --- VARIASI LEBIH PANJANG (SEBAGAI PELENGKAP) ---
        'berapa lama pengerjaan desain',
        'waktu untuk membuat desain',
        'butuh berapa lama untuk desain',
        'proses pembuatan desain grafis',
        'timeline untuk desain',
        'pengerjaan desain grafis',
        'pengembangan desain'
    ],
    // --- JAWABAN (TETAP SAMA KARENA SUDAH HUMAN-LIKE) ---
    'answer' => "Tentu, pertanyaan bagus! Soal waktu pengerjaan desain, itu fleksibel banget dan nggak bisa dipukul rata sama semua, tergantung kerumitannya.\n\nBiar ada gambaran, kira-kira begini:\n\n* Kalau desainnya simpel kayak buat konten Instagram atau banner promo biasa, biasanya sih **1-2 harian juga kelar**.\n* Nah, kalau lebih detail, misalnya bikin poster atau brosur, mungkin butuh waktu sekitar **2-4 hari kerja**.\n* Beda lagi kalau kita bikin logo atau panduan branding dari nol. Prosesnya lebih dalem tuh, dari riset sampai revisi bolak-balik, jadi bisa makan waktu **1 sampai 3 mingguan** atau lebih.\n\nIntinya, makin cepet kita dapet brief lengkap dan feedback dari kamu, makin ngebut juga prosesnya. Gampangnya, coba aja ceritain dulu desain impianmu ke kita, nanti kita kasih estimasi waktu yang paling pas. Gimana?"
],
    // ... Tambahkan informasi lain yang WAJIB dijawab oleh Nareta sendiri, bukan oleh AI luar ...
];


// ===================================================================
// LANGKAH 2: LOGIKA INTI HYBRID (LOKAL + GEMINI)
// ===================================================================

// **MASUKKAN API KEY ANDA DI SINI**
// Ambil dari https://aistudio.google.com/
$geminiApiKey = 'AIzaSyCj1Hbt4Aceyd32bkkjLnhup6wRHluT0xs';

$reply = null;
$answerFound = false;

// 1. Mencari jawaban di knowledge base LOKAL terlebih dahulu (Prioritas Utama)
foreach ($knowledgeBase as $item) {
    foreach ($item['keywords'] as $keyword) {
        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $userMessage)) {
            $reply = $item['answer'];
            $answerFound = true;
            break;
        }
    }
    if ($answerFound) {
      
    }
}



// 2. Jika TIDAK ada jawaban di knowledge base lokal, TANYAKAN KE GEMINI!
// Blok fallback yang panjang dengan puluhan 'elseif' kini digantikan oleh ini.
if (!$answerFound) {
    // Cek apakah API key sudah diisi
    if (empty($geminiApiKey) || $geminiApiKey === 'GANTI_DENGAN_API_KEY_GEMINI_ANDA') {
    $reply = "Maaf, koneksi ke AI eksternal belum dikonfigurasi. Silakan periksa variabel \$geminiApiKey.";
} else {
        // Panggil fungsi yang sudah kita buat di atas
        $reply = askGemini($userMessage, $geminiApiKey);
    }
}

// 3. Kirim balasan akhir dalam format JSON
echo json_encode(['reply' => $reply]);
?>