<footer class="footer mt-auto py-3 bg-white border-top">
        <div class="container text-center">
            <span class="text-muted small">
                &copy; <?php echo date("Y"); ?> <b>23552011324_Daffa Zulfan Zainal_Cns B_UasWeb1</b>. All Rights Reserved.
            </span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // 1. LOGIKA KONFIRMASI LOGOUT
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Anda harus login kembali untuk mengakses panel admin.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            })
        });
    }

    // 2. LOGIKA KONFIRMASI HAPUS BOOKING (Menggunakan Event Delegation)
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-hapus-booking')) {
            const btn = e.target.closest('.btn-hapus-booking');
            const id = btn.getAttribute('data-id');
            
            Swal.fire({
                title: 'Hapus Data Booking?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'data_booking.php?hapus=' + id;
                }
            });
        }
    });

    // 3. LOGIKA KONFIRMASI HAPUS FASILITAS
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-hapus-fasilitas')) {
            const btn = e.target.closest('.btn-hapus-fasilitas');
            const id = btn.getAttribute('data-id');
            
            Swal.fire({
                title: 'Hapus Lapangan?',
                text: "Seluruh data terkait lapangan ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'data_fasilitas.php?hapus=' + id;
                }
            });
        }
    });
    </script>
</body>
</html>