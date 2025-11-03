</main> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Script untuk menandai .nav-link yang aktif
        // Ini adalah cara sederhana, nanti bisa disempurnakan
        document.addEventListener("DOMContentLoaded", function() {
            const currentPath = window.location.pathname.split("/").pop();
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            
            navLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active'); // Hapus 'active' dari dashboard
                }
            });
            
            // Jika tidak ada yang cocok (misal di halaman utama), pastikan Dashboard aktif
            if (currentPath === 'index.php' || currentPath === '') {
                 document.querySelector('.sidebar-nav .nav-link[href="index.php"]').classList.add('active');
            }
        });
    </script>
</body>
</html>