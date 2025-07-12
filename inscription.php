<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = htmlspecialchars($_POST["email"]);
    $date = date("Y-m-d H:i:s");

    // Logs
    if (!is_dir("logs")) mkdir("logs");
    file_put_contents("logs/inscriptions.txt", "$date - $email\n", FILE_APPEND);

    // Email à l'admin
    $toAdmin = "jeaneudesavocegamou@gmail.com";
    $subjectAdmin = "Nouvelle inscription newsletter";
    $messageAdmin = "Nouvelle inscription : $email\nDate : $date";
    $headers = "From: notifier@getintje.com";
    mail($toAdmin, $subjectAdmin, $messageAdmin, $headers);

    // Email de confirmation
    $subjectUser = "Merci pour votre inscription à getintJE 🎉";
    $messageUser = "Bonjour,\n\nMerci de vous être inscrit à la newsletter getintJE.\nVous recevrez bientôt nos actualités.\n\nÀ bientôt,\nL’équipe getintJE";
    mail($email, $subjectUser, $messageUser, $headers);

    echo "Merci pour votre inscription, $email !";
} else {
    header("Location: index.html");
    exit;
}
?>
