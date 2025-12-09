<?php

class HomeController {
    
    public function index() {
        // Render homepage view
        include __DIR__ . '/../views/home.php';
    }
}
