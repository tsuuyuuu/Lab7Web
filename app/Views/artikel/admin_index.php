<?= $this->include('template/header_admin'); ?>

<div class="tabel-heading-gemoy">
    <h3><?= $title; ?></h3>
</div>

<div class="row mb-3">
    <div class="col-md-8">
        <form id="search-form">
            <input type="text" name="q" id="search-box" value="<?= esc($q); ?>"
                placeholder="Cari judul artikel...">

            <select name="kategori_id" id="filter-kategori">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id_kategori']; ?>" <?= ($kategori_id == $k['id_kategori']) ? 'selected' : ''; ?>> <?= $k['nama_kategori']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">🔍 Cari</button>
        </form>

    </div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <!-- Heading Girly -->
        <div class="tabel-heading-gemoy flex-grow-1">
            <h3>📚 Daftar Artikel Kesayangan 💖</h3>
        </div>

        <!-- Tombol Tambah Artikel -->
        <div>
            <a href="<?= base_url('admin/artikel/add'); ?>" class="btn btn-add-girly">➕ Tambah Artikel Baru</a>
        </div>
    </div>

    <!-- Indikator Loading -->
    <div id="loading-indicator" class="text-center text-muted mb-3" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        Memuat data...
    </div>
    <!-- Container untuk Data Artikel yang dimuat via AJAX -->
    <div id="article-container" class="mb-4">
        <!-- Tabel artikel akan dirender di sini -->
    </div>

    <!-- Container untuk Pagination yang dimuat via AJAX -->
    <div id="pagination-container" class="d-flex justify-content-center">
        <!-- Pagination akan dirender di sini -->
    </div>
</div>

<script src="<?= base_url('assets/js/jquery-3.7.1.min.js'); ?>"></script>
<script>
    $(document).ready(function() {
        const baseUrlForImages = "<?= base_url('gambar/'); ?>";
        const adminArtikelBaseUrl = "<?= base_url('admin/artikel'); ?>";
        const articleContainer = $('#article-container');
        const paginationContainer = $('#pagination-container');
        const searchForm = $('#search-form');
        const searchBox = $('#search-box');
        const categoryFilter = $('#filter-kategori');
        const loadingIndicator = $('#loading-indicator');
        let currentSortBy = 'artikel.id';
        let currentSortOrder = 'desc';

        const fetchData = (url) => {
            loadingIndicator.show();
            articleContainer.empty();
            paginationContainer.empty();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    loadingIndicator.hide();
                    renderArticles(response.artikel);
                    renderPagination(response.pager, response.q, response.kategori_id, response.sort_by, response.sort_order);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loadingIndicator.hide();
                    articleContainer.html('<p class="alert alert-danger text-center">Error memuat data: ' + textStatus + ' - ' + errorThrown + '</p>');
                    paginationContainer.html('');
                    console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        };

        const renderArticles = (articles) => {
            let html = '<div class="table-responsive"><table class="table table-hover table-striped table-bordered">';
            html += '<thead class="table-dark">';
            html += '<tr>';
            html += '<th class="sortable" data-sort-by="artikel.id">ID ' + getSortIcon('artikel.id') + '</th>';
            html += '<th class="sortable" data-sort-by="artikel.judul">Judul ' + getSortIcon('artikel.judul') + '</th>';
            html += '<th class="sortable" data-sort-by="kategori.nama_kategori">Kategori ' + getSortIcon('kategori.nama_kategori') + '</th>';
            html += '<th>Gambar</th>';
            html += '<th class="sortable" data-sort-by="artikel.status">Status ' + getSortIcon('artikel.status') + '</th>';
            html += '<th>Aksi</th>';
            html += '</tr>';
            html += '</thead><tbody>';

            if (articles && articles.length > 0) {
                articles.forEach(article => {
                    html += '<tr>';
                    html += '<td>' + article.id + '</td>';
                    html += '<td>';
                    html += '<b>' + (article.judul ? escapeHtml(article.judul) : '') + '</b>';
                    html += '<p><small>' + (article.isi ? escapeHtml(article.isi.substring(0, 50)) + (article.isi.length > 50 ? '...' : '') : '') + '</small></p>';
                    html += '</td>';
                    html += '<td>' + (article.nama_kategori ? escapeHtml(article.nama_kategori) : 'Tanpa Kategori') + '</td>';
                    html += '<td>';
                    if (article.gambar) {
                        html += `<img src="${baseUrlForImages}${article.gambar}" alt="Gambar" style="max-height: 60px; width: auto; object-fit: cover; border-radius: 5px;">`;
                    } else {
                        html += '<span class="text-muted">Tidak ada gambar</span>';
                    }
                    html += '</td>';
                    html += '<td>';
                    let statusClass = (article.status === 'published' || article.status === 'Aktif') ? 'bg-success' : 'bg-secondary';
                    html += '<span class="badge ' + statusClass + '">' + (article.status ? escapeHtml(article.status) : '') + '</span>';
                    html += '</td>';
                    html += '<td>';
                    html += `<a class="btn btn-sm btn-warning me-2" href="${adminArtikelBaseUrl}/edit/${article.id}">Ubah</a>`;
                    html += `<a class="btn btn-sm btn-danger" href="${adminArtikelBaseUrl}/delete/${article.id}" onclick="return confirm('Yakin ingin menghapus data?');">Hapus</a>`;
                    html += '</td>';
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data artikel yang ditemukan.</td></tr>';
            }

            html += '</tbody></table></div>';
            articleContainer.html(html);

            $('.sortable').on('click', function() {
                const sortBy = $(this).data('sort-by');
                let sortOrder = 'asc';
                if (sortBy === currentSortBy) {
                    sortOrder = (currentSortOrder === 'asc') ? 'desc' : 'asc';
                }
                currentSortBy = sortBy;
                currentSortOrder = sortOrder;

                const qVal = searchBox.val();
                const kategoriIdVal = categoryFilter.val();
                fetchData(`${adminArtikelBaseUrl}?q=${qVal}&kategori_id=${kategoriIdVal}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}&page=1`);
            });
        };

        const getSortIcon = (sortByColumn) => {
            if (sortByColumn === currentSortBy) {
                return currentSortOrder === 'asc' ? '<span class="ms-1">&#9650;</span>' : '<span class="ms-1">&#9660;</span>';
            }
            return '<span class="ms-1 text-muted">&#9660;&#9650;</span>';
        };

        const renderPagination = (pager, q, kategori_id, sort_by, sort_order) => {
            let html = '<nav><ul class="pagination">';

            if (pager && pager.pageCount === 1 && pager.total > 0) {
                html += `<li class="page-item active"><a class="page-link" href="#">1</a></li>`;
            } else if (pager && pager.links && pager.links.length > 0) {
                pager.links.forEach(link => {
                    let pageUrl = link.url ? `${link.url}&q=${q || ''}&kategori_id=${kategori_id || ''}&sort_by=${sort_by || 'artikel.id'}&sort_order=${sort_order || 'desc'}` : '#';
                    html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="${pageUrl}">${link.title}</a></li>`;
                });
            }
            html += '</ul></nav>';
            paginationContainer.html(html);

            paginationContainer.find('.page-link').on('click', function(e) {
                e.preventDefault();
                let pageUrl = $(this).attr('href');
                if (pageUrl && pageUrl !== '#') {
                    fetchData(pageUrl);
                }
            });
        };

        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        searchForm.on('submit', function(e) {
            e.preventDefault();
            const qVal = searchBox.val();
            const kategoriIdVal = categoryFilter.val();
            fetchData(`${adminArtikelBaseUrl}?q=${qVal}&kategori_id=${kategoriIdVal}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}&page=1`);
        });

        categoryFilter.on('change', function() {
            searchForm.trigger('submit');
        });
        fetchData(`${adminArtikelBaseUrl}?q=${searchBox.val()}&kategori_id=${categoryFilter.val()}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}&page=1`);
    });
</script>

<?= $this->include('template/footer_admin'); ?>