<?php
class TarifsController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM tarifs ORDER BY prix_heure ASC");
        $tarifs = $stmt->fetchAll();
        
        $this->view('tarifs/index', ['tarifs' => $tarifs]);
    }
}
