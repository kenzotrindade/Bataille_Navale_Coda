let draggedShipType = null;
let draggedShipSize = 0;
let orientation_ship = "H";
let placedShips = [];
let draggedShipId = null;

const cells = document.querySelectorAll(".cell");
const btnRotate = document.getElementById("rotateBtn");
const allShips = document.querySelectorAll(".ship");
const btnValider = document.getElementById("validateBtn");

console.log("✅ Test de connexion : " + cells.length + " cases trouvées.");

function getCasesCibles(x, y, taille, orientation) {
  let cellules = [];
  for (let i = 0; i < taille; i++) {
    let targetX = x;
    let targetY = y;

    if (orientation === "H") {
      targetX = parseInt(x) + i;
    } else {
      targetY = parseInt(y) + i;
    }

    let attributes = document.querySelector(
      `.cell[data-x='${targetX}'][data-y='${targetY}']`
    );

    if (attributes) {
      cellules.push(attributes);
    }
  }
  return cellules;
}

const ships = document.querySelectorAll(".ship");

ships.forEach((ship) => {
  ship.addEventListener("dragstart", (e) => {
    draggedShipSize = parseInt(e.target.dataset.size);
    draggedShipType = e.target.dataset.type;
    draggedShipId = e.target.id;

    console.log("J'ai attrapé un bateau de taille : " + draggedShipSize);
  });
});

cells.forEach((cell) => {
  // Permet de pouvoir glisser les bateaux
  cell.addEventListener("dragover", (e) => {
    e.preventDefault();
    const case_x = e.target.dataset.x;
    const case_y = e.target.dataset.y;
    const result = getCasesCibles(
      case_x,
      case_y,
      draggedShipSize,
      orientation_ship
    );

    // Permet de savoir si le placement est valide ou non
    let estValide = true;
    if (result.length !== draggedShipSize) {
      estValide = false;
    } else {
      result.forEach((cas) => {
        if (cas.classList.contains("occupied")) {
          estValide = false;
        }
      });
    }

    // Permet de faire les traces de placement en fonction de la validité de placement
    const classeCSS = estValide ? "hover-valid" : "hover-invalid";
    result.forEach((cas) => {
      cas.classList.add(classeCSS);
    });
  });

  // Permet de nettoyer les traces
  cell.addEventListener("dragleave", (e) => {
    const case_x = e.target.dataset.x;
    const case_y = e.target.dataset.y;
    const result = getCasesCibles(
      case_x,
      case_y,
      draggedShipSize,
      orientation_ship
    );

    result.forEach((cas) => {
      cas.classList.remove("hover-valid");
      cas.classList.remove("hover-invalid");
    });
  });

  cell.addEventListener("drop", (e) => {
    e.preventDefault();
    const case_x = e.target.dataset.x;
    const case_y = e.target.dataset.y;
    const result = getCasesCibles(
      case_x,
      case_y,
      draggedShipSize,
      orientation_ship
    );

    let estValide = true;
    if (result.length !== draggedShipSize) {
      estValide = false;
    } else {
      result.forEach((cas) => {
        if (cas.classList.contains("occupied")) {
          estValide = false;
        }
      });
    }

    if (estValide) {
      result.forEach((cas) => {
        cas.classList.add("occupied");
        cas.classList.remove("hover-valid");
      });

      placedShips.push({
        nom: draggedShipType,
        x: case_x,
        y: case_y,
        orientation: orientation_ship,
        taille: draggedShipSize,
      });

      const shipElement = document.getElementById(draggedShipId);
      if (shipElement) {
        shipElement.style.display = "none";
        shipElement.draggable = false;
      }

      if (placedShips.length === 5) {
        const btnValider = document.getElementById("validateBtn");
        btnValider.disabled = false;
        btnValider.style.backgroundColor = "green";
        btnValider.style.color = "white";
        btnValider.style.cursor = "pointer";
      }
    }

    result.forEach((cas) => {
      cas.classList.remove("hover-valid");
      cas.classList.remove("hover-invalid");
    });
  });
});

btnRotate.addEventListener("click", () => {
  if (orientation_ship === "H") {
    orientation_ship = "V";
    btnRotate.innerText = "Orientation : VERTICALE";
    btnRotate.style.backgroundColor = "#e91e63";

    allShips.forEach((ship) => {
      ship.classList.add("verticale");
    });
  } else {
    orientation_ship = "H";
    btnRotate.innerText = "Orientation : HORIZONTALE";
    btnRotate.style.backgroundColor = "#ff9800";

    allShips.forEach((ship) => {
      ship.classList.remove("verticale");
    });
  }
});

btnValider.addEventListener("click", () => {
  const dataToSend = JSON.stringify({ ships: placedShips });

  fetch("../data/save_placement.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: dataToSend,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        window.location.href = "../GUI/GUI_matrice.php";
      } else {
        alert("Erreur lors de la sauvegarde : " + (data.message || "Inconnue"));
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      alert("Impossible de contacter le serveur.");
    });
});
