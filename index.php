<?php
session_start();

// Initialisierung der Session-Variablen
if (!isset($_SESSION['display'])) {
    $_SESSION['display'] = '';
}
if (!isset($_SESSION['is_result'])) {
    $_SESSION['is_result'] = false; // Gibt an, ob das letzte Ergebnis berechnet wurde
}
if (!isset($_SESSION['disable_equals'])) {
    $_SESSION['disable_equals'] = false; // Gibt an, ob "=" blockiert werden soll
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $button = $_POST['button'];

    // Reset-Funktion
    if ($button === 'C') {
        $_SESSION['display'] = '0';
        $_SESSION['is_result'] = false;
        $_SESSION['disable_equals'] = false;

    // Gleichheitszeichen gedrückt
    } elseif ($button === '=') {
        // Wenn "=" blockiert ist, abbrechen
        if ($_SESSION['disable_equals']) {
            $_SESSION['display'] = 'Ungültige Eingabe';
            return;
        }

        // Überprüfen, ob der Ausdruck vollständig ist
        $expression = $_SESSION['display'];
        if (preg_match('/[+\-\*\/]$/', $expression)) { // Ausdruck endet auf einen Operator
            $_SESSION['display'] = 'Fehler: Unvollständiger Ausdruck';
            $_SESSION['is_result'] = true;
        } else {
            try {
                // Eval-Ausdruck ausführen
                $_SESSION['display'] = eval('return ' . $expression . ';');
                $_SESSION['is_result'] = true;
                $_SESSION['disable_equals'] = true; // Blockiere "=" nach Berechnung
            } catch (Exception $e) {
                $_SESSION['display'] = 'Fehler';
            }
        }

    // Normale Eingabe nach Ergebnis
    } elseif ($_SESSION['is_result']) {
        if (is_numeric($button)) {
            $_SESSION['display'] = $button; // Startet eine neue Eingabe
            $_SESSION['is_result'] = false;
            $_SESSION['disable_equals'] = false; // Aktiviert "=" wieder
        } else {
            $_SESSION['display'] = 'Ungültige Eingabe';
        }

    // Normale Eingabe
    } else {
        if ($_SESSION['display'] === '0' || $_SESSION['display'] === 'Fehler') {
            $_SESSION['display'] = $button;
        } else {
            $_SESSION['display'] .= $button;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taschenrechner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calculator">
        <form method="POST">
            <div class="display">
                <?php
                // Aktuelle Anzeige ausgeben
                echo htmlspecialchars($_SESSION['display']);
                ?>
            </div>
            <div class="buttons">
                <!-- Zahlen und Operatoren -->
                <button type="submit" name="button" value="7">7</button>
                <button type="submit" name="button" value="8">8</button>
                <button type="submit" name="button" value="9">9</button>
                <button type="submit" name="button" value="/" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>÷</button>

                <button type="submit" name="button" value="4">4</button>
                <button type="submit" name="button" value="5">5</button>
                <button type="submit" name="button" value="6">6</button>
                <button type="submit" name="button" value="*" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>×</button>

                <button type="submit" name="button" value="1">1</button>
                <button type="submit" name="button" value="2">2</button>
                <button type="submit" name="button" value="3">3</button>
                <button type="submit" name="button" value="-" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>-</button>

                <button type="submit" name="button" value="C">C</button>
                <button type="submit" name="button" value="0">0</button>
                <!-- "=" bleibt gelb auch wenn es deaktiviert ist -->
                <button type="submit" name="button" value="=" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>=</button>
                <button type="submit" name="button" value="+" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>+</button>
            </div>
        </form>
    </div>
</body>
</html>