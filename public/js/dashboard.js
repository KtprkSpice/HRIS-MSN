// Jalankan setelah seluruh elemen halaman dimuat
document.addEventListener("DOMContentLoaded", function() {
    // 🌙 Toggle Dark Mode
    const toggleBtn = document.getElementById("toggleModeBtn");
    const body = document.body;
    const moon = document.getElementById("moon-icon");
    const sun = document.getElementById("sun-icon");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            body.classList.toggle("bg-dark");
            moon.classList.toggle("d-none");
            sun.classList.toggle("d-none");
        });
    }

    // 🧾 DataTables (jika tabel ada)
    const table = document.getElementById("karyawanTable");
    if (table) {
        $('#karyawanTable').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            initComplete: function() {
                $('#karyawanTable').fadeIn(500);
            }
        });
    }

    // 📊 ApexCharts - Gender
    const genderChart = document.querySelector("#genderChart");
    if (genderChart) {
        const options = {
            chart: {
                type: 'pie',
                height: 300
            },
            // Dummy data (ubah nanti sesuai data backend)
            series: [12, 8],
            labels: ['Laki-laki', 'Perempuan'],
            colors: ['#120588', '#e83e8c'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 250 }
                }
            }]
        };

        const chart = new ApexCharts(genderChart, options);
        chart.render();
    }
});

// 🔔 Toggle Detail Notifikasi
function toggleDetail(item) {
    const detail = item.querySelector('.notif-detail');
    if (detail) {
        detail.classList.toggle('d-none');
    }
}
