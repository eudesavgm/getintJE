<?php
// Traitement de l'envoi du fichier
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['file'];
    $email = htmlspecialchars($_POST['email']);
    $filename = basename($file['name']);
    $targetPath = $uploadDir . time() . "_" . $filename;

    $allowed = ['zip', 'rar', 'pdf', 'exe', 'dwg', 'jpg', 'png'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $message = "<p style='color:green;'>✅ Fichier envoyé avec succès : $filename</p>";
        } else {
            $message = "<p style='color:red;'>❌ Échec du téléchargement.</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Type de fichier .$ext non autorisé.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Upload & Liste des fichiers</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 30px;
      background: #f4f4f4;
    }
    h2 {
      color: #333;
    }
    .success, .error {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input, button {
      padding: 10px;
      margin: 10px 0;
      display: block;
      width: 100%;
      max-width: 400px;
    }
    ul {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      list-style-type: none;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    li {
      margin: 10px 0;
    }
    a.download {
      color: #1a73e8;
      text-decoration: none;
    }
    a.download:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <h2>Envoyer un fichier</h2>

  <?php if (!empty($message)) echo "<div>$message</div>"; ?>

  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <input type="email" name="email" placeholder="Votre adresse email" required>
    <button type="submit">Envoyer</button>
  </form>

  <h2>Fichiers disponibles</h2>
  <ul>
    <?php
      $dir = 'uploads/';
      if (is_dir($dir)) {
          $files = array_diff(scandir($dir), array('.', '..'));
          if (count($files) === 0) {
              echo "<li>Aucun fichier disponible.</li>";
          } else {
              foreach ($files as $file) {
                  echo "<li><a class='download' href='$dir$file' download>$file</a></li>";
              }
          }
      } else {
          echo "<li>Le dossier d'upload n'existe pas.</li>";
      }
    ?>
  </ul>
  <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['file'];
    $email = htmlspecialchars($_POST['email']);
    $filename = basename($file['name']);
    $targetPath = $uploadDir . time() . "_" . $filename;

    $allowed = ['zip', 'rar', 'pdf', 'exe', 'dwg', 'jpg', 'png'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "<p style='color:green;text-align:center'>✅ Fichier envoyé avec succès : $filename</p>";

            // Logs
            if (!is_dir("logs")) mkdir("logs");
            file_put_contents("logs/uploads.txt", date("Y-m-d H:i:s") . " - $email a envoyé : $filename\n", FILE_APPEND);

            // Email à l'admin
            $toAdmin = "jeaneudesavocegamou@gmail.com";
            $subjectAdmin = "📁 Nouveau fichier envoyé";
            $messageAdmin = "Utilisateur : $email\nFichier : $filename\nDate : " . date("Y-m-d H:i:s");
            $headers = "From: notifier@getintje.com";
            mail($toAdmin, $subjectAdmin, $messageAdmin, $headers);

            // Email à l'utilisateur
            $subjectUser = "✅ getintJE : Votre fichier a été reçu";
            $messageUser = "Bonjour,\n\nNous avons bien reçu votre fichier : $filename.\nMerci pour votre contribution.\n\nL’équipe getintJE";
            mail($email, $subjectUser, $messageUser, $headers);
        } else {
            echo "<p style='color:red;text-align:center'>❌ Échec du téléchargement.</p>";
        }
    } else {
        echo "<p style='color:red;text-align:center'>❌ Type de fichier .$ext non autorisé.</p>";
    }

    echo "<p style='text-align:center'><a href='index.html'>⬅ Retour</a></p>";
} else {
    header("Location: index.html");
    exit;
}
?>
</body>
</html>
