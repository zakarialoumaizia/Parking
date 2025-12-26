<?php
class HomeController extends Controller {
    public function index() {
        // Fetch Data for Landing Page Sections
        $db = Database::getInstance()->getConnection();
        
        // Tarifs
        $tarifs = $db->query("SELECT * FROM tarifs ORDER BY prix_heure ASC")->fetchAll();
        
        // Reglement / Amendes
        $amendes = $db->query("SELECT * FROM types_taxe WHERE slug != 'tva_global'")->fetchAll();
        
        $this->view('home/index', ['tarifs' => $tarifs, 'amendes' => $amendes]);
    }
}
