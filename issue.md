# Issue: Redesign Halaman Video Menjadi YouTube-Style Gallery

## Deskripsi Singkat

Ubah tampilan frontend halaman daftar video (`/videos`) dari layout sederhana menjadi tampilan bergaya **YouTube**. Halaman harus menampilkan video dalam bentuk **thumbnail grid** (bukan langsung embed `<video>` player), dan ketika thumbnail diklik akan membuka halaman/modal **video player** yang memiliki fitur pemutaran seperti YouTube (lazy loading, streaming, kontrol lengkap).

---

## Latar Belakang & Konteks Proyek

Proyek ini dibangun menggunakan **CodeIgniter 3** dengan arsitektur sebagai berikut:

- **Backend API** sudah siap dan **TIDAK PERLU diubah**.
- **Framework CSS**: Tailwind CSS (via CDN).
- **Autentikasi**: Token Bearer disimpan di `localStorage` dengan key `auth_token`.
- **Server-side Pagination** sudah diimplementasikan di backend. API mengembalikan objek `pagination` berisi `total_items`, `total_pages`, `current_page`, dan `limit`.

### File yang Akan Dimodifikasi

| File | Lokasi | Keterangan |
|------|--------|------------|
| `videos.php` | `application/views/videos.php` | **File utama** yang harus di-redesign |

### File Referensi (JANGAN diubah, hanya untuk dipelajari)

| File | Lokasi | Keterangan |
|------|--------|------------|
| `FileController.php` | `application/controllers/api/FileController.php` | API controller, sudah mendukung pagination |
| `File_model.php` | `application/models/File_model.php` | Model database |
| `routes.php` | `application/config/routes.php` | Routing konfigurasi |
| `footer.php` | `application/views/layout/footer.php` | Global modal upload & JS utilities |
| `photo_category.php` | `application/views/photo_category.php` | **Contoh referensi** lightbox & pagination yang sudah jadi |

---

## API Endpoint yang Digunakan

### GET `/api/files?type=video&page=1&limit=10`

**Header:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "category_id": null,
      "original_name": "tutorial.mp4",
      "file_name": "Tutorial CodeIgniter",
      "file_path": "assets/uploads/abc123.mp4",
      "file_size": 52428800,
      "file_type": "video/mp4",
      "is_deleted": 0,
      "created_at": "2026-04-09 10:00:00",
      "uploader_name": "Admin",
      "category_name": null
    }
  ],
  "pagination": {
    "total_items": 25,
    "total_pages": 3,
    "current_page": 1,
    "limit": 10
  }
}
```

### PUT `/api/files/{id}` — Update judul video
### DELETE `/api/files/{id}` — Soft delete video

---

## Spesifikasi Fitur yang Harus Diimplementasikan

### 1. Thumbnail Grid (Halaman Utama Video)

Tampilkan daftar video dalam bentuk **thumbnail card** bergaya YouTube dengan layout grid responsif.

**Aturan:**
- Grid layout: `2 kolom` di mobile, `3 kolom` di tablet, `4 kolom` di desktop besar.
- Maksimal **10 video per halaman** (`limit=10`).
- Setiap card menampilkan:
  - **Thumbnail**: Gunakan elemen `<video>` dengan atribut `preload="metadata"` agar browser hanya memuat frame pertama sebagai thumbnail (JANGAN gunakan `autoplay` atau `controls`). Tampilkan poster/frame pertama saja.
  - **Ikon play** di tengah thumbnail (overlay transparan).
  - **Durasi video** di pojok kanan bawah thumbnail (jika tersedia, boleh skip jika sulit).
  - **Judul video** (`file_name`) di bawah thumbnail, maksimal 2 baris (gunakan `line-clamp-2`).
  - **Info meta**: Nama uploader (`uploader_name`), ukuran file, dan tanggal upload.
  - **Tombol aksi**: Menu titik tiga (⋮) yang saat diklik menampilkan dropdown berisi "Edit Title" dan "Delete".

**Contoh Struktur HTML per Card:**
```html
<div class="video-card group cursor-pointer" onclick="openVideoPlayer(index)">
    <!-- Thumbnail Container -->
    <div class="relative aspect-video bg-slate-900 rounded-xl overflow-hidden">
        <video src="/path/to/video.mp4" preload="metadata" 
               class="w-full h-full object-cover">
        </video>
        <!-- Play Button Overlay -->
        <div class="absolute inset-0 flex items-center justify-center 
                    bg-black/20 group-hover:bg-black/40 transition-all">
            <div class="w-14 h-14 bg-red-600 rounded-full flex items-center 
                        justify-center shadow-lg transform group-hover:scale-110 
                        transition-transform">
                <i class="fas fa-play text-white text-xl ml-1"></i>
            </div>
        </div>
    </div>
    <!-- Info -->
    <div class="pt-3 flex gap-3">
        <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-slate-900 line-clamp-2">Judul Video</h3>
            <p class="text-sm text-slate-500 mt-1">Uploader • 52 MB</p>
            <p class="text-sm text-slate-500">9 Apr 2026</p>
        </div>
        <!-- Menu Button -->
        <button onclick="event.stopPropagation(); toggleMenu(id)" 
                class="text-slate-400 hover:text-slate-600 self-start">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </div>
</div>
```

---

### 2. Video Player (Modal Fullscreen)

Ketika thumbnail diklik, tampilkan **modal video player** yang muncul di atas halaman, mirip seperti menonton video di YouTube.

**Aturan teknis agar tidak berat di server:**
- Gunakan elemen HTML5 `<video>` native (JANGAN embed ulang seluruh file).
- Tambahkan atribut `preload="auto"` hanya pada video yang sedang dibuka (bukan seluruh video di halaman).  
- Tambahkan atribut `controls` agar browser menampilkan kontrol bawaan (play, pause, progress bar, volume, fullscreen).
- **Penting**: Video yang sedang TIDAK ditonton harus dalam keadaan `preload="metadata"` atau `preload="none"` — ini mencegah browser mengunduh semua video sekaligus.

**Komponen Modal Video Player:**
```
┌──────────────────────────────────────────────┐
│  [✕ Close]                                   │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │                                        │  │
│  │          VIDEO PLAYER                  │  │
│  │     (HTML5 <video> dengan controls)    │  │
│  │                                        │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  Judul Video                                 │
│  Uploader • 52 MB • 9 Apr 2026               │
│                                              │
│  [Edit Title]  [Delete]                      │
│                                              │
│  ─────────────────────────────────────────   │
│  Up Next (video lain di halaman yang sama):  │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐       │
│  │thumb1│ │thumb2│ │thumb3│ │thumb4│       │
│  └──────┘ └──────┘ └──────┘ └──────┘       │
└──────────────────────────────────────────────┘
```

**Detail Fitur Player:**
1. Backdrop gelap semi-transparan (`bg-slate-950/95 backdrop-blur`) — klik di luar modal untuk menutup.
2. Tombol `✕` (close) di pojok kanan atas.
3. Video player dengan `controls`, `preload="auto"`, dan `autoplay` agar langsung bermain saat dibuka.
4. Di bawah player: judul, info meta, dan tombol aksi (Edit Title, Delete).
5. Bagian "Up Next": Tampilkan thumbnail horizontal kecil dari video lain yang ada di halaman aktif (maksimal 4-6 video). Klik thumbnail ini akan mengganti video yang sedang diputar tanpa menutup modal.
6. **Keyboard support**: Tekan `Escape` untuk menutup modal.
7. **Penting**: Saat modal ditutup, video harus di-pause (`video.pause()`) agar tidak terus buffering di background.

---

### 3. Server-side Pagination

Pagination sudah ada di versi saat ini dan **SUDAH BENAR**. Pastikan tetap menggunakan logika yang sama:

```javascript
// Contoh cara memanggil API dengan pagination
const params = new URLSearchParams({ 
    type: 'video',
    page: page,   // nomor halaman
    limit: 10     // maksimal 10 per halaman
});

const response = await fetch(`/api/files?${params.toString()}`, {
    headers: { 'Authorization': `Bearer ${token}` }
});

const result = await response.json();
const videos = result.data;             // array video
const pagination = result.pagination;   // metadata halaman
```

- Tombol Prev/Next dan nomor halaman sudah ada, jangan ubah logikanya.
- Saat pindah halaman, tampilkan loading state (spinner + opacity).

---

## Tahapan Implementasi (Step-by-Step)

### Tahap 1: Pelajari Kode yang Sudah Ada

1. Buka dan baca file `application/views/videos.php` — ini adalah file yang akan kamu ubah.
2. Buka dan baca file `application/views/photo_category.php` — ini adalah **contoh referensi** yang sudah mengimplementasikan lightbox + server-side pagination dengan benar. Gunakan pola yang sama.
3. Pahami response API di bagian "API Endpoint" di atas.

### Tahap 2: Ubah Layout Grid Thumbnail

1. Buka file `application/views/videos.php`.
2. Cari fungsi `renderGallery(videos, pagination)` di dalam `<script>`.
3. **Ubah** isi `grid.innerHTML` di dalam loop `videos.forEach(...)`:
   - **Hapus** elemen `<video controls>` yang ada (karena saat ini video langsung diputar di card).
   - **Ganti** dengan thumbnail card bergaya YouTube sesuai spesifikasi di bagian "Thumbnail Grid" di atas.
   - Gunakan `<video preload="metadata">` tanpa `controls` dan tanpa `autoplay`.
   - Tambahkan overlay ikon play di tengah thumbnail.
   - Tambahkan `onclick="openVideoPlayer(index)"` pada card (dengan `index` dari parameter loop `forEach`).
4. **Ubah** class grid container dari `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3` menjadi `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4` untuk mendukung 4 kolom di layar besar.

### Tahap 3: Buat Modal Video Player

1. Tambahkan HTML modal **sebelum** tag `<script>` (setelah `</main>`), contoh struktur:
   ```html
   <!-- Video Player Modal -->
   <div id="videoPlayerModal" class="fixed inset-0 z-[60] hidden">
       <!-- Backdrop -->
       <div class="absolute inset-0 bg-slate-950/95 backdrop-blur-md" 
            onclick="closeVideoPlayer()"></div>
       <!-- Close Button -->
       <button onclick="closeVideoPlayer()" class="absolute top-4 right-4 z-[70] ...">
           <i class="fas fa-times text-2xl"></i>
       </button>
       <!-- Content -->
       <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
           <div class="w-full max-w-4xl">
               <!-- Player -->
               <video id="mainVideoPlayer" controls autoplay preload="auto"
                      class="w-full rounded-xl shadow-2xl aspect-video bg-black">
               </video>
               <!-- Info Section -->
               <div id="videoInfoSection" class="mt-4">
                   <h2 id="playerTitle" class="text-xl font-bold text-white"></h2>
                   <p id="playerMeta" class="text-sm text-white/60 mt-1"></p>
                   <div class="flex gap-3 mt-4"> ... tombol Edit & Delete ... </div>
               </div>
               <!-- Up Next Section -->
               <div class="mt-6 border-t border-white/10 pt-4">
                   <h4 class="text-white/80 font-semibold mb-3">Up Next</h4>
                   <div id="upNextGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                   </div>
               </div>
           </div>
       </div>
   </div>
   ```

### Tahap 4: Implementasi Logika JavaScript Video Player

Tambahkan fungsi-fungsi berikut di dalam `<script>`:

```javascript
let currentVideoIndex = -1;

function openVideoPlayer(index) {
    currentVideoIndex = index;
    const video = allVideos[index];
    const player = document.getElementById('mainVideoPlayer');
    const modal = document.getElementById('videoPlayerModal');
    
    // Set video source
    player.src = '/' + video.file_path;
    
    // Set info
    document.getElementById('playerTitle').innerText = video.file_name;
    const fileSize = (video.file_size / (1024 * 1024)).toFixed(2) + ' MB';
    const uploadDate = new Date(video.created_at).toLocaleDateString('id-ID');
    document.getElementById('playerMeta').innerText = 
        `${video.uploader_name} • ${fileSize} • ${uploadDate}`;
    
    // Render Up Next
    renderUpNext(index);
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeVideoPlayer() {
    const player = document.getElementById('mainVideoPlayer');
    player.pause();              // WAJIB: hentikan pemutaran
    player.removeAttribute('src'); // Bebaskan memory
    player.load();               // Reset player
    
    document.getElementById('videoPlayerModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function renderUpNext(currentIndex) {
    const grid = document.getElementById('upNextGrid');
    grid.innerHTML = '';
    
    allVideos.forEach((v, i) => {
        if (i === currentIndex) return; // Skip video yang sedang diputar
        grid.innerHTML += `
            <div onclick="openVideoPlayer(${i})" 
                 class="cursor-pointer group rounded-lg overflow-hidden">
                <div class="relative aspect-video bg-slate-800">
                    <video src="/${v.file_path}" preload="metadata" 
                           class="w-full h-full object-cover"></video>
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/50 
                                flex items-center justify-center transition">
                        <i class="fas fa-play text-white text-sm"></i>
                    </div>
                </div>
                <p class="text-white/80 text-xs font-medium mt-1 line-clamp-1">${v.file_name}</p>
            </div>
        `;
    });
}
```

### Tahap 5: Tambahkan Keyboard Support

```javascript
// Tambahkan di dalam fungsi initPage()
document.addEventListener('keydown', (e) => {
    const modal = document.getElementById('videoPlayerModal');
    if (modal.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeVideoPlayer();
});
```

### Tahap 6: Pastikan Fitur Edit & Delete Tetap Berfungsi

- Tombol **Edit Title** dan **Delete** yang sudah ada harus tetap berfungsi.
- Jika delete dilakukan dari dalam modal player, setelah berhasil:
  1. Tutup modal player (`closeVideoPlayer()`)
  2. Refresh daftar video (`fetchVideos(currentPage)`)

### Tahap 7: Testing & Verifikasi

Lakukan pengujian berikut setelah implementasi selesai:

| No | Test Case | Expected Result |
|----|-----------|-----------------|
| 1 | Buka halaman `/videos` | Grid thumbnail muncul, video TIDAK autoplay |
| 2 | Klik salah satu thumbnail | Modal player terbuka, video mulai diputar |
| 3 | Klik tombol ✕ atau tekan Esc | Modal tertutup, video berhenti |
| 4 | Klik "Up Next" thumbnail | Video berganti tanpa menutup modal |
| 5 | Klik Edit Title di modal | Modal edit muncul, bisa simpan perubahan |
| 6 | Klik Delete di modal | Konfirmasi muncul, video dihapus, modal tutup |
| 7 | Paginasi: klik halaman 2 | Data berubah, hanya 10 video yang dimuat |
| 8 | Buka Network tab di DevTools | Hanya video yang diklik yang loading penuh |

---

## Catatan Penting

> ⚠️ **JANGAN** mengubah file backend (Controller, Model, Routes). Semua perubahan hanya di `application/views/videos.php`.

> ⚠️ **JANGAN** menggunakan `autoplay` pada thumbnail. Hanya video di dalam modal player yang boleh autoplay.

> ⚠️ **WAJIB** memanggil `video.pause()` dan `video.removeAttribute('src')` saat menutup modal agar browser menghentikan buffering dan membebaskan memori.

> ⚠️ **Gunakan** `preload="metadata"` pada thumbnail untuk menampilkan frame pertama tanpa mengunduh seluruh file video.

> ⚠️ **Pertahankan** semua fitur yang sudah ada: Server-side Pagination (limit=10), Edit Title, Soft Delete, Loading State.
