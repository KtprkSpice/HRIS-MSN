function confirmDelete(id) {
    Swal.fire({
        title: "Apakah kamu yakin?",
        text: "Data ini tidak bisa dikembalikan setelah dihapus!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("deleteForm" + id).submit();
        }
    });
}

const salary = document.getElementById("salary");

salary.addEventListener("input", function () {
    let value = this.value.replace(/\D/g, "");
    this.value = new Intl.NumberFormat("id-ID").format(value);
});

form.addEventListener("submit", function () {
    salary.value = salary.value.replace(/\./g, "");
});
