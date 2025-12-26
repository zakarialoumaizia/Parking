<?php
class ReglementController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();
        // Fetch all taxes/fines that are NOT TVA
        $amendes = $db->query("SELECT * FROM types_taxe WHERE slug != 'tva_global'")->fetchAll();
        
        $this->view('home/reglement', ['amendes' => $amendes]);
    }
}
