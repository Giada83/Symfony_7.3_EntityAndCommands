// Attendiamo che il DOM sia completamente caricato prima di eseguire qualsiasi script.
document.addEventListener("DOMContentLoaded", function () {
    // --- PRIMA FETCH (per le statistiche del Top Author) ---
    // Questa parte è già corretta e non viene toccata.
    const winnerInfoDiv = document.querySelector(".winner-info");
    if (winnerInfoDiv) {
        const topAuthorElement = document.getElementById("top-author");
        const apiUrl = winnerInfoDiv.dataset.url;

        if (topAuthorElement && apiUrl) {
            fetch(apiUrl)
                .then((response) => response.json())
                .then((data) => {
                    if (data.topAuthor && data.topAuthor.author) {
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
                    console.error("Errore nel caricamento Top Author:", error);
                    topAuthorElement.textContent =
                        "Errore nel caricamento dei dati";
                    topAuthorElement.classList.remove("loading");
                });
        }
    }

    // --- SECONDA FETCH (per il grafico con Chart.js) CON STILE APPLICATO ---
    const chartCanvas = document.getElementById("publishingChart");
    if (chartCanvas) {
        const chartApiUrl = chartCanvas.dataset.url;

        fetch(chartApiUrl)
            .then((response) => response.json())
            .then((chartData) => {
                // 1. Definiamo la palette di colori per le barre
                const barColors = [
                    "rgba(59, 130, 246, 0.7)", // Blu
                    "rgba(239, 68, 68, 0.7)", // Rosso
                    "rgba(245, 158, 11, 0.7)", // Ambra
                    "rgba(16, 185, 129, 0.7)", // Verde
                    "rgba(139, 92, 246, 0.7)", // Viola
                    "rgba(236, 72, 153, 0.7)", // Rosa
                ];

                // 2. Assegniamo i colori al nostro dataset
                if (chartData.datasets && chartData.datasets[0]) {
                    chartData.datasets[0].backgroundColor = barColors;
                    chartData.datasets[0].borderColor = barColors;
                    chartData.datasets[0].borderWidth = 1;
                }

                // 3. Definiamo il font da usare, prendendolo dal tuo CSS
                const siteFont =
                    "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif";

                const ctx = chartCanvas.getContext("2d");
                new Chart(ctx, {
                    type: "bar",
                    data: chartData,
                    // 4. Applichiamo tutte le opzioni di stile
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                bodyFont: { size: 12, family: siteFont },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: { size: 12, family: siteFont },
                                    color: "#64748b", // Colore dei label sull'asse X
                                },
                                grid: {
                                    display: false, // Nasconde le linee verticali della griglia
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { size: 12, family: siteFont },
                                    color: "#64748b", // Colore dei label sull'asse Y
                                },
                                title: {
                                    display: true,
                                    text: "Giorni di Esperienza",
                                    font: {
                                        size: 13,
                                        family: siteFont,
                                        weight: "500",
                                    },
                                    color: "#374151",
                                },
                            },
                        },
                    },
                });
            })
            .catch((error) => {
                console.error(
                    "Errore durante la creazione del grafico:",
                    error
                );
                const chartContainer =
                    document.querySelector(".chart-container");
                if (chartContainer) {
                    chartContainer.innerHTML =
                        '<p style="color: red; text-align: center;">Impossibile caricare il grafico.</p>';
                }
            });
    }
});
