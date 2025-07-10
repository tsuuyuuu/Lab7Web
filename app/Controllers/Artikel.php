<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Artikel extends BaseController
{
    public function index()
    {
        $model = new ArtikelModel();
        $artikel = $model->getArtikelDenganKategori();

        $kategoriModel = new KategoriModel();

        // Ambil slug kategori dari query string
        $kategori_slug = $this->request->getGet('kategori');

        // Ambil semua kategori untuk dropdown
        $kategoriList = $kategoriModel->findAll();

        // Siapkan query builder
        $builder = $model
            ->select('artikel.*, kategori.nama_kategori, kategori.slug_kategori')
            ->join('kategori', 'kategori.id_kategori = artikel.id_kategori');

        // Jika kategori dipilih, filter berdasarkan slug
        if (!empty($kategori_slug)) {
            $builder->where('kategori.slug_kategori', $kategori_slug);
        }

        // Eksekusi query
        $artikel = $builder->findAll();

        // Ubah judul jika kategori dipilih
        $title = 'Daftar Artikel';
        if (!empty($kategori_slug)) {
            $kategori = $kategoriModel->where('slug_kategori', $kategori_slug)->first();
            if ($kategori) {
                $title = 'Kategori: ' . $kategori['nama_kategori'];
            }
        }

        // Kirim ke view
        return view('artikel/index', [
            'title'           => $title,
            'artikel'         => $artikel,
            'kategoriList'    => $kategoriList,
            'kategoriDipilih' => $kategori_slug
        ]);
    }


    public function view($slug)
    {
        $model = new ArtikelModel();
        $artikel = $model
            ->select('artikel.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = artikel.id_kategori')
            ->where('artikel.slug', $slug)
            ->first();

        if (!$artikel) {
            throw PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        return view('artikel/detail', [
            'title' => $artikel['judul'],
            'artikel' => $artikel
        ]);
    }

    public function admin_index()
    {
       $artikelModel = new ArtikelModel();
        $kategoriModel = new KategoriModel();

        $title = 'Manage Article - Admin Panel';

        // Ambil parameter pencarian, filter kategori, nomor halaman
        $q = $this->request->getVar('q');
        $kategori_id = $this->request->getVar('kategori_id');
        $page = $this->request->getVar('page') ?? 1;

        // --- Parameter Sorting ---
        $sort_by = $this->request->getVar('sort_by') ?? 'artikel.id'; // Default sort by ID
        $sort_order = $this->request->getVar('sort_order') ?? 'desc'; // Default order DESC

        $builder = $artikelModel->select('artikel.*, kategori.nama_kategori')
                                ->join('kategori', 'kategori.id_kategori = artikel.id_kategori');

        // Terapkan filter pencarian
        if (!empty($q)) {
            $builder->like('artikel.judul', $q);
        }

        // Terapkan filter kategori
        if (!empty($kategori_id)) {
            $builder->where('artikel.id_kategori', $kategori_id);
        }

        // --- Terapkan Sorting ---
        // Pastikan kolom yang diurutkan sesuai dengan nama kolom di DB
        $builder->orderBy($sort_by, $sort_order);

        // Dapatkan data artikel dengan pagination
        $artikel = $builder->paginate(10, 'default', $page);
        $pager = $artikelModel->pager;

        $data = [
            'title' => $title,
            'artikel' => $artikel,
            'pager' => $pager,
            'q' => $q,
            'kategori_id' => $kategori_id,
            'sort_by' => $sort_by,    // Kirim parameter sorting ke view
            'sort_order' => $sort_order // Kirim parameter sorting ke view
        ];

        // Cek apakah request datang dari AJAX
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($data);
        } else {
            $data['kategori'] = $kategoriModel->findAll();
            return view('artikel/admin_index', $data);
        }
    }

    public function kategori($slug_kategori)
    {
        $kategoriModel = new KategoriModel();
        $artikelModel = new ArtikelModel();

        $kategori = $kategoriModel->where('slug_kategori', $slug_kategori)->first();

        if (!$kategori) {
            throw PageNotFoundException::forPageNotFound("Kategori tidak ditemukan.");
        }

        $artikel = $artikelModel
            ->select('artikel.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = artikel.id_kategori')
            ->where('kategori.slug_kategori', $slug_kategori)
            ->findAll();

        return view('artikel/index', [
            'title' => 'Kategori: ' . $kategori['nama_kategori'],
            'artikel' => $artikel,
            'kategoriList' => $kategoriModel->findAll(),
            'kategoriDipilih' => $slug_kategori
        ]);
    }

    public function add()
    {
        $kategoriModel = new KategoriModel();

        if (
            $this->request->getMethod() === 'POST' &&
            $this->validate([
                'judul' => 'required',
                'id_kategori' => 'required|integer',
                'gambar' => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,2048]'
            ])
        ) {
            $file = $this->request->getFile('gambar');
            $namaGambar = $file->getRandomName();
            $file->move(ROOTPATH . 'public/gambar', $namaGambar);

            $artikelModel = new ArtikelModel();
            $artikelModel->insert([
                'judul'       => $this->request->getPost('judul'),
                'isi'         => $this->request->getPost('isi'),
                'slug'        => url_title($this->request->getPost('judul'), '-', true),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'gambar'      => $namaGambar,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
                'status'      => 'Aktif',
            ]);

            return redirect()->to('/admin/artikel');
        }

        return view('artikel/form_add', [
            'title' => 'Tambah Artikel',
            'kategori' => $kategoriModel->findAll(),
        ]);
    }

    public function edit($id)
    {
        $artikelModel = new ArtikelModel();
        $kategoriModel = new KategoriModel();
        $artikel = $artikelModel->find($id);

        if (!$artikel) {
            throw PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'judul' => 'required',
                'id_kategori' => 'required|integer',
            ];

            if ($this->validate($rules)) {
                $dataUpdate = [
                    'judul' => $this->request->getPost('judul'),
                    'isi' => $this->request->getPost('isi'),
                    'id_kategori' => $this->request->getPost('id_kategori'),
                ];

                $file = $this->request->getFile('gambar');
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $namaGambar = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/gambar', $namaGambar);
                    $dataUpdate['gambar'] = $namaGambar;
                }

                $artikelModel->update($id, $dataUpdate);
                return redirect()->to('/admin/artikel');
            }
        }

        return view('artikel/form_edit', [
            'title' => 'Edit Artikel',
            'artikel' => $artikel,
            'kategori' => $kategoriModel->findAll()
        ]);
    }

    public function delete($id)
    {
        $artikelModel = new ArtikelModel();
        $artikelModel->delete($id);
        return redirect()->to('/admin/artikel');
    }
}