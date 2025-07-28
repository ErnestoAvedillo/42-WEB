<?php

// Determinar qué página mostrar
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Páginas permitidas
$allowed_pages = ['home', 'gallery', 'upload', 'login', 'register', 'profile'];

// Verificar si la página es válida
if (!in_array($page, $allowed_pages)) {
  $page = 'home';
}

// Definir títulos para cada página
$page_titles = [
  'home' => 'Home - Camagru',
  'gallery' => 'Gallery - Camagru',
  'upload' => 'Upload - Camagru',
  'login' => 'Login - Camagru',
  'register' => 'Register - Camagru',
  'profile' => 'Profile - Camagru'
];

$pageTitle = $page_titles[$page];
?>
<main id="mainContent">
  <?php
  // Cargar el contenido de la página correspondiente
  switch ($page) {
    case 'home':
      include 'pages/home.php';
      break;
    case 'gallery':
      include 'pages/gallery.php';
      break;
    case 'upload':
      include 'pages/upload.php';
      break;
    case 'login':
      include 'pages/login/login.php';
      break;
    case 'register':
      include 'pages/register/register.php';
      break;
    case 'profile':
      include 'pages/profile.php';
      break;
    case 'error_register_handler':
      include 'pages/register/error_register_handler.php';
      break;
    default:
      include 'pages/home.php';
  }
  ?>
</main>