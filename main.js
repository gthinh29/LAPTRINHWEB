document.addEventListener('DOMContentLoaded', function () {

    console.log("Chào mừng đến với Blog! Script đã sẵn sàng.");

    khoiTaoNutLenDauTrang();
    khoiTaoHieuUngBaiViet();

});



function khoiTaoNutLenDauTrang() {
    const nutLenDauTrang = document.getElementById('scrollTopBtn');

    if (!nutLenDauTrang) {
        return;
    }
    window.addEventListener('scroll', function () {

        if (window.scrollY > 200) {
            nutLenDauTrang.style.display = 'block';
        } else {
            nutLenDauTrang.style.display = 'none';
        }
    });

    nutLenDauTrang.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

function khoiTaoHieuUngBaiViet() {
    const cacBaiViet = document.querySelectorAll('.post-item');

    if (cacBaiViet.length === 0) {
        return;
    }
    cacBaiViet.forEach((baiViet, index) => {
        baiViet.style.opacity = '0';
        baiViet.style.transform = 'translateY(20px)';
        baiViet.style.animation = `hieuUngXuatHien 0.6s ease-out ${index * 0.1}s forwards`;
    });
}
