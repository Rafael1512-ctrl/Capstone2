const DraftPengadaan = require("../models/DraftPengadaan");
const DetailDraft = require("../models/DetailDraft");
const User = require("../models/User");
const Inventaris = require("../models/Inventaris");

class KepalaLabController {
  // GET /api/kepala-lab/pengadaan
  static async showPengadaan(req, res) {
    try {
      const activeDraft = await DraftPengadaan.getActiveDraft(req.user.id);
      let items = [];
      if (activeDraft) {
        items = await DetailDraft.getByDraftId(activeDraft.id);
      }
      const kaprodiList = await User.getKetuaProdiList();
      const inventarisList = await Inventaris.getAllReceived();

      res.json({
        title: "Draf Pengadaan Baru",
        activePath: "/kepala-lab/pengadaan",
        activeDraft,
        items,
        kaprodiList,
        inventarisList,
        hasActiveDraft: !!activeDraft,
        hasItems: items.length > 0,
        itemCount: items.length,
        hasKaprodiList: kaprodiList.length > 0,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/create-draft
  static async createDraft(req, res) {
    try {
      const { tahun, ketua_prodi_id } = req.body;

      // Auto-select ketua prodi if not provided or "auto" value
      let selectedKetuaProdiId = ketua_prodi_id;
      if (!selectedKetuaProdiId || selectedKetuaProdiId === "auto") {
        const defaultKaprodi = await User.getKetuaProdiDefault();
        selectedKetuaProdiId = defaultKaprodi ? defaultKaprodi.id : null;
      }

      await DraftPengadaan.create(req.user.id, selectedKetuaProdiId, tahun);
      res.json({ success: true, message: "Draft created successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/update-draft/:id
  static async updateDraft(req, res) {
    try {
      const { tahun, ketua_prodi_id } = req.body;
      await DraftPengadaan.update(
        req.params.id,
        tahun,
        ketua_prodi_id,
        req.user.id,
      );
      res.json({ success: true, message: "Draft updated successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/delete-draft/:id
  static async deleteDraft(req, res) {
    try {
      await DraftPengadaan.delete(req.params.id, req.user.id);
      res.json({ success: true, message: "Draft deleted successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/add-item
  static async addItem(req, res) {
    try {
      const {
        draft_id,
        nama_barang,
        tipe_barang,
        harga_satuan,
        jumlah,
        rasionalisasi,
        link_pembelian,
        inventaris_digantikan_id
      } = req.body;
      await DetailDraft.create(
        draft_id,
        nama_barang,
        tipe_barang,
        harga_satuan,
        jumlah,
        rasionalisasi,
        link_pembelian,
        inventaris_digantikan_id ? parseInt(inventaris_digantikan_id) : null
      );
      res.json({ success: true, message: "Item added successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/delete-item/:id
  static async deleteItem(req, res) {
    try {
      await DetailDraft.delete(req.params.id);
      res.json({ success: true, message: "Item deleted successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // POST /api/kepala-lab/pengadaan/submit/:id
  static async submitDraft(req, res) {
    try {
      await DraftPengadaan.submit(req.params.id);
      res.json({ success: true, message: "Draft submitted successfully" });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }

  // GET /api/kepala-lab/history
  static async showHistory(req, res) {
    try {
      const drafts = await DraftPengadaan.getHistoryForKepalaLab(req.user.id);
      const activeDraft = await DraftPengadaan.getActiveDraft(req.user.id);
      let items = [];
      if (activeDraft) {
        items = await DetailDraft.getByDraftId(activeDraft.id);
      }

      res.json({
        title: "Riwayat Pengadaan",
        activePath: "/kepala-lab/history",
        drafts,
        activeDraft,
        items,
        hasActiveDraft: !!activeDraft,
        hasItems: items.length > 0,
        itemCount: items.length,
        hasDrafts: drafts.length > 0,
        draftCount: drafts.length,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ error: "Database error: " + err.message });
    }
  }
}

module.exports = KepalaLabController;
