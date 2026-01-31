<!-- HOME PAGE WITH PORTFOLIO -->
<style>
/* Portfolio Styles */
.portfolio-container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.tab-btn {
    padding: 12px 30px;
    background: white;
    border: 2px solid #667eea;
    color: #667eea;
    font-weight: bold;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s ease;
}

.tab-btn:hover {
    background: #f0f4ff;
}

.tab-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: 2px solid transparent;
}

.page {
    display: none;
    animation: fadeIn 0.5s ease;
}

.page.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* HOME SECTION */
.salam {
    font-size: 2.5em;
    color: #667eea;
    margin-bottom: 20px;
}

.home-intro {
    font-size: 1.1em;
    line-height: 1.8;
    color: #555;
    margin-bottom: 20px;
}

.home-box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.home-box h3 {
    color: #667eea;
    margin-bottom: 15px;
    font-size: 1.5em;
}

.home-box ul {
    list-style: none;
    padding-left: 0;
}

.home-box ul li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.home-box ul li:last-child {
    border-bottom: none;
}

.home-box ul li:before {
    content: "✓ ";
    color: #667eea;
    font-weight: bold;
    margin-right: 10px;
}

/* ABOUT SECTION */
.tentang {
    font-size: 2.5em;
    color: #667eea;
    margin-bottom: 20px;
}

.about-text {
    font-size: 1.1em;
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
}

.about-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    display: flex;
    gap: 30px;
    align-items: flex-start;
    flex-wrap: wrap;
}

.about-img {
    width: 250px;
    height: 250px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.bio {
    flex: 1;
    min-width: 300px;
}

.bio h3 {
    color: #667eea;
    margin-bottom: 15px;
    font-size: 1.4em;
}

.bio ul {
    list-style: none;
    padding: 0;
}

.bio ul li {
    padding: 12px 0;
    font-size: 1.05em;
    border-bottom: 1px solid #f0f0f0;
}

.bio ul li:last-child {
    border-bottom: none;
}

/* CONTACT SECTION */
.contact-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 0 auto;
}

.contact-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #667eea;
}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1em;
    font-family: inherit;
    transition: border-color 0.3s ease;
}

.contact-form input:focus,
.contact-form textarea:focus {
    outline: none;
    border-color: #667eea;
}

.btn-kirim {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.btn-kirim:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-kirim:active {
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
    .about-box {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .about-img {
        width: 200px;
        height: 200px;
    }
    
    .page-tabs {
        flex-wrap: wrap;
    }
    
    .tab-btn {
        flex: 1;
        min-width: 100px;
    }
}
</style>

<div class="portfolio-container">
    <!-- TAB NAVIGATION -->
    <div class="page-tabs">
        <button class="tab-btn active" onclick="showPage('home')">🏠 Home</button>
        <button class="tab-btn" onclick="showPage('about')">👤 About</button>
        <button class="tab-btn" onclick="showPage('contact')">📧 Contact</button>
    </div>

    <!-- HOME PAGE -->
    <section id="home" class="page active">
        <h1 class="salam">Selamat Datang</h1>
        <p class="home-intro">
            Halo! Saya <b>Fahrezi Luthfi</b>, seorang pelajar/mahasiswa yang sedang mendalami dunia 
            pemrograman khususnya pada bidang <b>web development</b>. Website ini saya buat sebagai media 
            untuk menampilkan profil, kemampuan, serta sebagai tempat untuk belajar dan berproses menjadi
            lebih baik dari hari ke hari.
        </p>

        <p class="home-intro">
            Di dalam website ini, kamu dapat melihat beberapa informasi mengenai diri saya melalui 
            halaman <b>About</b>, serta kamu juga dapat mengirim pesan atau bertanya mengenai project yang 
            sedang atau akan saya kerjakan melalui halaman <b>Contact</b>. Saya percaya bahwa belajar 
            tidak pernah ada batasnya, dan setiap langkah kecil akan membawa saya menuju tujuan besar.
        </p>

        <div class="home-box">
            <h3 class="ques">Apa yang saya pelajari?</h3>
            <ul>
                <li>HTML – struktur dasar website</li>
                <li>CSS – mengatur tampilan agar menarik</li>
                <li>JavaScript – membuat website lebih hidup</li>
                <li>PHP & MySQL – backend dan database</li>
                <li>UI Design dasar – tata letak & visual</li>
            </ul>
        </div>

        <div class="home-box">
            <h3 class="goals">Goals Saya</h3>
            <p>
                Saya ingin terus meningkatkan skill dalam pengembangan website, membuat tampilan yang 
                modern, responsif, dan nyaman untuk digunakan. Ke depannya, saya berencana mempelajari 
                framework seperti <b>React</b> atau <b>Tailwind</b> agar website yang saya bangun lebih cepat dan efisien.
            </p>
        </div>
    </section>

    <!-- ABOUT PAGE -->
    <section id="about" class="page">
        <h1 class="tentang">Tentang Saya</h1>
        <p class="about-text">
            Perkenalkan, saya <b>Luthfi Ahmad Fahrezi</b>. Saya seorang pelajar/mahasiswa yang sedang mendalami 
            dunia <b>pemrograman web</b> dan teknologi. Saya sangat tertarik dalam membuat tampilan web 
            yang modern, responsive, dan mudah digunakan.
        </p>

        <div class="about-box">
            <img src="assets/img/profile.jpg" alt="Foto Profil" class="about-img" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22250%22%3E%3Crect fill=%22%23667eea%22 width=%22250%22 height=%22250%22/%3E%3Ctext fill=%22white%22 font-size=%2260%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3EFL%3C/text%3E%3C/svg%3E'">
            
            <div class="bio">
                <h3>Informasi Singkat:</h3>
                <ul>
                    <li>📍 Domisili : Banjarmasin, Kalimantan Selatan, Indonesia</li>
                    <li>💻 Instagram : @lutpee.e</li>
                    <li>🎯 Email : fahrezillutfi1@gmail.com</li>
                    <li>📚 Hobi : Coding, desain, belajar teknologi</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- CONTACT PAGE -->
    <section id="contact" class="page">
        <h1>Hubungi Saya 📩</h1>
        <p style="text-align: center; font-size: 1.1em; color: #666; margin-bottom: 30px;">
            Silahkan kirim pesan Anda melalui form di bawah ini.
        </p>

        <form action="#" class="contact-form" onsubmit="handleSubmit(event)">
            <label for="nama">Nama</label>
            <input type="text" id="nama" placeholder="Masukkan nama anda.." required>

            <label for="email">Email</label>
            <input type="email" id="email" placeholder="Alamat email aktif.." required>

            <label for="pesan">Pesan</label>
            <textarea id="pesan" rows="5" placeholder="Tulis pesan disini..." required></textarea>

            <button type="submit" class="btn-kirim">Kirim Pesan</button>
        </form>
    </section>
</div>

<script>
// Function to switch between tabs
function showPage(pageId) {
    // Hide all pages
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => page.classList.remove('active'));
    
    // Remove active from all tabs
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show selected page
    document.getElementById(pageId).classList.add('active');
    
    // Add active to clicked tab
    event.target.classList.add('active');
}

// Handle form submission
function handleSubmit(event) {
    event.preventDefault();
    
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    const pesan = document.getElementById('pesan').value;
    
    // Show success message (you can replace this with actual form submission logic)
    alert(`Terima kasih ${nama}!\n\nPesan Anda telah diterima. Saya akan menghubungi Anda melalui ${email} segera.`);
    
    // Reset form
    event.target.reset();
}
</script>