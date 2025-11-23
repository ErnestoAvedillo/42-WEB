<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Camagru'; ?></title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/privacidad/privacidad.css">
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
</head>

<body>
  <?php
  $pageTitle = "Política de Privacidad - Camagru";
  include __DIR__ . '/../../pages/header/header.php';

  $pageTitle = "left_bar - Camagru";
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>
  <div class="privacidad-container">
    <main>
      <section>
        <h2>Introducción</h2>
        <h3>Aviso de Privacidad</h3>
        <p>En la empresa Avedillo nos tomamos muy en serio la protección de tus datos personales.</br>
          Esta Política de Privacidad explica cómo recopilamos, usamos, almacenamos y protegemos tu información cuando utilizas nuestros servicios.
        <ol>

          <li>Información que recopilamos.</br>
            Podemos solicitar y almacenar información personal como nombre, correo electrónico, número de teléfono, dirección IP, así como datos de navegación (cookies, historial de uso, dispositivo y navegador).</li>
          </br>
          <li>Finalidad del tratamiento de datos.</br>
            La información se utiliza para:</li>

          <ul>
            <li>Proporcionar, mantener y mejorar nuestros servicios.</li>
            <li>Enviar notificaciones relevantes, actualizaciones y comunicaciones relacionadas.</li>
            <li>Cumplir con obligaciones legales aplicables.</li>
            <li>Mejorar la experiencia del usuario a través de estadísticas y análisis.</li>
          </ul>
          </br>
          <li>Uso de cookies.</br>
            Este sitio utiliza cookies y tecnologías similares para mejorar tu experiencia, analizar patrones de uso y personalizar el contenido. Puedes configurar tu navegador para rechazar cookies, aunque algunas funciones pueden verse afectadas.</li>
          </br>
          <li>Transferencia de datos.</br>
            No compartimos tus datos personales con terceros, salvo que:

            <ul>
              <li>Exista una obligación legal.</li>

              <li>Sea necesario para la prestación del servicio (ej. proveedores de hosting, pasarelas de pago).</li>

              <li>Contemos con tu consentimiento expreso.</li>

            </ul>
            </br>
          <li>Seguridad.</br>
            Implementamos medidas técnicas y organizativas para proteger tus datos contra accesos no autorizados, pérdida o alteración. Sin embargo, ningún sistema es 100% seguro, por lo que no podemos garantizar seguridad absoluta.</li>
          </br>
          <li>Derechos del usuario.</br>
            En cualquier momento puedes acceder, rectificar, cancelar u oponerte al uso de tus datos personales, así como revocar el consentimiento otorgado, enviando una solicitud a: [correo de contacto].</li>
          </br>
          <li>Cambios en la política.</br>
            Podemos actualizar este Aviso de Privacidad en cualquier momento. Las modificaciones entrarán en vigor al publicarse en este sitio web.</br>
            Contacto:
            Si tienes dudas o deseas ejercer tus derechos, escríbenos a eavedillo@yahoo.es o a C/Pepito de los palotes S/N. Santa Gracia de Bisfundia</li>
        </ol>
      </section>
    </main>
  </div>
</body>

</html>
<?php
include __DIR__ . '/../../pages/footer/footer.php';
?>
</body>