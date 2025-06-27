// Attendiamo che il DOM sia caricato
document.addEventListener("DOMContentLoaded", function () {
    // 1. Troviamo l'elemento che conterrà il risultato e che ha l'URL
    const winnerInfoDiv = document.querySelector(".winner-info");

    // 2. Eseguiamo lo script solo se l'elemento esiste in questa pagina
    if (winnerInfoDiv) {
        // 3. Leggiamo l'URL dall'attributo data-url
        const apiUrl = winnerInfoDiv.dataset.url;
        const topAuthorElement = document.getElementById("top-author");

        fetch(apiUrl) // Usiamo l'URL che abbiamo appena letto
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Errore nel caricamento dei dati");
                }
                return response.json();
            })
            .then((data) => {
                if (data.topAuthor) {
                    console.log(data);
                    topAuthorElement.innerHTML = `
                        <span class="author-name">${data.topAuthor.author.name}</span> 
                        con <span class="article-count">${data.topAuthor.article_count}</span> articoli
                    `;
                } else {
                    topAuthorElement.textContent = "Nessun autore trovato";
                }
                topAuthorElement.classList.remove("loading");
            })
            .catch((error) => {
                console.error("Errore:", error);
                topAuthorElement.textContent =
                    "Errore nel caricamento dei dati";
                topAuthorElement.classList.remove("loading");
            });
    }
});
