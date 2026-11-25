<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Đồ án môn Lập trình ứng dụng web - Trường Đại học Văn Lang.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdowns = document.querySelectorAll('.nav-dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const isOpen = dropdown.classList.contains('show');
            dropdowns.forEach(d => d.classList.remove('show'));
            if (!isOpen) {
                dropdown.classList.add('show');
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            dropdowns.forEach(d => d.classList.remove('show'));
        }
    });
});
</script>
</body>
</html>