<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function home(): string
    {
        $data = [
            'title' => 'Daftar Artikel',
            'artikel' => [
                [
                    'judul' => 'Artikel 1',
                    'slug' => 'artikel-1',
                    'isi' => 'Ini adalah isi artikel 1.'
                ],
                [
                    'judul' => 'Artikel 2',
                    'slug' => 'artikel-2',
                    'isi' => 'Ini adalah isi artikel 2.'
                ]
            ],
            'content' => 'Ini adalah halaman utama.'
        ];
        return view('home', $data);
    }
}