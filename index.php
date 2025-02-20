<?php
session_start();

// Initialisierung der Session-Variablen
if (!isset($_SESSION['display'])) {
    $_SESSION['display'] = ''; // Startet mit leerem Feld
}
if (!isset($_SESSION['is_result'])) {
    $_SESSION['is_result'] = false;
}
if (!isset($_SESSION['disable_equals'])) {
    $_SESSION['disable_equals'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $button = $_POST['button'];

    // Reset-Funktion
    if ($button === 'C') {
        $_SESSION['display'] = ''; // Jetzt wird das Feld komplett geleert
        $_SESSION['is_result'] = false;
        $_SESSION['disable_equals'] = false;
    
    } elseif ($button === '=') {
        if ($_SESSION['disable_equals']) {
            $_SESSION['display'] = 'Ungültige Eingabe';
            return;
        }

        $expression = $_SESSION['display'];
        if (preg_match('/[+\-*\/]$/', $expression)) {
            $_SESSION['display'] = 'Fehler: Unvollständiger Ausdruck';
            $_SESSION['is_result'] = true;
        } else {
            try {
                $_SESSION['display'] = eval('return ' . $expression . ';');
                $_SESSION['is_result'] = true;
                $_SESSION['disable_equals'] = true;
            } catch (Exception $e) {
                $_SESSION['display'] = 'Fehler';
            }
        }
    } elseif ($_SESSION['is_result']) {
        if (is_numeric($button)) {
            $_SESSION['display'] = $button;
            $_SESSION['is_result'] = false;
            $_SESSION['disable_equals'] = false;
        } else {
            $_SESSION['display'] = 'Ungültige Eingabe';
        }
    } else {
        if ($_SESSION['display'] === '' || $_SESSION['display'] === 'Fehler') {
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
    <div class="calculator-container">
        <div class="calculator">
            <form method="POST">
                <div class="display">
                    <?php echo htmlspecialchars($_SESSION['display']); ?>
                </div>
                <div class="buttons">
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
                    <button type="submit" name="button" value="=" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>=</button>
                    <button type="submit" name="button" value="+" class="equals" <?php if ($_SESSION['disable_equals']) echo 'disabled'; ?>>+</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>