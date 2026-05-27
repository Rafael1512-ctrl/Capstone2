const BHP = require('../models/BHP');
const MaintenanceLog = require('../models/MaintenanceLog');
const Inventaris = require('../models/Inventaris');
const Ruangan = require('../models/Ruangan');

class StafLabController {
  // GET /api/staf-lab/bhp
  static async showBHP(req, res) {
    try {
      const bhpList = await BHP.getAll();
      const ruangan = await Ruangan.getAll();

      res.json({
        title: 'Kelola Stok BHP',
        activePath: '/staf-lab/bhp',
        bhpList,
        ruangan,
        hasBHP: bhpList.length > 0,
        bhpCount: bhpList.length,
        hasRuangan: ruangan.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/staf-lab/bhp/create
  static async createBHP(req, res) {
    try {
      const { nama_bhp, ruangan_id, stok, satuan, kondisi } = req.body;
      await BHP.create(nama_bhp, ruangan_id, stok, satuan, kondisi);
      res.json({ success: true, message: 'BHP created successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/staf-lab/bhp/update-stock/:id
  static async updateBHPStock(req, res) {
    try {
      const { stok, kondisi } = req.body;
      await BHP.updateStock(req.params.id, stok, kondisi);
      res.json({ success: true, message: 'BHP stock updated successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // GET /api/staf-lab/maintenance
  static async showMaintenance(req, res) {
    try {
      const logs = await MaintenanceLog.getAll();
      const inventaris = await Inventaris.getForMaintenance();
      const bhpList = await BHP.getInStock();

      res.json({
        title: 'Log Maintenance & Update Kondisi',
        activePath: '/staf-lab/maintenance',
        logs,
        inventaris,
        bhpList,
        hasLogs: logs.length > 0,
        logCount: logs.length,
        hasInventaris: inventaris.length > 0,
        hasBHP: bhpList.length > 0
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }

  // POST /api/staf-lab/maintenance/create
  static async createMaintenance(req, res) {
    try {
      const { inventaris_id, bhp_id_used, qty_bhp_used, deskripsi, status_akhir } = req.body;
      await MaintenanceLog.create(
        inventaris_id,
        req.user.id,
        bhp_id_used ? parseInt(bhp_id_used) : null,
        qty_bhp_used ? parseInt(qty_bhp_used) : 0,
        deskripsi,
        status_akhir
      );
      res.json({ success: true, message: 'Maintenance log created successfully' });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: 'Database error: ' + err.message });
    }
  }
}

module.exports = StafLabController;
