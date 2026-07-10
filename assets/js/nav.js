document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("section");
    const navLinks = document.querySelectorAll(".nav-link");

    // Index interne pour savoir EXACTEMENT où on en est (indépendant de la nav)
    let currentIndex = 0;
    let isKeyboardScrolling = false;
    let scrollTimeout;

    // 1. Configuration de l'observateur (pour la souris et le visuel)
    const options = {
        root: null,
        rootMargin: "-30% 0px -60% 0px",
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const idSectionVisible = entry.target.getAttribute("id");

                // Si l'utilisateur utilise la souris, on synchronise notre index interne
                if (!isKeyboardScrolling) {
                    const sectionsArray = Array.from(sections);
                    currentIndex = sectionsArray.indexOf(entry.target);
                }

                // Mise à jour visuelle des liens de la nav
                navLinks.forEach(link => link.classList.remove("active"));
                const linkActif = document.querySelector(`.nav-link[href="#${idSectionVisible}"]`);
                if (linkActif) {
                    linkActif.classList.add("active");
                }
            }
        });
    }, options);

    sections.forEach(section => {
        observer.observe(section);
    });

    // ==========================================
    // LOGIQUE CLAVIER FLUIDE ET ULTRA-RÉACTIVE
    // ==========================================
    window.addEventListener("keydown", (e) => {
        if (e.key === "ArrowDown" || e.key === "ArrowUp") {
            // Empêche le comportement par défaut du navigateur
            e.preventDefault();

            // On active le verrou pour empêcher l'observateur de perturber l'index pendant le voyage
            isKeyboardScrolling = true;
            clearTimeout(scrollTimeout);

            // Calcul instantané de la prochaine destination
            if (e.key === "ArrowDown") {
                if (currentIndex < sections.length - 1) {
                    currentIndex++;
                }
            } else if (e.key === "ArrowUp") {
                if (currentIndex > 0) {
                    currentIndex--;
                }
            }

            // On lance le défilement vers la cible
            sections[currentIndex].scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

            // On relâche le verrou dès que le défilement fluide est terminé (~600ms)
            scrollTimeout = setTimeout(() => {
                isKeyboardScrolling = false;
            }, 600);
        }
    });
});


//blog
// ==========================================
// GESTION DES BOUTONS J'AIME & PARTAGE
// ==========================================
const likeButtons = document.querySelectorAll('.btn-like');
const shareButtons = document.querySelectorAll('.btn-share');

// 1. Logique du bouton J'aime (Toggle d'état)
likeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Alterne entre l'état rempli rouge et l'état vide
        btn.classList.toggle('unliked');

        // Petit effet de rebond au clic
        btn.style.transform = "scale(1.4)";
        setTimeout(() => {
            btn.style.transform = "";
        }, 200);
    });
});

// 2. Logique du bouton Partager (API Native Web Share + Fallback)
shareButtons.forEach(btn => {
    btn.addEventListener('click', async () => {
        // Récupère l'URL personnalisée du bloc ou l'URL du site actuel
        const urlToShare = btn.getAttribute('data-url') || window.location.href;

        // Si l'utilisateur est sur mobile ou un navigateur moderne compatible partage
        if (navigator.share) {
            try {
                await navigator.share({
                    title: "Portfolio de Lauren",
                    text: "Regarde ce projet sur mon portfolio !",
                    url: urlToShare
                });
            } catch (err) {
                console.log("Partage annulé");
            }
        } else {
            // Fallback (PC de bureau) : Copie directement le lien dans le presse-papiers
            navigator.clipboard.writeText(urlToShare);

            // Crée un petit badge de notification éphémère
            const toast = document.createElement('div');
            toast.textContent = "Lien copié dans le presse-papiers ! 🚀";
            toast.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: #8a2be2;
                color: white;
                padding: 12px 25px;
                border-radius: 15px;
                font-family: sans-serif;
                font-size: 0.9rem;
                z-index: 9999;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                animation: fadeIn 0.3s ease;
            `;
            document.body.appendChild(toast);

            // Fait disparaître la notification après 2,5 secondes
            setTimeout(() => {
                toast.style.opacity = "0";
                toast.style.transition = "opacity 0.5s ease";
                setTimeout(() => toast.remove(), 500);
            }, 2500);
        }
    });
});


    document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('filter-dropdown-toggle');
    const options = document.getElementById('filter-options');
    const input = document.getElementById('filter-input');
    const cards = document.querySelectorAll('.carte-projet');

    // Ouvrir / Fermer le menu déroulant au clic
    toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    options.style.display = options.style.display === 'block' ? 'none' : 'block';
});

    // Fermer le menu si on clique n'importe où ailleurs sur la page
    document.addEventListener('click', () => {
    options.style.display = 'none';
});

    // Logique de filtrage lors de la sélection d'une technologie
    document.querySelectorAll('.suggestion-item').forEach(item => {
    item.addEventListener('click', (e) => {
    e.stopPropagation();
    const selectedTech = item.getAttribute('data-tech');

    // Mettre à jour le texte du champ input
    input.value = item.textContent.trim();
    options.style.display = 'none';

    // Masquer ou afficher les cartes
    cards.forEach(card => {
    const cardTechsAttribute = card.getAttribute('data-techs');
    // Découpe la chaîne de caractères des technos en tableau
    const cardTechs = cardTechsAttribute ? cardTechsAttribute.split(',') : [];

    if (selectedTech === 'all' || cardTechs.includes(selectedTech)) {
    card.style.display = 'flex';
} else {
    card.style.display = 'none';
}
});
});
});
});
