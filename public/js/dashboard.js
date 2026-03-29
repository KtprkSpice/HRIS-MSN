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


    // 📊 ApexCharts - Gender
    
});

// 🔔 Toggle Detail Notifikasi
function toggleDetail(item) {
    const detail = item.querySelector('.notif-detail');
    if (detail) {
        detail.classList.toggle('d-none');
    }
}
