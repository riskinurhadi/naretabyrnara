<?php
// Load schedule data from JSON file
$schedule_file = 'schedule.json';
$schedule_data = [];
if (file_exists($schedule_file)) {
    $schedule_data = json_decode(file_get_contents($schedule_file), true);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Harian Alpii</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-grad-start: #fde4ea; 
            --bg-grad-end: #faf3e0;   
            --text-dark: #5d5463;     
            --accent-pink: #ff7bac;   
            --clock-border: #e0c5c6;  
            --text-light: #8b8195;    
            /* === WARNA BARU: Untuk garis pembatas === */
            --divider-color: rgba(224, 197, 198, 0.8); 
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(to bottom, var(--bg-grad-start), var(--bg-grad-end));
            overflow: hidden;
        }

        .main-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: center;
            padding: 20px 0;
            text-align: center;
        }
        
        .header-title h1 {
            font-family: 'Pacifico', cursive;
            color: var(--text-dark);
            font-size: 2rem;
            margin: 0;
        }
        .header-title p {
            color: var(--text-light);
            margin: 0;
            font-size: 0.9rem;
        }

        .clock-container {
            position: relative;
            width: 90vw;
            max-width: 380px;
            aspect-ratio: 1 / 1;
        }
        .clock {
            width: 100%;
            height: 100%;
            border: 5px solid var(--clock-border);
            border-radius: 50%;
            position: relative;
            background-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
        .clock-center {
            position: absolute;
            width: 12%; 
            height: 12%;
            background-color: var(--accent-pink);
            border: 3px solid white;
            border-radius: 50%;
            z-index: 11;
            cursor: pointer; 
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .clock-center::before {
             content: '';
             display: block;
             width: 0;
             height: 0;
             border-top: 6px solid transparent;
             border-bottom: 6px solid transparent;
             border-left: 10px solid white;
             margin-left: 5px;
             transition: all 0.2s ease;
        }
        .clock-center.playing::before {
            width: 10px;
            height: 10px;
            border: none;
            background-color: white;
            margin-left: 0;
        }
        
        .hand {
            position: absolute;
            top: 50%;
            left: 50%;
            transform-origin: left center;
            border-radius: 4px;
        }
        #hour-hand {
            width: 25%;
            height: 5px;
            background-color: var(--text-dark);
            z-index: 8;
        }
        #minute-hand {
            width: 35%;
            height: 4px;
            background-color: var(--text-dark);
            z-index: 9;
        }
        #second-hand {
            width: 42%;
            height: 2px;
            background-color: var(--accent-pink);
            z-index: 10;
        }
        
        .hour-marker { display: none; }
        .hour-number {
            position: absolute;
            transform: translate(-50%, -50%);
            color: var(--text-light);
            font-size: clamp(0.9rem, 3vw, 1.1rem);
            font-weight: 500;
        }
        .activity-label {
            position: absolute;
            transform: translate(-50%, -50%);
            padding: 3px 10px;
            font-size: clamp(0.7rem, 2vw, 0.8rem);
            white-space: nowrap;
            z-index: 6;
            color: var(--text-dark);
            font-weight: 500;
        }

        /* === CSS BARU: Untuk garis pembatas kegiatan === */
        .activity-divider {
            position: absolute;
            top: 50%;
            left: 50%;
            height: 1px; /* Ketebalan garis */
            background-color: var(--divider-color);
            transform-origin: left center;
            z-index: 5; /* Di bawah label, di atas background */
        }

        .btn-custom {
            background-color: var(--accent-pink);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(255, 123, 172, 0.5);
            transition: all 0.2s ease-in-out;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 123, 172, 0.6);
        }

    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-title">
            <h1>Jadwal Nyantai Alpii</h1>
            <p>inspired by Bae Seok Ryu</p>
        </div>

        <div class="clock-container">
            <div class="clock" id="clock">
                </div>
        </div>
        
        <a href="settings.php" class="btn-custom">Ubah Jadwal</a>
    </div>

    <audio id="background-music" src="music.mp3" loop></audio>

    <script>
        const scheduleData = <?php echo json_encode($schedule_data, JSON_UNESCAPED_UNICODE); ?>;
        const clock = document.getElementById('clock');

        // Fungsi untuk mengonversi waktu (e.g., "14:30") ke total jam desimal
        function timeToHours(timeStr) {
            const [h, m] = timeStr.split(':').map(Number);
            return h + m / 60;
        }
        
        // Fungsi untuk membuat elemen garis pembatas
        function createDivider(angle, length) {
            const divider = document.createElement('div');
            divider.className = 'activity-divider';
            divider.style.width = `${length}px`;
            divider.style.transform = `rotate(${angle}deg)`;
            return divider;
        }

        function renderFullClock() {
            clock.innerHTML = '';
            const clockRadius = clock.offsetWidth / 2;

            clock.innerHTML += `
                <div class="clock-center" id="play-button"></div>
                <div id="hour-hand" class="hand"></div>
                <div id="minute-hand" class="hand"></div>
                <div id="second-hand" class="hand"></div>
            `;
            
            for (let i = 1; i <= 24; i++) {
                if (i % 2 === 0) {
                    const angle = (i / 24) * 360 - 90;
                    const angleRad = angle * (Math.PI / 180);
                    const number = document.createElement('div');
                    number.className = 'hour-number';
                    number.textContent = i;
                    const numberX = Math.cos(angleRad) * (clockRadius * 0.82);
                    const numberY = Math.sin(angleRad) * (clockRadius * 0.82);
                    number.style.left = `calc(50% + ${numberX}px)`;
                    number.style.top = `calc(50% + ${numberY}px)`;
                    clock.appendChild(number);
                }
            }

            if (scheduleData) {
                scheduleData.forEach(activity => {
                    const startHours = timeToHours(activity.start);
                    const endHours = timeToHours(activity.end);
                    
                    // === LOGIKA BARU: Membuat Garis Pembatas ===
                    const dividerLength = clockRadius - (clock.offsetWidth * 0.05); // Panjang garis dari pusat ke tepi
                    
                    // Buat garis untuk waktu MULAI
                    const startAngle = (startHours / 24) * 360 - 90;
                    clock.appendChild(createDivider(startAngle, dividerLength));
                    
                    // Buat garis untuk waktu SELESAI
                    const endAngle = (endHours / 24) * 360 - 90;
                    clock.appendChild(createDivider(endAngle, dividerLength));

                    // --- Logika untuk menempatkan label (tidak berubah) ---
                    let midTotalHours = (startHours + (endHours < startHours ? endHours + 24 : endHours)) / 2;
                    if (midTotalHours >= 24) midTotalHours -= 24;
                    const labelAngle = (midTotalHours / 24) * 360 - 90;
                    const labelAngleRad = labelAngle * (Math.PI / 180);
                    const labelDistance = clockRadius * 0.60;
                    const label = document.createElement('div');
                    label.className = 'activity-label';
                    label.textContent = activity.name;
                    label.style.color = activity.color || 'var(--text-dark)';
                    const labelX = Math.cos(labelAngleRad) * labelDistance;
                    const labelY = Math.sin(labelAngleRad) * labelDistance;
                    label.style.left = `calc(50% + ${labelX}px)`;
                    label.style.top = `calc(50% + ${labelY}px)`;
                    clock.appendChild(label);
                });
            }
            setupPlayButton();
        }
        
        function updateClockHands() {
            const hourHand = document.getElementById('hour-hand');
            const minuteHand = document.getElementById('minute-hand');
            const secondHand = document.getElementById('second-hand');
            if (!hourHand || !minuteHand || !secondHand) return;
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const seconds = now.getSeconds();
            const secondsAngle = (seconds / 60) * 360 - 90;
            const totalMinutes = minutes + seconds / 60;
            const minutesAngle = (totalMinutes / 60) * 360 - 90;
            const totalHours = hours + minutes / 60;
            const hoursAngle = (totalHours / 24) * 360 - 90;
            secondHand.style.transform = `rotate(${secondsAngle}deg)`;
            minuteHand.style.transform = `rotate(${minutesAngle}deg)`;
            hourHand.style.transform = `rotate(${hoursAngle}deg)`;
        }
        
        function setupPlayButton() {
            const playButton = document.getElementById('play-button');
            const music = document.getElementById('background-music');
            if (playButton) {
                if(!music.paused) playButton.classList.add('playing');
                playButton.addEventListener('click', () => {
                    if (music.paused) {
                        music.play();
                        playButton.classList.add('playing');
                    } else {
                        music.pause();
                        playButton.classList.remove('playing');
                    }
                });
            }
        }
        
        renderFullClock();
        updateClockHands();
        setInterval(updateClockHands, 1000);
        window.addEventListener('resize', renderFullClock);
    </script>
</body>
</html>