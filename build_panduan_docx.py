# -*- coding: utf-8 -*-
"""
Script Generator Dokumen Word Panduan Proyek Pemrograman Web Dasar
Target:
1. /Users/rosidin/Downloads/Stmik Ikmi Cirebon_28agust26_0704/Ganjil 2026_2027/Pemrograman Web Dasar/Projek/Panduan Projek - Sistem Informasi Inventaris Gudang.docx
2. /Users/rosidin/Sites/inventaris-gudang-si/Panduan Projek - Sistem Informasi Inventaris Gudang.docx
"""

import os
import sys
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

def set_cell_background(cell, hex_color):
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    cell._tc.get_or_add_tcPr().append(shd)

def set_cell_margins(cell, top=80, bottom=80, left=120, right=120):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def set_cell_border(cell, **kwargs):
    tcPr = cell._tc.get_or_add_tcPr()
    tcBorders = parse_xml(f'<w:tcBorders {nsdecls("w")}/>')
    for edge in ('top', 'left', 'bottom', 'right', 'insideH', 'insideV'):
        edge_data = kwargs.get(edge)
        if edge_data:
            val = edge_data.get('val', 'single')
            color = edge_data.get('color', 'D0D7DE')
            sz = edge_data.get('sz', '4')
            tag = f'<w:{edge} {nsdecls("w")} w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
            tcBorders.append(parse_xml(tag))
    tcPr.append(tcBorders)

def build_panduan():
    doc = Document()

    # Page Setup A4 & Margin 2.0 cm
    for s in doc.sections:
        s.page_width = Cm(21.0)
        s.page_height = Cm(29.7)
        s.top_margin = Cm(2.0)
        s.bottom_margin = Cm(2.0)
        s.left_margin = Cm(2.0)
        s.right_margin = Cm(2.0)

    def add_p(text, bold=False, italic=False, size_pt=12, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=0, space_after=4, color_rgb=(0,0,0)):
        p = doc.add_paragraph()
        p.alignment = align
        p.paragraph_format.space_before = Pt(space_before)
        p.paragraph_format.space_after = Pt(space_after)
        p.paragraph_format.line_spacing = 1.15
        r = p.add_run(text)
        r.bold = bold
        r.italic = italic
        r.font.name = "Times New Roman"
        r.font.size = Pt(size_pt)
        r.font.color.rgb = RGBColor(*color_rgb)
        return p

    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        r = p.add_run(text)
        r.bold = True
        r.font.name = "Times New Roman"
        r.font.size = Pt(14)
        r.font.color.rgb = RGBColor(15, 44, 89)
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        r = p.add_run(text)
        r.bold = True
        r.font.name = "Times New Roman"
        r.font.size = Pt(12)
        r.font.color.rgb = RGBColor(27, 79, 137)
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(8)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.keep_with_next = True
        r = p.add_run(text)
        r.bold = True
        r.italic = True
        r.font.name = "Times New Roman"
        r.font.size = Pt(12)
        r.font.color.rgb = RGBColor(30, 41, 59)
        return p

    def add_bullet(prefix, text):
        p = doc.add_paragraph()
        p.paragraph_format.left_indent = Inches(0.25)
        p.paragraph_format.space_before = Pt(1)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.line_spacing = 1.15
        r_pre = p.add_run("• " + prefix)
        r_pre.bold = True
        r_pre.font.name = "Times New Roman"
        r_pre.font.size = Pt(11.5)
        r_pre.font.color.rgb = RGBColor(15, 44, 89)
        r_txt = p.add_run(text)
        r_txt.font.name = "Times New Roman"
        r_txt.font.size = Pt(11.5)
        return p

    def add_code_block(title, code_lines):
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        cell = tbl.rows[0].cells[0]
        set_cell_background(cell, "F8FAFC")
        set_cell_margins(cell, top=90, bottom=90, left=130, right=130)
        set_cell_border(cell, top={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                              bottom={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                              left={'sz': 12, 'val': 'single', 'color': '2563EB'},
                              right={'sz': 4, 'val': 'single', 'color': 'CBD5E1'})
        
        # Header Label
        p_hdr = cell.paragraphs[0]
        p_hdr.paragraph_format.space_before = Pt(0)
        p_hdr.paragraph_format.space_after = Pt(4)
        r_lbl = p_hdr.add_run(f"📁 {title}\n")
        r_lbl.bold = True
        r_lbl.font.name = "Courier New"
        r_lbl.font.size = Pt(9.5)
        r_lbl.font.color.rgb = RGBColor(37, 99, 235)

        # Code content
        code_str = "\n".join(code_lines) if isinstance(code_lines, list) else code_lines
        r_code = p_hdr.add_run(code_str)
        r_code.font.name = "Courier New"
        r_code.font.size = Pt(9.0)
        r_code.font.color.rgb = RGBColor(15, 23, 42)

        doc.add_paragraph().paragraph_format.space_before = Pt(4)

    # ==================== COVER & HEADER ====================
    add_p("MODUL PRAKTIKUM & PANDUAN PROYEK PEMROGRAMAN WEB DASAR", bold=True, size_pt=15, align=WD_ALIGN_PARAGRAPH.CENTER, color_rgb=(15, 44, 89))
    add_p("Sistem Informasi Manajemen Inventaris & Logistik Gudang Pabrik (AutoStock SCM)", bold=True, size_pt=13, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=8, color_rgb=(37, 99, 235))
    add_p("STMIK IKMI CIREBON — TAHUN AKADEMIK 2026/2027", bold=True, size_pt=11, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=12, color_rgb=(100, 116, 139))

    # Info Table Box
    tbl_info = doc.add_table(rows=4, cols=2)
    tbl_info.alignment = WD_TABLE_ALIGNMENT.CENTER
    info_data = [
        ("Mata Kuliah / Beban", "Pemrograman Web Dasar (3 SKS Praktikum Terpadu)"),
        ("Program Studi Sasaran", "S1 Sistem Informasi (SI) & D3 Komputerisasi Akuntansi (KA)"),
        ("Teknologi Inti", "PHP Native 8.3+, MySQL / MariaDB, PDO Prepared Statements, HTML5 & Tailwind CSS"),
        ("Lokasi Repositori Proyek", "/Users/rosidin/Sites/inventaris-gudang-si (http://localhost:8383/ atau http://localhost:8000/inventaris-gudang-si/)")
    ]
    for idx, (k, v) in enumerate(info_data):
        c1, c2 = tbl_info.rows[idx].cells
        c1.text = k
        c2.text = v
        for c in (c1, c2):
            set_cell_margins(c, top=50, bottom=50, left=70, right=70)
            set_cell_background(c, "F1F5F9" if idx % 2 == 0 else "FFFFFF")
            set_cell_border(c, top={'sz': 4, 'val': 'single', 'color': 'E2E8F0'},
                               bottom={'sz': 4, 'val': 'single', 'color': 'E2E8F0'},
                               left={'sz': 4, 'val': 'single', 'color': 'E2E8F0'},
                               right={'sz': 4, 'val': 'single', 'color': 'E2E8F0'})
            for p in c.paragraphs:
                p.paragraph_format.line_spacing = 1.05
                p.paragraph_format.space_after = Pt(0)
                for r in p.runs:
                    r.font.name = "Times New Roman"
                    r.font.size = Pt(10)
        c1.paragraphs[0].runs[0].bold = True

    add_p("", space_after=10)

    # ==================== BAB 1 ====================
    add_h1("BAB 1: PENDAHULUAN & PENYELARASAN SILABUS PERKULIAHAN")
    add_h2("1.1 Latar Belakang & Filosofi 'Web Dasar Ramah Pemula'")
    add_p(
        "Mata kuliah Pemrograman Web Dasar pada hakikatnya bertujuan membekali mahasiswa semester awal dengan pondasi berpikir algoritmik "
        "yang kokoh dalam membangun aplikasi web interaktif berbasis arsitektur client-server. Pada rancangan terdahulu, proyek sering kali "
        "terjebak dalam kompleksitas yang terlalu tinggi—seperti pencatatan akuntansi double-entry, sistem pendukung keputusan (SPK) berbobot matriks matematis, "
        "hingga penggunaan router regex PSR-4 OOP—yang justru membingungkan mahasiswa dan menyulitkan dosen saat live coding di depan kelas."
    )
    add_p(
        "Modul proyek 'AutoStock SCM' (Sistem Informasi Manajemen Inventaris & Logistik Gudang Pabrik) ini dirancang khusus dengan pendekatan "
        "yang sangat mendasar, membumi, dan logis. Mahasiswa diajak memahami siklus hidup pengelolaan barang nyata di industri: "
        "penerimaan barang dari supplier (stok masuk) dan pengeluaran barang untuk kebutuhan mesin/operasional pabrik (stok keluar). "
        "Tidak ada rumus matematika rumit; seluruh komponen berfokus pada penguasaan CRUD murni, validasi formulir, dan keamanan data."
    )

    add_h2("1.2 Pemetaan Materi Perkuliahan P9 s/d P15 menuju Proyek Mini")
    add_p(
        "Seluruh baris kode dalam proyek ini disusun secara linier mencerminkan topik-topik perkuliahan yang telah dipelajari mahasiswa:"
    )
    add_bullet("Pertemuan 9 (Sintaks & Integrasi HTML): ", "Struktur kode PHP yang disisipkan rapi ke dalam dokumen HTML5 (header, navbar, footer).")
    add_bullet("Pertemuan 10 (Percabangan & Array): ", "Penentuan status stok kritis (if $stok <= $stok_min) dan perulangan foreach array asosiatif.")
    add_bullet("Pertemuan 11 (Form GET & POST): ", "Pengambilan data formulir input tambah/edit barang dan pencarian filter via metode GET/POST.")
    add_bullet("Pertemuan 12 (Session & Cookie): ", "Mekanisme session_start(), penyimpanan identitas user di $_SESSION, dan fungsi checkAuth().")
    add_bullet("Pertemuan 13 (MySQL & PHP CRUD): ", "Operasi lengkap Create, Read, Update, Delete menggunakan PDO Prepared Statements murni.")
    add_bullet("Pertemuan 14 (Keamanan Web Dasar): ", "Proteksi terhadap celah SQL Injection dengan parameter bound (:kode) dan XSS dengan htmlspecialchars().")
    add_bullet("Pertemuan 15 (Validasi & Password Hash): ", "Penggunaan password_hash() dengan algoritma BCRYPT dan verifikasi password_verify().")

    # ==================== BAB 2 ====================
    add_h1("BAB 2: STANDAR KEAMANAN WEB DASAR & PRAKTIK TERBAIK (OWASP TOP 10)")
    add_h2("2.1 Penanggulangan SQL Injection (SQLi) dengan PDO Prepared Statements")
    add_p(
        "SQL Injection merupakan kerentanan fatal nomor satu di mana penyerang menyisipkan perintah SQL berbahaya ke dalam parameter input. "
        "Dalam proyek ini, seluruh mahasiswa dilarang keras menggabungkan variabel langsung (string concatenation) seperti query string konvensional. "
        "Seluruh eksekusi wajib melalui tahap prepare() dan execute():"
    )
    add_code_block("Contoh Penerapan PDO Prepared Statement Bebas SQLi", [
        "// ❌ CARA BERBAHAYA (Rentan SQL Injection):",
        "$sql = \"SELECT * FROM users WHERE username = '\" . $_POST['username'] . \"'\";",
        "",
        "// ✅ CARA AMAN STANDAR INDUSTRI (PDO Parameterized Query):",
        "$stmt = $db->prepare(\"SELECT * FROM users WHERE username = :username LIMIT 1\");",
        "$stmt->execute([':username' => $username]);",
        "$user = $stmt->fetch();"
    ])

    add_h2("2.2 Pencegahan Cross-Site Scripting (XSS) dengan htmlspecialchars()")
    add_p(
        "Serangan XSS terjadi ketika data teks yang dimasukkan pengguna mengandung skrip JavaScript berbahaya (<script>alert('hack')</script>). "
        "Setiap data dinamis yang dicetak ke antarmuka HTML wajib disanitasi menggunakan htmlspecialchars($data, ENT_QUOTES, 'UTF-8'):"
    )
    add_code_block("Pencegahan XSS pada Output HTML", [
        "<!-- Mencetak nama barang dengan aman -->",
        "<td class=\"font-bold\"><?= htmlspecialchars($barang['nama_barang']) ?></td>"
    ])

    add_h2("2.3 Otentikasi Terenkripsi & Keamanan Sesi")
    add_p(
        "Kata sandi tidak boleh disimpan dalam format teks polos (plain-text) di database. Proyek ini mendemokan penyimpanan hash aman "
        "dengan fungsi bawaan PHP modern: password_hash($password, PASSWORD_DEFAULT) saat pendaftaran akun, dan diverifikasi dengan password_verify()."
    )

    # ==================== BAB 3 ====================
    add_h1("BAB 3: PERANCANGAN BASIS DATA & PEMBAGIAN MODUL KELOMPOK")
    add_h2("3.1 Skema Basis Data Relasional (db_inventaris_gudang)")
    add_p(
        "Basis data terdiri atas 5 tabel relasional yang ringkas dan padat. Skema ini telah dinormalisasi hingga bentuk normal ketiga (3NF):"
    )
    add_bullet("1. users: ", "Menyimpan data akun kredensial petugas dan administrator (id, username, password, nama_lengkap, role).")
    add_bullet("2. kategori: ", "Menyimpan pengelompokan jenis barang logistik pabrik (id, kode_kategori, nama_kategori, keterangan).")
    add_bullet("3. supplier: ", "Menyimpan master data rekanan/distributor resmi (id, kode_supplier, nama_supplier, telepon, alamat).")
    add_bullet("4. barang: ", "Menyimpan katalog suku cadang mesin (id, kode_barang, kategori_id, nama_barang, satuan, harga_beli, harga_jual, stok, stok_min).")
    add_bullet("5. transaksi_stok: ", "Menyimpan histori mutasi masuk dan keluar (id, no_transaksi, barang_id, supplier_id, jenis_transaksi, jumlah, tanggal, keterangan, petugas).")

    add_h2("3.2 Matriks Pembagian 4 Modul CRUD Per Anggota Kelompok")
    add_p(
        "Sesuai ketentuan BAB IV pada Template Laporan Akhir Mahasiswa (Template Laporan Projek Prodi SI), setiap anggota kelompok "
        "wajib mempertanggungjawabkan 1 modul CRUD secara mandiri dengan porsi yang setara:"
    )

    tbl_kel = doc.add_table(rows=5, cols=4)
    tbl_kel.alignment = WD_TABLE_ALIGNMENT.CENTER
    kel_data = [
        [("Modul CRUD", True), ("Tanggung Jawab Anggota", True), ("Fitur Fungsionalitas Modul", True), ("Relasi & Validasi Khusus", True)],
        [
            ("Modul 1:\nKategori Barang", False),
            ("Anggota 1\n(Ketua Kelompok)", False),
            ("• Menampilkan daftar kategori & total item barang\n• Form tambah kategori baru\n• Form ubah data kategori\n• Hapus data kategori", False),
            ("Validasi kode kategori unik (uppercase). Proteksi penghapusan jika kategori masih memiliki barang terikat.", False)
        ],
        [
            ("Modul 2:\nBarang / Sparepart", False),
            ("Anggota 2", False),
            ("• Katalog suku cadang & sparepart mesin\n• Form registrasi barang + dropdown kategori\n• Form pembaruan rincian & harga barang\n• Hapus data barang", False),
            ("Deteksi otomatis status stok kritis (stok <= stok_min) dengan badge peringatan merah. Filter data berdasarkan kategori.", False)
        ],
        [
            ("Modul 3:\nSupplier Rekanan", False),
            ("Anggota 3", False),
            ("• Daftar supplier & vendor resmi\n• Form tambah supplier baru\n• Form edit informasi kontak & alamat\n• Hapus supplier", False),
            ("Pelacakan total frekuensi pasokan barang dari supplier ke gudang pabrik. Validasi format nomor kontak.", False)
        ],
        [
            ("Modul 4:\nMutasi Stok Masuk & Keluar", False),
            ("Anggota 4", False),
            ("• Papan histori transaksi keluar-masuk\n• Form pencatatan mutasi stok baru\n• Otomatis update stok fisik di tabel barang\n• Pembatalan transaksi & rollback stok", False),
            ("Validasi pencegahan stok minus (barang keluar tidak boleh melebihi sisa fisik). Transaksi ACID aman.", False)
        ]
    ]

    for r_idx, row in enumerate(kel_data):
        for c_idx, (val, is_hdr) in enumerate(row):
            c = tbl_kel.rows[r_idx].cells[c_idx]
            p = c.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            p.paragraph_format.line_spacing = 1.05
            r = p.add_run(val)
            r.font.name = "Times New Roman"
            r.font.size = Pt(9.5)
            if is_hdr:
                r.bold = True
                set_cell_background(c, "E2E8F0")
                r.font.color.rgb = RGBColor(15, 23, 42)
            else:
                set_cell_background(c, "FFFFFF" if r_idx % 2 != 0 else "F8FAFC")
            set_cell_margins(c, top=50, bottom=50, left=60, right=60)
            set_cell_border(c, top={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               bottom={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               left={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               right={'sz': 4, 'val': 'single', 'color': 'CBD5E1'})

    add_p("", space_after=8)

    # ==================== BAB 4 ====================
    add_h1("BAB 4: DESAIN ARSITEKTUR DIREKTORI & ALUR EKSEKUSI (REQUEST LIFECYCLE)")
    add_h2("4.1 Struktur Direktori Proyek Transparan")
    add_p(
        "Proyek disusun dengan struktur direktori modular berbasis halaman (Modular Page-Based) yang sangat transparan, "
        "sehingga mahasiswa dapat langsung melihat korelasi antara file URL peramban dengan file kode pemrosesnya di VS Code:"
    )
    add_code_block("Pohon Struktur Direktori inventaris-gudang-si", [
        "inventaris-gudang-si/",
        "├── index.php             <- Halaman Web Frontend / Landing Page publik (katalog & cek stok)",
        "├── dashboard.php         <- Halaman Dashboard operasional petugas (terproteksi checkAuth)",
        "├── setup.php             <- Inisialisasi basis data otomatis 1-klik via peramban web",
        "├── database.sql          <- Skema SQL MySQL & data benih awal (seeder)",
        "├── config/",
        "│   └── database.php      <- Multi-Environment BASE_URL, helper getDB() & checkAuth()",
        "├── auth/",
        "│   ├── login.php         <- Form login petugas (verifikasi password_hash BCRYPT)",
        "│   ├── logout.php        <- Pembersihan sesi $_SESSION",
        "│   └── index.php         <- Fallback router pengalihan otomatis ke login.php",
        "├── layout/",
        "│   ├── header.php        <- Navigasi atas, identitas sesi, dan tombol Web Publik",
        "│   └── footer.php        <- Copyright kampus & dialog konfirmasi hapus data",
        "└── modul/",
        "    ├── kategori/         <- Modul CRUD 1: Kategori Barang (Anggota 1)",
        "    │   ├── index.php     <- Read (Tabel data kategori)",
        "    │   ├── tambah.php    <- Create (Form & simpan kategori)",
        "    │   ├── edit.php      <- Update (Form & ubah kategori)",
        "    │   └── hapus.php     <- Delete (Penghapusan aman)",
        "    ├── barang/           <- Modul CRUD 2: Katalog Suku Cadang (Anggota 2)",
        "    │   ├── index.php     <- Read & filter kategori barang",
        "    │   ├── tambah.php    <- Create + dropdown relasi kategori",
        "    │   ├── edit.php      <- Update data barang & stok minimum",
        "    │   └── hapus.php     <- Delete data barang",
        "    ├── supplier/         <- Modul CRUD 3: Rekanan Supplier (Anggota 3)",
        "    │   ├── index.php     <- Read (Daftar rekanan resmi)",
        "    │   ├── tambah.php    <- Create (Registrasi supplier baru)",
        "    │   ├── edit.php      <- Update (Ubah kontak supplier)",
        "    │   └── hapus.php     <- Delete supplier",
        "    └── transaksi/        <- Modul CRUD 4: Mutasi Masuk/Keluar (Anggota 4)",
        "        ├── index.php     <- Read (Histori mutasi stok)",
        "        ├── tambah.php    <- Create + Transaksi ACID & validasi anti-stok minus",
        "        └── hapus.php     <- Delete transaksi & rollback stok fisik"
    ])

    # ==================== BAB 5 ====================
    add_h1("BAB 5: IMPLEMENTASI KODE SUMBER MASTER ACUAN & BEDAH LOGIKA")
    add_p(
        "Bab ini membedah cuplikan kode program inti dari setiap komponen agar dosen dan mahasiswa dapat memahami logika pemrosesannya secara ringkas:"
    )

    add_h2("5.1 Koneksi Database PDO & BASE_URL Dinamis (config/database.php)")
    add_p(
        "Konfigurasi 'BASE_URL' dirancang otomatis multi-environment sehingga aplikasi langsung berjalan mulus di port mandiri (8383), port web server utama (8000), maupun XAMPP tanpa perlu mengubah baris kode:"
    )
    add_code_block("Deteksi Dinamis BASE_URL & Koneksi PDO Bebas SQLi", [
        "// 1. Deteksi Cerdas BASE_URL Multi-Environment",
        "$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';",
        "$host = $_SERVER['HTTP_HOST'] ?? 'localhost';",
        "$scriptName = str_replace('\\\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');",
        "$projectFolder = basename(dirname(__DIR__)); // Dinamis: 'inventaris-gudang-si'",
        "$pos = strpos($scriptName, '/' . $projectFolder);",
        "",
        "if ($pos !== false) {",
        "    $baseSub = substr($scriptName, 0, $pos + strlen('/' . $projectFolder));",
        "} else {",
        "    $baseSub = ''; // Dijalankan di port mandiri misal localhost:8383",
        "}",
        "define('BASE_URL', rtrim($protocol . $host . $baseSub, '/'));",
        "",
        "// 2. Fungsi Koneksi PDO Singleton Bebas Celah",
        "function getDB(): PDO {",
        "    static $pdo = null;",
        "    if ($pdo === null) {",
        "        // String koneksi lengkap basis data (Data Source Name)",
        "        $data_source_name = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';",
        "        $options = [",
        "            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,",
        "            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,",
        "            PDO::ATTR_EMULATE_PREPARES   => false, // Menutup celah SQL Injection",
        "        ];",
        "        $pdo = new PDO($data_source_name, DB_USER, DB_PASS, $options);",
        "    }",
        "    return $pdo;",
        "}"
    ])

    add_h2("5.2 Transaksi ACID pada Mutasi Stok (modul/transaksi/tambah.php)")
    add_p(
        "Komponen ini mendemokan mekanisme transaksi basis data (Atomic, Consistent, Isolated, Durable) di mana "
        "pencatatan transaksi dan pembaruan stok fisik di tabel barang harus berhasil bersamaan atau dibatalkan seluruhnya (rollBack) jika terjadi galat:"
    )
    add_code_block("Implementasi Transaksi PDO & Validasi Stok Minus", [
        "// 1. Validasi pencegahan stok minus jika mutasi keluar",
        "if ($jenisTransaksi === 'Keluar' && $barangData['stok'] < $jumlah) {",
        "    die('Stok fisik tidak mencukupi untuk dikeluarkan!');",
        "}",
        "",
        "// 2. Eksekusi Transaksi ACID Berpasangan",
        "$db->beginTransaction();",
        "$stmtInsert->execute([':no' => $noTrx, ':bid' => $barangId, ':jml' => $jumlah, ...]);",
        "",
        "if ($jenisTransaksi === 'Masuk') {",
        "    $sqlUpdate = \"UPDATE barang SET stok = stok + :jml WHERE id = :id\";",
        "} else {",
        "    $sqlUpdate = \"UPDATE barang SET stok = stok - :jml WHERE id = :id\";",
        "}",
        "$stmtUpdate = $db->prepare($sqlUpdate);",
        "$stmtUpdate->execute([':jml' => $jumlah, ':id' => $barangId]);",
        "",
        "$db->commit(); // Komit permanen jika kedua query sukses"
    ])

    add_h2("5.3 Otomasi Basis Data Mandiri & Seeder 1-Klik (setup.php)")
    add_p(
        "Untuk mencegah kendala mahasiswa saat melakukan impor basis data secara manual di phpMyAdmin (seperti galat batas ukuran unggah berkas, konfigurasi timeout, atau salah ketik nama database), proyek ini dilengkapi alat migrasi mandiri berbasis web 'setup.php':"
    )
    add_code_block("Logika Auto-Migrator Berbasis PDO (setup.php)", [
        "// 1. Terhubung ke server database tanpa memilih database spesifik terlebih dahulu",
        "$pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS, [",
        "    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION",
        "]);",
        "",
        "// 2. Baca seluruh perintah SQL dari database.sql (DDL tabel & DML seeder)",
        "$sqlFile = __DIR__ . '/database.sql';",
        "$sqlContent = file_get_contents($sqlFile);",
        "",
        "// 3. Eksekusi massal pembentukan database, 5 tabel, dan data awal",
        "$pdo->exec($sqlContent);",
        "$message = 'Basis data ' . DB_NAME . ' beserta 5 tabel dan data awal berhasil diinisialisasi 100%!';"
    ])
    add_p(
        "Mekanisme ini mengenalkan mahasiswa pada konsep dasar 'Database Migration' yang umum dipakai pada standar industri perangkat lunak modern, "
        "namun diimplementasikan secara murni dengan fungsi bawaan PHP native PDO::exec() yang transparan dan mudah dipahami mahasiswa pemula."
    )

    # ==================== BAB 6 ====================
    add_h1("BAB 6: PANDUAN PRAKTIKUM STEP-BY-STEP MAHASISWA & PANDUAN DOSEN")
    add_p(
        "Panduan langkah demi langkah ini dirancang singkat, padat, dan jelas untuk memandu mahasiswa dari tahap inisialisasi awal hingga penguasaan seluruh fitur aplikasi:"
    )

    add_h2("6.1 Langkah 1: Persiapan Server & Inisialisasi Database (1 Menit)")
    add_bullet("1. Pastikan Database Aktif: ", "Pastikan layanan MariaDB/MySQL berjalan di Port 3306.")
    add_bullet("2. Buka Auto-Migrator: ", "Buka peramban ke tautan: http://localhost:8383/setup.php (atau http://localhost:8000/inventaris-gudang-si/setup.php).")
    add_bullet("3. Eksekusi Migrasi: ", "Klik tombol hijau 'Inisialisasi Database Sekarang'. Seluruh 5 tabel relasional dan akun dummy otomatis terpasang tanpa perlu perintah terminal.")

    add_h2("6.2 Langkah 2: Eksplorasi Frontend Web Publik (Tanpa Login)")
    add_bullet("1. Akses Halaman Utama: ", "Buka tautan: http://localhost:8383/. Mahasiswa disambut tampilan landing page profesional bertema logistik pabrik manufaktur.")
    add_bullet("2. Uji Metrik Gudang: ", "Perhatikan 4 kartu statistik ringkasan barang, stok fisik, kategori, dan supplier yang diambil secara live dari basis data.")
    add_bullet("3. Uji Cek Stok & Filter: ", "Cari suku cadang pada kolom input pencarian dan uji filter dropdown kategori. Perhatikan badge status stok (Tersedia, Stok Kritis, atau Habis).")

    add_h2("6.3 Langkah 3: Masuk ke Portal Operasional Petugas (Login)")
    add_bullet("1. Buka Halaman Login: ", "Klik tombol 'Portal Petugas' di navbar atas atau akses langsung http://localhost:8383/auth/login.php.")
    add_bullet("2. Kredensial Praktikum: ", "Gunakan akun resmi: Username: 'admin' dan Password: 'admin123' (atau akun petugas: 'petugas1' / 'admin123').")
    add_bullet("3. Pengalihan ke Dashboard: ", "Setelah verifikasi password_hash sukses, sistem langsung mengarahkan pengguna ke halaman terproteksi 'dashboard.php'.")

    add_h2("6.4 Langkah 4: Pengoperasian 4 Modul CRUD Mandiri Mahasiswa")
    add_p("Mahasiswa mempraktikkan alur penginputan data berelasi secara berurutan dan logis:")
    add_bullet("Tahap A (Modul Kategori): ", "Buka '📁 Kategori' &rarr; Tambah kategori baru (contoh: 'Pneumatik & Kompresor'). Uji tombol Edit dan Hapus.")
    add_bullet("Tahap B (Modul Supplier): ", "Buka '🏢 Supplier' &rarr; Tambah master data mitra rekanan resmi beserta kontak dan alamat.")
    add_bullet("Tahap C (Modul Barang): ", "Buka '📦 Barang' &rarr; Tambah suku cadang baru dengan memilih kategori yang sudah dibuat pada Tahap A.")
    add_bullet("Tahap D (Modul Mutasi Stok): ", "Buka '🔄 Mutasi Stok' &rarr; Catat transaksi barang masuk (+) atau barang keluar (-). Perhatikan stok fisik pada tabel barang otomatis berubah secara presisi.")

    add_h2("6.5 Langkah 5: Pembuktian Validasi Keamanan & Logika Bisnis")
    add_bullet("Uji Anti-Stok Minus: ", "Pada form mutasi keluar, coba masukkan jumlah yang melebihi sisa stok fisik. Sistem wajib membatalkan transaksi untuk mencegah stok bernilai negatif.")
    add_bullet("Uji Anti-SQL Injection: ", "Masukkan karakter petik tunggal (') pada form pencarian. Query tetap aman karena parameter telah dibungkus oleh PDO Prepared Statements.")
    add_bullet("Uji Proteksi Sesi: ", "Coba buka 'dashboard.php' langsung di tab penyamaran (Incognito). Sistem otomatis menolak dan mengalihkan ke form login.")

    add_h2("6.6 Langkah 6: Navigasi Silang & Mengakhiri Sesi (Logout)")
    add_bullet("Tinjau Web Publik: ", "Petugas dapat melihat tampilan web pengunjung kapan saja dengan mengklik tombol '🌐 Web Publik' pada navbar atas.")
    add_bullet("Keluar Aman: ", "Klik tombol 'Keluar' di pojok kanan atas untuk menghancurkan data sesi ($_SESSION) dan kembali ke halaman login.")

    add_h2("6.7 Strategi Live-Coding Dosen & Pertanyaan Asistensi")
    add_bullet("Skenario 1 (15 Menit): ", "Tunjukkan skema tabel di phpMyAdmin db_inventaris_gudang dan jelaskan relasi kunci asing (Foreign Key) barang.kategori_id.")
    add_bullet("Skenario 2 (20 Menit): ", "Tunjukkan alur login (auth/login.php): bagaimana form mengirim $_POST['username'], diambil dengan prepared statement, lalu divalidasi dengan password_verify().")
    add_bullet("Skenario 3 (30 Menit): ", "Buka modul/kategori/tambah.php: jelaskan bagaimana $db->prepare() melindungi query dari manipulasi SQL penyerang.")
    add_bullet("Skenario 4 (25 Menit): ", "Demonstrasikan transaksi ACID di modul/transaksi/tambah.php: simulasikan error listrik/koneksi saat update stok untuk membuktikan transaksi rollback.")
    add_bullet("Pertanyaan Asistensi 1: ", "Mengapa pada formulir tambah barang kita menggunakan tag <select> berelasi ke tabel kategori, bukan mengetik nama kategori secara manual?")
    add_bullet("Pertanyaan Asistensi 2: ", "Apa perbedaan mendasar antara fungsi password_hash() dengan fungsi enkripsi usang seperti md5()?")
    add_bullet("Pertanyaan Asistensi 3: ", "Apa fungsi htmlspecialchars() saat mencetak variabel nama barang di tabel HTML?")

    # ==================== BAB 7 ====================
    add_h1("BAB 7: KETENTUAN TUGAS KELOMPOK & RUBRIK PENILAIAN AKHIR")
    add_p(
        "Penilaian proyek mini mahasiswa didasarkan pada matriks Capaian Pembelajaran Lulusan (CPL) berikut:"
    )

    tbl_rub = doc.add_table(rows=6, cols=3)
    tbl_rub.alignment = WD_TABLE_ALIGNMENT.CENTER
    rub_data = [
        [("Komponen Evaluasi", True), ("Bobot Nilai", True), ("Kriteria Capaian & Bukti Unjuk Kerja", True)],
        [
            ("1. Kelengkapan 4 Modul CRUD Mandiri", False),
            ("35%", False),
            ("Setiap anggota kelompok terbukti memiliki kontribusi kode pada modul yang dipegangnya (Create, Read, Update, Delete berjalan 100% tanpa error).", False)
        ],
        [
            ("2. Implementasi Keamanan OWASP Dasar", False),
            ("25%", False),
            ("Wajib menggunakan PDO Prepared Statements pada seluruh query INSERT/UPDATE/DELETE/SELECT berparameter serta sanitasi htmlspecialchars() pada View.", False)
        ],
        [
            ("3. Desain Antarmuka & Responsivitas UI", False),
            ("15%", False),
            ("Tampilan form dan tabel rapi, nyaman dilihat di perangkat mobile/desktop, serta dilengkapi dialog konfirmasi saat menghapus data.", False)
        ],
        [
            ("4. Kerapian Laporan Sesuai Template SI", False),
            ("15%", False),
            ("Laporan memuat Use Case Diagram, ERD, screenshot antarmuka per anggota pada BAB IV, serta cuplikan kode inti.", False)
        ],
        [
            ("5. Penguasaan Saat Asistensi / Tanya Jawab", False),
            ("10%", False),
            ("Mahasiswa mampu menjelaskan baris kode yang ditulisnya secara lancar di depan dosen pengampu.", False)
        ]
    ]

    for r_idx, row in enumerate(rub_data):
        for c_idx, (val, is_hdr) in enumerate(row):
            c = tbl_rub.rows[r_idx].cells[c_idx]
            p = c.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            p.paragraph_format.line_spacing = 1.05
            r = p.add_run(val)
            r.font.name = "Times New Roman"
            r.font.size = Pt(9.5)
            if is_hdr:
                r.bold = True
                set_cell_background(c, "E2E8F0")
                r.font.color.rgb = RGBColor(15, 23, 42)
            else:
                set_cell_background(c, "FFFFFF" if r_idx % 2 != 0 else "F8FAFC")
            set_cell_margins(c, top=50, bottom=50, left=60, right=60)
            set_cell_border(c, top={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               bottom={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               left={'sz': 4, 'val': 'single', 'color': 'CBD5E1'},
                               right={'sz': 4, 'val': 'single', 'color': 'CBD5E1'})

    # Save to both target locations
    path1 = "/Users/rosidin/Downloads/Stmik Ikmi Cirebon_28agust26_0704/Ganjil 2026_2027/Pemrograman Web Dasar/Projek/Panduan Projek - Sistem Informasi Inventaris Gudang.docx"
    path2 = "/Users/rosidin/Sites/inventaris-gudang-si/Panduan Projek - Sistem Informasi Inventaris Gudang.docx"

    try:
        os.makedirs(os.path.dirname(path1), exist_ok=True)
        doc.save(path1)
        print(f"1. {path1}")
    except Exception as e:
        print(f"Catatan (path1): {e}")

    doc.save(path2)
    print(f"2. {path2}")
    print("SUCCESS: Dokumen Word Panduan Projek berhasil diperbarui!")

if __name__ == "__main__":
    build_panduan()

