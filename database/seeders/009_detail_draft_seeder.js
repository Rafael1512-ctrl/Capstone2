const db = require('../../config/db');

async function run() {
  const details = [
    [1, 1, 'PC Client Lenovo ThinkCentre', 10, 7500000.00, 'https://tokopedia.com', '/assets/images/lenovo.png', 'approved'],
    [2, 1, 'Switch Hub Cisco 24 Port', 2, 3500000.00, 'https://tokopedia.com', '/assets/images/cisco.png', 'approved'],
    [3, 2, 'Mouse Logitech Wireless B170', 20, 120000.00, 'https://tokopedia.com', null, 'pending'],
    [4, 1, 'Monitor ASUS VZ249HE 24 inch', 10, 1650000.00, 'https://tokopedia.com', null, 'approved'],
    [5, 1, 'Keyboard Logitech K120 USB', 15, 110000.00, 'https://tokopedia.com', null, 'approved']
  ];

  for (const dt of details) {
    await db.query(
      `INSERT INTO detail_draft (id, draft_id, nama_barang, jumlah, harga_satuan, link_pembelian, foto_url, status_item) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE nama_barang=VALUES(nama_barang), jumlah=VALUES(jumlah), harga_satuan=VALUES(harga_satuan), link_pembelian=VALUES(link_pembelian), foto_url=VALUES(foto_url), status_item=VALUES(status_item)`,
      dt
    );
  }
  console.log('🌱  Seeded "detail_draft" table.');
}

module.exports = { run };
