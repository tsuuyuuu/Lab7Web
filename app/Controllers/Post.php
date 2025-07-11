<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait; // Penting untuk helper respons API
use App\Models\ArtikelModel; // Import ArtikelModel

class Post extends ResourceController // Menggunakan ResourceController untuk RESTful API
{
    use ResponseTrait; // Memungkinkan penggunaan respond(), respondCreated(), respondDeleted(), failNotFound()

    protected $format    = 'json'; // Mengatur format respons default menjadi JSON

    // Method: GET /post - Menampilkan seluruh data artikel
    public function index()
    {
        $model = new ArtikelModel();
        // Mengurutkan data berdasarkan ID secara DESC
        $data['artikel'] = $model->orderBy('id', 'DESC')->findAll();
        // Mengembalikan data dalam format JSON
        return $this->respond($data);
    }

    // Method: POST /post - Menambahkan data artikel baru
    public function create()
    {
        $model = new ArtikelModel();

        // Aturan validasi
        $rules = [
            'judul' => 'required|min_length[3]',
            'isi'   => 'required|min_length[10]'
        ];

        // Jalankan validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan respons error
            $response = [
                'status'  => 400, // Bad Request
                'error'   => $this->validator->getErrors(),
                'messages' => [
                    'error' => 'Data tidak valid.'
                ]
            ];
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Siapkan data untuk disimpan
        $data = [
            'judul'       => $this->request->getVar('judul'),
            'isi'         => $this->request->getVar('isi'),
            'slug'        => url_title($this->request->getVar('judul'), '-', true), // Buat slug otomatis
            'status'      => 'published', // Default status, bisa disesuaikan
            // 'id_kategori' => $this->request->getVar('id_kategori') ?? null, // Tambahkan ini jika artikel memiliki kategori
            // 'gambar'      => $this->request->getVar('gambar') ?? null // Tambahkan ini jika ada kolom gambar
        ];

        // Simpan data ke database
        $inserted = $model->insert($data);

        if ($inserted) {
            $response = [
                'status'   => 201, // Created
                'error'    => null,
                'messages' => [
                    'success' => 'Data artikel berhasil ditambahkan.'
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status'   => 500, // Internal Server Error
                'error'    => 'Gagal menyimpan data ke database.',
                'messages' => [
                    'error' => 'Terjadi kesalahan saat menambahkan artikel.'
                ]
            ];
            return $this->failServerError($response['error']);
        }
    }

    // Method: GET /post/{id} - Menampilkan satu data artikel spesifik
    public function show($id = null)
    {
        $model = new ArtikelModel();
        $data = $model->where('id', $id)->first();

        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Data tidak ditemukan.');
        }
    }

    // Method: PUT/PATCH /post/{id} - Mengubah data artikel
    public function update($id = null)
    {
        $model = new ArtikelModel();

        // Aturan validasi
        $rules = [
            'judul' => 'required|min_length[3]',
            'isi'   => 'required|min_length[10]'
        ];

        // Periksa apakah request method adalah PUT atau POST (untuk form HTML)
        // CodeIgniter RESTful ResourceController secara otomatis akan menangani _method spoofing untuk PUT/PATCH dari POST
        $input = $this->request->getRawInput(); // Mengambil semua input, termasuk JSON/x-www-form-urlencoded

        // Jika data tidak dari getRawInput() (misal dari form-data), coba getPost()
        if (empty($input)) {
            $input = $this->request->getPost();
        }

        // Lakukan validasi data input
        if (!$this->validate($rules, $input)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Siapkan data untuk update
        $data = [
            'judul' => $input['judul'],
            'isi'   => $input['isi'],
            'slug'  => url_title($input['judul'], '-', true),
            // Tambahkan kolom lain yang relevan seperti 'id_kategori', 'gambar', 'status'
        ];

        // Lakukan update data
        $updated = $model->update($id, $data);

        if ($updated) {
            $response = [
                'status'   => 200, // OK
                'error'    => null,
                'messages' => [
                    'success' => 'Data artikel berhasil diubah.'
                ]
            ];
            return $this->respond($response);
        } else {
            // Ini akan mencakup kasus di mana ID tidak ditemukan atau tidak ada perubahan data
            $existingArticle = $model->find($id);
            if (!$existingArticle) {
                return $this->failNotFound('Data tidak ditemukan untuk ID: ' . $id);
            } else {
                $response = [
                    'status'   => 200, // OK (jika tidak ada perubahan data, tetap 200)
                    'error'    => 'Tidak ada perubahan data atau gagal menyimpan.',
                    'messages' => [
                        'info' => 'Artikel ditemukan, tetapi tidak ada perubahan data atau update gagal.'
                    ]
                ];
                return $this->respond($response);
            }
        }
    }


    // Method: DELETE /post/{id} - Menghapus data artikel
    public function delete($id = null)
    {
        $model = new ArtikelModel();
        $find = $model->find($id); // Cari artikel terlebih dahulu

        if ($find) {
            $deleted = $model->delete($id);
            if ($deleted) {
                $response = [
                    'status'   => 200, // OK
                    'error'    => null,
                    'messages' => [
                        'success' => 'Data artikel berhasil dihapus.'
                    ]
                ];
                return $this->respondDeleted($response); // Menggunakan respondDeleted
            } else {
                $response = [
                    'status'   => 500, // Internal Server Error
                    'error'    => 'Gagal menghapus data dari database.',
                    'messages' => [
                        'error' => 'Terjadi kesalahan saat menghapus artikel.'
                    ]
                ];
                return $this->failServerError($response['error']);
            }
        } else {
            return $this->failNotFound('Data tidak ditemukan.');
        }
    }
}
