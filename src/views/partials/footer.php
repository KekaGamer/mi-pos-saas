<?php
// src/views/partials/footer.php
?>
    </div> <footer class="text-center text-muted p-3" style="background-color: rgba(0, 0, 0, 0.05);">
        © <?php echo date("Y"); ?> Mi Sistema POS. Todos los derechos reservados.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const themeToggler = document.getElementById('theme-toggler');
        const htmlElement = document.documentElement;
        const icon = themeToggler.querySelector('i');

        const toggleTheme = () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            if (currentTheme === 'dark') {
                htmlElement.setAttribute('data-bs-theme', 'light');
                icon.classList.remove('bi-sun-fill');
                icon.classList.add('bi-moon-fill');
            } else {
                htmlElement.setAttribute('data-bs-theme', 'dark');
                icon.classList.remove('bi-moon-fill');
                icon.classList.add('bi-sun-fill');
            }
        };

        themeToggler.addEventListener('click', toggleTheme);
    </script>
</body>
</html>