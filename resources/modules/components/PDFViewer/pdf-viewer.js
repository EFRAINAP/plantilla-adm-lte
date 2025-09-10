/**
 * Clase reutilizable para visor PDF
 * Basado en el código funcional de DocumentosISO
 */
class PDFViewer {
    constructor(pdfUrl, containerId = 'pdfViewer') {
        this.pdfUrl = pdfUrl;
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        
        // Variables del PDF (igual que DocumentosISO)
        this.pdfDoc = null;
        this.pageNum = 1;
        this.pageRendering = false;
        this.pageNumPending = null;
        this.scale = 2.5;
        this.MIN_SCALE = 0.2;
        this.MAX_SCALE = 5.0;
        this.searchText = '';
        this.matchCount = 0;
        this.matchPositions = [];
        this.currentMatch = -1;
        this.modoVerTodo = false;
        
        // Elementos del DOM
        this.elements = {};
        
        this.init();
    }
    
    async init() {
        try {
            this.setupElements();
            this.setupEventListeners();
            await this.loadPDF();
        } catch (error) {
            console.error('Error inicializando PDF viewer:', error);
            this.showError('Error al cargar el visor PDF');
        }
    }
    
    setupElements() {
        // Obtener referencias a los elementos del DOM
        this.elements = {
            loadingIndicator: this.container.querySelector('#loadingIndicator'),
            verTodo: this.container.querySelector('#verTodo'),
            verPaginado: this.container.querySelector('#verPaginado'),
            searchText: this.container.querySelector('#searchText'),
            searchBtn: this.container.querySelector('#searchBtn'),
            matchCount: this.container.querySelector('#matchCount'),
            prevPage: this.container.querySelector('#prevPage'),
            nextPage: this.container.querySelector('#nextPage'),
            zoomIn: this.container.querySelector('#zoomIn'),
            zoomOut: this.container.querySelector('#zoomOut'),
            prevMatch: this.container.querySelector('#prevMatch'),
            nextMatch: this.container.querySelector('#nextMatch'),
            pageNum: this.container.querySelector('#pageNum'),
            pageCount: this.container.querySelector('#pageCount'),
            pdfCanvas: this.container.querySelector('#pdfCanvas'),
            pdfCanvasContainer: this.container.querySelector('#pdfCanvasContainer')
        };
        
        this.canvas = this.elements.pdfCanvas;
        this.ctx = this.canvas.getContext('2d');
        this.canvasContainer = this.elements.pdfCanvasContainer;
        
        // Desactivar menú contextual en el canvas (igual que DocumentosISO)
        this.canvas.addEventListener('contextmenu', (event) => {
            event.preventDefault();
        });
    }
    
    setupEventListeners() {
        // Botones de vista
        this.elements.verTodo.addEventListener('click', () => {
            this.modoVerTodo = true;
            this.renderAllPages();
            this.canvas.style.display = 'none';
            this.canvasContainer.style.display = 'block';

            // Cambia estilos de los botones (igual que DocumentosISO)
            this.elements.verTodo.classList.remove('btn-secondary');
            this.elements.verTodo.classList.add('btn-primary');
            this.elements.verPaginado.classList.remove('btn-primary');
            this.elements.verPaginado.classList.add('btn-secondary');
        });

        this.elements.verPaginado.addEventListener('click', () => {
            this.modoVerTodo = false;
            this.renderPage(this.pageNum);
            this.canvas.style.display = 'block';
            this.canvasContainer.style.display = 'none';

            // Cambia estilos de los botones (igual que DocumentosISO)
            this.elements.verPaginado.classList.remove('btn-secondary');
            this.elements.verPaginado.classList.add('btn-primary');
            this.elements.verTodo.classList.remove('btn-primary');
            this.elements.verTodo.classList.add('btn-secondary');
        });
        
        // Navegación
        this.elements.prevPage.addEventListener('click', () => this.onPrevPage());
        this.elements.nextPage.addEventListener('click', () => this.onNextPage());
        
        // Zoom
        this.elements.zoomIn.addEventListener('click', () => this.zoomIn());
        this.elements.zoomOut.addEventListener('click', () => this.zoomOut());
        
        // Búsqueda
        this.elements.searchBtn.addEventListener('click', () => this.search());
        this.elements.searchText.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.search();
        });
        
        // Navegación de coincidencias
        this.elements.prevMatch.addEventListener('click', () => this.prevMatch());
        this.elements.nextMatch.addEventListener('click', () => this.nextMatch());
    }
    
    async loadPDF() {
        try {
            this.elements.loadingIndicator.textContent = 'Cargando...';
            this.pdfDoc = await pdfjsLib.getDocument(this.pdfUrl).promise;
            this.elements.pageCount.textContent = this.pdfDoc.numPages;
            this.renderPage(this.pageNum); // Renderiza la primera página por defecto
            this.elements.verPaginado.classList.add('btn-primary'); // igual que DocumentosISO
            this.elements.loadingIndicator.textContent = '';
        } catch (error) {
            console.error('Error al cargar el PDF:', error);
            this.showError('Error al cargar el archivo PDF');
        }
    }
    
    // Función renderAllPages (exactamente igual que DocumentosISO)
    async renderAllPages() {
        this.canvasContainer.innerHTML = ''; // Limpiar el contenedor del canvas
        this.matchCount = 0; // Reiniciar contador de coincidencias
        this.matchPositions = []; // Limpiar las posiciones de las coincidencias
        this.elements.matchCount.textContent = this.matchCount;

        for (let i = 1; i <= this.pdfDoc.numPages; i++) {
            let pageCanvas = document.createElement('canvas');
            this.canvasContainer.appendChild(pageCanvas);
            var ctx = pageCanvas.getContext('2d');
            
            try {
                const page = await this.pdfDoc.getPage(i);
                const viewport = page.getViewport({ scale: this.scale });
                pageCanvas.width = viewport.width;
                pageCanvas.height = viewport.height;

                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                await page.render(renderContext).promise;

                // Si hay texto para buscar, ejecutamos la búsqueda
                if (this.searchText) {
                    const textContent = await page.getTextContent();
                    textContent.items.forEach((item) => {
                        let startIndex = 0;
                        while ((startIndex = item.str.toLowerCase().indexOf(this.searchText.toLowerCase(), startIndex)) > -1) {
                            let endIndex = startIndex + this.searchText.length;
                            let tx = pdfjsLib.Util.transform(
                                pdfjsLib.Util.transform(viewport.transform, item.transform),
                                [1, 0, 0, -1, 0, 0]
                            );
                            let width = (endIndex - startIndex) * item.width / item.str.length;
                            let xPos = tx[4] + (startIndex / item.str.length) * item.width;
                            let yPos = tx[5];
                            ctx.fillStyle = 'lime';
                            ctx.fillRect(xPos, yPos, width * 4.5, item.height * 0.5);

                            // Guardar: [canvas, x, y, width, height, pageIndex]
                            this.matchPositions.push([pageCanvas, xPos, yPos, width, item.height, i-1]);
                            this.matchCount++;
                            startIndex = endIndex;
                        }
                    });
                }
                this.elements.matchCount.textContent = this.matchCount;
            } catch (error) {
                console.error('Error al renderizar la página:', error);
            }
        }

        // Mostrar mensaje solo una vez después de procesar todas las páginas
        if (this.searchText && this.matchCount === 0) {
            this.elements.loadingIndicator.textContent = 'No se encontraron coincidencias.';
        } else {
            this.elements.loadingIndicator.textContent = ''; // Limpiar mensaje si hay coincidencias
        }
    }
    
    // Función renderPage (exactamente igual que DocumentosISO)
    renderPage(num) {
        this.pageRendering = true;
        this.pdfDoc.getPage(num).then((page) => {
            var viewport = page.getViewport({ scale: this.scale });
            this.canvas.height = viewport.height;
            this.canvas.width = viewport.width;

            var renderContext = {
                canvasContext: this.ctx,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);

            renderTask.promise.then(() => {
                this.pageRendering = false;
                if (this.pageNumPending !== null) {
                    this.renderPage(this.pageNumPending);
                    this.pageNumPending = null;
                }

                // Buscar y resaltar texto (exactamente igual que DocumentosISO)
                if (this.searchText) {
                    page.getTextContent().then((textContent) => {
                        this.matchCount = 0; // Reiniciar contador de coincidencias
                        this.matchPositions = []; // Limpiar las posiciones de las coincidencias

                        textContent.items.forEach((item) => {
                            let searchPos = 0; // Variable para ir buscando las posiciones

                            // Bucle para encontrar todas las coincidencias dentro de item.str
                            while ((searchPos = item.str.toLowerCase().indexOf(this.searchText.toLowerCase(), searchPos)) !== -1) {
                                // Aquí encontramos la coincidencia, ahora calculamos su posición exacta
                                let endPos = searchPos + this.searchText.length;

                                // Calcular la posición X e Y para resaltar la coincidencia
                                let tx = pdfjsLib.Util.transform(
                                    pdfjsLib.Util.transform(viewport.transform, item.transform),
                                    [1, 0, 0, -1, 0, 0]
                                );

                                // El cálculo de la posición de la coincidencia
                                let width = (endPos - searchPos) * item.width / item.str.length;
                                let xPos = tx[4] + (searchPos / item.str.length) * item.width;
                                let yPos = tx[5];

                                // Resaltamos la coincidencia
                                this.ctx.fillStyle = 'lime'; // Resaltado en amarillo
                                this.ctx.fillRect(xPos, yPos, width * 4.5, item.height * 0.5);

                                // Guardamos la posición para usarla si es necesario
                                this.matchPositions.push([xPos, yPos, width, item.height]);

                                this.matchCount++; // Incrementamos el contador de coincidencias

                                // Avanzamos para buscar la siguiente coincidencia en el mismo item
                                searchPos = endPos;
                            }
                        });

                        // Mostrar el número total de coincidencias encontradas
                        this.elements.matchCount.textContent = this.matchCount;

                        if (this.matchCount === 0) {
                            this.elements.loadingIndicator.textContent = 'No se encontraron coincidencias.'; // Mostrar mensaje si no hay coincidencias
                        } else {
                            this.highlightMatch(this.currentMatch); // Resaltar la coincidencia actual (si existe)
                        }
                    }).catch((error) => {
                        console.error('Error buscando texto:', error);
                    });
                }
            });
        });

        this.elements.pageNum.textContent = num;
    }
    
    // highlightMatch (exactamente igual que DocumentosISO)
    highlightMatch(index) {
        if (this.matchPositions.length > 0 && index >= 0 && index < this.matchPositions.length) {
            var match = this.matchPositions[index];
            this.ctx.strokeStyle = 'red';
            this.ctx.lineWidth = 2;
            // Usa los valores reales de la coincidencia
            this.ctx.strokeRect(match[0], match[1], match[2], match[3]);
        }
    }
    
    // scrollToMatch (exactamente igual que DocumentosISO)
    async scrollToMatch(index) {
        if (this.modoVerTodo && this.matchPositions.length > 0 && index >= 0 && index < this.matchPositions.length) {
            var match = this.matchPositions[index];
            var canvas = match[0];
            var x = match[1];
            var y = match[2];
            var width = match[3];
            var height = match[4];
            var pageIndex = match[5];

            // Volver a renderizar el canvas para limpiar resaltados previos
            const page = await this.pdfDoc.getPage(pageIndex + 1);
            const viewport = page.getViewport({ scale: this.scale });
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            var ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;

            // Vuelve a resaltar todas las coincidencias de esa página
            const textContent = await page.getTextContent();
            textContent.items.forEach((item) => {
                let startIndex = 0;
                while ((startIndex = item.str.toLowerCase().indexOf(this.searchText.toLowerCase(), startIndex)) > -1) {
                    let endIndex = startIndex + this.searchText.length;
                    let tx = pdfjsLib.Util.transform(
                        pdfjsLib.Util.transform(viewport.transform, item.transform),
                        [1, 0, 0, -1, 0, 0]
                    );
                    let w = (endIndex - startIndex) * item.width / item.str.length;
                    let xP = tx[4] + (startIndex / item.str.length) * item.width;
                    let yP = tx[5];
                    ctx.fillStyle = 'lime';
                    ctx.fillRect(xP, yP, w * 4.5, item.height * 0.5);
                    startIndex = endIndex;
                }
            });

            // Dibuja el borde rojo solo en la coincidencia actual
            ctx.save();
            ctx.strokeStyle = 'red';
            ctx.lineWidth = 2;
            ctx.strokeRect(x, y, width * 4.5, height * 0.5);
            ctx.restore();

            // Scroll al canvas y a la coincidencia
            canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Si el canvas es muy grande horizontalmente, ajusta el scroll horizontal
            var container = this.canvasContainer;
            container.scrollLeft = x - container.clientWidth / 2;
        }
    }
    
    // nextMatch y prevMatch (exactamente igual que DocumentosISO)
    nextMatch() {
        if (this.currentMatch < this.matchPositions.length - 1) {
            this.currentMatch++;
            if (this.modoVerTodo) {
                this.scrollToMatch(this.currentMatch);
            } else {
                this.renderPage(this.pageNum);
            }
        }
    }

    prevMatch() {
        if (this.currentMatch > 0) {
            this.currentMatch--;
            if (this.modoVerTodo) {
                this.scrollToMatch(this.currentMatch);
            } else {
                this.renderPage(this.pageNum);
            }
        }
    }
    
    // queueRenderPage (exactamente igual que DocumentosISO)
    queueRenderPage(num) {
        if (this.pageRendering) {
            this.pageNumPending = num;
        } else {
            this.renderPage(num);
        }
    }
    
    // onPrevPage y onNextPage (exactamente igual que DocumentosISO)
    onPrevPage() {
        if (this.pageNum <= 1) {
            return;
        }
        this.pageNum--;
        this.queueRenderPage(this.pageNum);
    }

    onNextPage() {
        if (this.pageNum >= this.pdfDoc.numPages) {
            return;
        }
        this.pageNum++;
        this.queueRenderPage(this.pageNum);
    }
    
    // zoomIn y zoomOut (exactamente igual que DocumentosISO)
    zoomIn() {
        if (this.scale < this.MAX_SCALE) {
            this.scale = Math.min(this.scale + 0.1, this.MAX_SCALE);
            if (this.modoVerTodo) {
                this.renderAllPages();
            } else {
                this.renderPage(this.pageNum);
            }
        }
    }

    zoomOut() {
        if (this.scale > this.MIN_SCALE) {
            this.scale = Math.max(this.scale - 0.1, this.MIN_SCALE);
            if (this.modoVerTodo) {
                this.renderAllPages();
            } else {
                this.renderPage(this.pageNum);
            }
        }
    }
    
    // search (exactamente igual que DocumentosISO)
    search() {
        this.searchText = this.elements.searchText.value.trim();
        if (!this.searchText) return;

        this.currentMatch = -1;
        this.matchCount = 0;
        this.elements.matchCount.textContent = this.matchCount;
        this.elements.loadingIndicator.textContent = '';

        if (this.modoVerTodo) {
            this.renderAllPages();
        } else {
            this.renderPage(this.pageNum);
        }
    }
    
    showError(message) {
        this.elements.loadingIndicator.textContent = message;
        this.elements.loadingIndicator.style.color = 'red';
    }
    
    // Método público para cargar un nuevo PDF
    async loadNewPDF(pdfUrl) {
        this.pdfUrl = pdfUrl;
        this.pageNum = 1;
        this.searchText = '';
        this.matchCount = 0;
        this.matchPositions = [];
        this.currentMatch = -1;
        this.pageRendering = false;
        this.pageNumPending = null;
        this.elements.searchText.value = '';
        await this.loadPDF();
    }
}

// Hacer la clase disponible globalmente
window.PDFViewer = PDFViewer;
