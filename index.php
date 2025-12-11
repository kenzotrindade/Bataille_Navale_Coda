<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../GUI/CSS/style_index.css">
  <title>Bataille Navale - Launcher</title>

  <script>
    (function() {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      document.body.className = savedTheme + '-theme';
    })();
  </script>
</head>

<body>
  <div class="launcher-backdrop"></div>

  <div class="main-menu-container">
    <h1 class="game-title">BATAILLE NAVALE</h1>
    <p class="slogan">La Guerre des Flottes Commence Maintenant.</p>

    <div class="theme-selector index-select">
      <h2>Thème Actuel :</h2>
      <select id="theme-select" onchange="appliquerTheme(this.value)">
        <option value="classic">Classique</option>
        <option value="starwars">Star Wars</option>
        <option value="apoc">Post-Apocalyptique</option>
      </select>
    </div>

    <div class="button-group">
      <a href="utils/player.php" class="btn btn-play">▶️ Lancer une nouvelle partie</a>
      <a href="install_DB.php" class="btn btn-db">⚙️ Installer/Réinitialiser la Base de Données</a>
    </div>
  </div>

  <script>
    function appliquerTheme(theme) {
      document.body.className = '';
      document.body.classList.add(theme + '-theme');
      localStorage.setItem('gameTheme', theme);
    }
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      const themeSelect = document.getElementById('theme-select');
      if (themeSelect) {
        themeSelect.value = savedTheme;
      }
    });
  </script>
</body>

</html>