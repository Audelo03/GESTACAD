</main> </div> 
<script>


    function initTooltips() {
        var oldTooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        oldTooltipList.map(function (tooltipEl) {
            var tooltip = bootstrap.Tooltip.getInstance(tooltipEl);
            if (tooltip) {
                tooltip.dispose();
            }
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Ejecutar la función una vez cuando la página carga por primera vez
    document.addEventListener('DOMContentLoaded', function () {
        initTooltips();
        });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- El código del sidebar ahora está en app.js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.0/dist/umd/simple-datatables.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachLogoutHandler = (linkEl) => {
        if (!linkEl) return;
        linkEl.addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de cerrar tu sesión actual.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = linkEl.href;
                }
            });
        });
    };

    attachLogoutHandler(document.getElementById('logout-link'));
    attachLogoutHandler(document.getElementById('logout-link-mobile'));
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const mainElement = document.querySelector('main');

    if (mainElement) {
        mainElement.addEventListener('click', async function(e) {
            const pageLink = e.target.closest('.pagination[data-id-grupo] a.page-link');

            if (!pageLink) {
                return;
            }

            e.preventDefault();

            const parentLi = pageLink.parentElement;

            if (parentLi.classList.contains('disabled')) {
                return;
            }

            const paginationUl = pageLink.closest('.pagination');
            const id_grupo = paginationUl.dataset.idGrupo; // data-id-grupo se convierte en idGrupo
            const totalPages = parseInt(paginationUl.dataset.totalPages, 10);
            let currentPage = parseInt(paginationUl.dataset.currentPage, 10);
            const studentListContainer = document.getElementById(`lista-alumnos-${id_grupo}`);

            if (parentLi.dataset.role === 'prev') {
                currentPage--;
            } else if (parentLi.dataset.role === 'next') {
                currentPage++;
            }

            studentListContainer.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    

            const params = new URLSearchParams({
                action: 'load_students',
                id_grupo: id_grupo,
                page: currentPage
            });
            const url = `/GORA/alumnos-paginados?${params.toString()}`;

            try {
                // Realizar la petición con fetch
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const data = await response.json();

                studentListContainer.innerHTML = data.html;

                paginationUl.dataset.currentPage = currentPage;

                const pageIndicator = paginationUl.querySelector('li[data-role="page-indicator"] span.page-link');
                if (pageIndicator) {
                    pageIndicator.textContent = `${currentPage} de ${totalPages}`;
                }

                const prevButton = paginationUl.querySelector('li[data-role="prev"]');
                if (prevButton) {
                    prevButton.classList.toggle('disabled', currentPage === 1);
                }

                const nextButton = paginationUl.querySelector('li[data-role="next"]');
                if (nextButton) {
                    nextButton.classList.toggle('disabled', currentPage === totalPages);
                }
                if (typeof initTooltips === 'function') {
                    initTooltips();
                }

            } catch (error) {
                studentListContainer.innerHTML = '<div class="alert alert-danger">Error al cargar la lista de alumnos.</div>';
                console.error("Error de Fetch:", error);
            }
           
        });
    }
});
</script>

</body>
</html>
