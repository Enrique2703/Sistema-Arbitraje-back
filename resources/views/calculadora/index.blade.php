@extends('layouts.app')

@section('content')

<style>
/* Ocultar flechitas de inputs tipo number */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}
</style>

<div class="min-h-screen bg-white flex flex-col py-8">
    <div class="w-full bg-white">
        <!-- Header principal -->
        <div class="flex flex-col md:flex-row items-center justify-between px-16 pt-8 pb-4">
            <h1 class="text-3xl font-bold text-black mb-4 md:mb-0">Calculadora</h1>
            <div class="flex items-center gap-2">
                <button onclick="mostrarModalTarifario()" class="px-8 py-2 bg-black text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition flex items-center gap-2">
                    Ver Tarifario
                </button>
                        <!-- Modal Tarifario -->
                        <div id="modalTarifario" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fadeIn">
                                <h3 class="text-2xl font-bold mb-6">Tarifario</h3>
                                <div class="flex items-center mb-4 gap-2">
                                    <input type="text" class="border rounded-lg px-3 py-2 w-full bg-gray-50" value="Ya tienes un tarifario subido" readonly>
                                    <button type="button" onclick="verAdjuntoTarifario()" class="flex flex-row items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-200" style="height:40px; min-width:120px;">
                                        <i class="bi bi-eye"></i>
                                        <span style="line-height:1;">Ver adjunto</span>
                                    </button>
                                <!-- Modal para ver el PDF del tarifario (se mueve al final del body) -->
                                <!-- Modal para ver el PDF del tarifario (fuera de los contenedores principales) -->
                                <div id="modalVerTarifario" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                                    <div class="bg-white rounded-lg shadow-xl w-full max-w-md animate-fadeIn relative" style="margin: 0 auto; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
                                        <button type="button" onclick="ocultarModalVerTarifario()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 z-10" style="padding: 4px;">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-200" style="flex-shrink: 0;">
                                            <svg style="width: 28px; height: 28px; min-width: 28px; opacity: 0.7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                <polyline points="13 2 13 9 20 9"></polyline>
                                            </svg>
                                            <br>
                                            <span id="nombreArchivoVerTarifario" class="flex-1 text-base font-semibold text-gray-800 truncate">Tarifario.pdf</span>
                                        </div>
                                        <div class="flex justify-end gap-3 px-6 py-4" style="flex-shrink: 0;">
                                            <button type="button" onclick="verTarifarioEnVentana()" class="px-5 py-2 rounded-lg bg-gray-100 border border-gray-300 text-gray-700 font-semibold hover:bg-gray-200 transition flex items-center gap-2">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>
                                            <button type="button" onclick="downloadTarifario()" class="px-5 py-2 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition flex items-center gap-2">
                                                <i class="bi bi-download"></i> Descargar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                async function verAdjuntoTarifario() {
                                    // Obtener el nombre real del archivo y mostrar modal compacto solo con el botón Descargar
                                    let nombreArchivo = 'Tarifario.pdf';
                                    const modal = document.getElementById('modalVerTarifario');
                                    try {
                                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                                        if (!token) throw new Error();
                                        const responseInfo = await fetch(`/api/tarifario?t=${Date.now()}`, {
                                            headers: {
                                                'Authorization': `Bearer ${token}`,
                                                'Cache-Control': 'no-cache',
                                                'Pragma': 'no-cache'
                                            }
                                        });
                                        if (responseInfo.ok) {
                                            const data = await responseInfo.json();
                                            if (Array.isArray(data) && data.length > 0 && data[0].nombre_original) {
                                                nombreArchivo = data[0].nombre_original;
                                            }
                                        }
                                    } catch (e) {}
                                    // Mostrar solo el nombre y el botón Descargar
                                    if (modal) {
                                        document.getElementById('nombreArchivoVerTarifario').textContent = nombreArchivo;
                                        modal.classList.remove('hidden');
                                    }
                                }

                                function ocultarModalVerTarifario() {
                                    document.getElementById('modalVerTarifario').classList.add('hidden');
                                    document.getElementById('tarifarioViewer').innerHTML = '';
                                }

                                async function verTarifarioEnVentana() {
                                    try {
                                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                                        if (!token) {
                                            alert('No se encontró el token de autenticación');
                                            return;
                                        }
                                        
                                        // Obtener el PDF
                                        const response = await fetch(`/api/tarifario/download?t=${Date.now()}`, {
                                            headers: {
                                                'Authorization': `Bearer ${token}`,
                                                'Accept': 'application/pdf'
                                            }
                                        });
                                        
                                        if (!response.ok) {
                                            throw new Error('Error al cargar el tarifario');
                                        }
                                        
                                        // Convertir la respuesta a blob
                                        const blob = await response.blob();
                                        
                                        // Crear URL del blob
                                        const url = window.URL.createObjectURL(blob);
                                        
                                        // Abrir en nueva ventana
                                        window.open(url, '_blank');
                                        
                                        // Liberar la URL después de un tiempo
                                        setTimeout(() => window.URL.revokeObjectURL(url), 100);
                                    } catch (error) {
                                        console.error('Error al visualizar tarifario:', error);
                                        alert('Error al visualizar el tarifario');
                                    }
                                }
                                </script>
                                </div>
                                <div class="mb-2 font-semibold">Reemplazar tarifario</div>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center text-gray-400 mb-8 cursor-pointer hover:border-gray-400 transition" id="dropzoneTarifario">
                                    <i class="bi bi-upload text-3xl mb-2"></i>
                                    <span id="nombreArchivoTarifario" class="text-sm text-gray-600 mb-2"></span>
                                    <span>Adjuntar archivos (PDF)</span>
                                    <input type="file" accept="application/pdf" class="hidden" id="inputTarifario">
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" onclick="ocultarModalTarifario()" class="px-6 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-100">Cancelar</button>
                                    <button type="button" onclick="subirTarifario()" class="px-6 py-2 rounded-lg bg-black text-white font-semibold hover:bg-gray-800">Guardar</button>
                                </div>
                            </div>
                        </div>
                <div class="relative flex items-center">
                    <input type="text" id="tipoCambio" value="3.54" class="border border-gray-300 rounded-lg px-3 py-2 text-base w-28 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tipo de Cambio ($1)">
                    <button class="absolute right-2 text-gray-400 hover:text-black">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs principales -->
        <div class="flex border-b border-gray-200 px-16">
            <button id="btnDeterminada" onclick="cambiarTipo('determinada')" class="px-8 py-2 text-base font-semibold text-black border-b-2 border-black -mb-px focus:outline-none">Determinada</button>
            <button id="btnIndeterminada" onclick="cambiarTipo('indeterminada')" class="px-8 py-2 text-base font-semibold text-gray-400 border-b-2 border-transparent hover:text-black hover:border-black -mb-px focus:outline-none">Indeterminada</button>
        </div>

        <div class="px-16 pt-8">
            <!-- Sección Determinada -->
            <div id="seccionDeterminada">
                <h2 class="text-2xl font-bold text-black mb-6">Cuantía Determinada</h2>
                <!-- Tabs secundarios -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button id="btnGastosAdmin" onclick="cambiarTabCuantia('gastos_administrativos')" class="px-6 py-2 text-base font-medium text-black border-b-2 border-black -mb-px focus:outline-none">Gastos Administrativos del Centro de Arbitraje</button>
                    <button id="btnArbitroUnico" onclick="cambiarTabCuantia('honorarios_arbitros')" class="px-6 py-2 text-base font-medium text-gray-400 border-b-2 border-transparent hover:text-black hover:border-black -mb-px focus:outline-none">Honorarios del Árbitro Único</button>
                    <button id="btnTribunalArbitral" onclick="cambiarTabCuantia('honorarios_tribunales')" class="px-6 py-2 text-base font-medium text-gray-400 border-b-2 border-transparent hover:text-black hover:border-black -mb-px focus:outline-none">Honorarios del Tribunal Arbitral</button>
                    <button id="btnSecretarioArbitral" onclick="cambiarTabCuantia('honorarios_secretarios')" class="px-6 py-2 text-base font-medium text-gray-400 border-b-2 border-transparent hover:text-black hover:border-black -mb-px focus:outline-none">Honorarios del Secretario Arbitral</button>
                </div>

                <div class="flex justify-end mb-4">
                    <button onclick="mostrarModalCuantia()" class="px-6 py-2 bg-black text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition flex items-center gap-2">
                        Agregar Cuantía
                    </button>
                </div>

                <!-- Modal Agregar Cuantía -->
                <div id="modalCuantia" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fadeIn">
                        <h3 id="tituloModalCuantia" class="text-2xl font-bold mb-6">Agregar Cuantía</h3>
                        <form id="formCuantia" autocomplete="off">
                            <input type="hidden" name="id" id="cuantiaId">
                            <div class="grid grid-cols-2 gap-4 mb-4 items-center">
                                <label class="font-medium">N° Escala</label>
                                <input type="number" name="escala" id="cuantiaEscala" class="border rounded-lg px-3 py-2 w-full" placeholder="N° de escala" required min="1" step="1">
                                <label class="font-medium">Rango Mín</label>
                                <input type="number" name="rango_min" id="cuantiaRangoMin" step="0.01" min="0" class="border rounded-lg px-3 py-2 w-full" placeholder="Rango Mínimo" required>
                                <label class="font-medium">Rango Máx.</label>
                                <input type="number" name="rango_max" id="cuantiaRangoMax" step="0.01" min="0" class="border rounded-lg px-3 py-2 w-full" placeholder="Rango Máximo" required>
                                <label class="font-medium">Porcentaje %</label>
                                <input type="number" name="porcentaje" id="cuantiaPorcentaje" step="0.01" class="border rounded-lg px-3 py-2 w-full" placeholder="Porcentaje" required>
                                <label class="font-medium">Monto Máx.</label>
                                <input type="number" name="monto_max" id="cuantiaMontoMax" step="0.01" class="border rounded-lg px-3 py-2 w-full" placeholder="Monto Máximo">
                                <label class="font-medium">Monto Base</label>
                                <input type="number" name="monto_base" id="cuantiaMontoBase" step="0.01" class="border rounded-lg px-3 py-2 w-full" placeholder="Monto Base">
                                <label class="font-medium">Regla</label>
                                <textarea name="regla" id="cuantiaRegla" class="border rounded-lg px-3 py-2 w-full resize-none" placeholder="Regla" rows="2"></textarea>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" onclick="ocultarModalCuantia()" class="px-6 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-100">Cancelar</button>
                                <button type="submit" class="px-6 py-2 rounded-lg bg-black text-white font-semibold hover:bg-gray-800">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700">
                                <th class="px-4 py-3 font-semibold text-left">Escala</th>
                                <th class="px-4 py-3 font-semibold text-left">Rango Min.</th>
                                <th class="px-4 py-3 font-semibold text-left">Rango Max.</th>
                                <th class="px-4 py-3 font-semibold text-left">Porcentaje %</th>
                                <th class="px-4 py-3 font-semibold text-left">Monto Máximo</th>
                                <th class="px-4 py-3 font-semibold text-left">Regla</th>
                                <th class="px-4 py-3 font-semibold text-left">Monto Base</th>
                                <th class="px-4 py-3 font-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCuantias">
                            <!-- Aquí se renderizan las filas dinámicamente con JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div id="paginationContainer" class="px-6 py-4 border-t bg-white mt-4 rounded-lg shadow">
                    <nav id="paginationControls" class="flex items-center justify-between" aria-label="Pagination">
                    </nav>
                </div>
            </div>

            <!-- Sección Indeterminada -->
            <div id="seccionIndeterminada" class="hidden">
                <h2 class="text-2xl font-bold text-black mb-8">Cuantía Indeterminada</h2>
                <form class="space-y-6 max-w-xl">
                    <div>
                        <label class="font-medium block mb-2">Porcentaje de Árbitro Único / Tribunal Arbitral</label>
                        <div class="flex items-center w-full">
                            <input type="number" step="0.01" class="border rounded-lg px-3 py-2 w-full" value="3.10">
                            <span class="ml-2 text-lg font-semibold">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium block mb-2">Porcentaje de Secretaria Arbitral / Gastos Administrativos</label>
                        <div class="flex items-center w-full">
                            <input type="number" step="0.01" class="border rounded-lg px-3 py-2 w-full" value="2.50">
                            <span class="ml-2 text-lg font-semibold">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium block mb-2">Porcentaje de Nulidad de Contrato</label>
                        <div class="flex items-center w-full">
                            <input type="number" step="0.01" class="border rounded-lg px-3 py-2 w-full" value="75.00">
                            <span class="ml-2 text-lg font-semibold">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium block mb-2">Porcentaje de Resolución de Contrato</label>
                        <div class="flex items-center w-full">
                            <input type="number" step="0.01" class="border rounded-lg px-3 py-2 w-full" value="50.00">
                            <span class="ml-2 text-lg font-semibold">%</span>
                        </div>
                    </div>
                    <button type="submit" class="mt-4 px-8 py-2 bg-black text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Tarifario lógica
    async function mostrarModalTarifario() {
        document.getElementById('modalTarifario').classList.remove('hidden');
        await cargarNombreArchivoTarifario();
    }
    function ocultarModalTarifario() {
        document.getElementById('modalTarifario').classList.add('hidden');
    }
    // Drag & drop para PDF
    const dropzone = document.getElementById('dropzoneTarifario');
    const inputTarifario = document.getElementById('inputTarifario');
    if(dropzone && inputTarifario) {
        dropzone.addEventListener('click', () => inputTarifario.click());
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('border-blue-400'); });
        dropzone.addEventListener('dragleave', e => { e.preventDefault(); dropzone.classList.remove('border-blue-400'); });
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400');
            if(e.dataTransfer.files.length) {
                inputTarifario.files = e.dataTransfer.files;
                mostrarNombreArchivoSeleccionado();
            }
        });
        inputTarifario.addEventListener('change', mostrarNombreArchivoSeleccionado);
    }

    function mostrarNombreArchivoSeleccionado() {
        const input = document.getElementById('inputTarifario');
        const nombre = input.files && input.files.length > 0 ? input.files[0].name : '';
        document.getElementById('nombreArchivoTarifario').textContent = nombre ? `Archivo seleccionado: ${nombre}` : '';
    }
    async function subirTarifario() {
        const input = document.getElementById('inputTarifario');
        if (!input.files || input.files.length === 0) {
            alert('Por favor seleccione un archivo PDF.');
            return;
        }
        const file = input.files[0];
        const formData = new FormData();
        formData.append('tarifario', file);
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch('/api/tarifario/upload', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                console.error('Error del servidor:', errorData);
                throw new Error(errorData.message || 'Error al subir el archivo');
            }
            
            const result = await response.json();
            console.log('Archivo subido:', result);
            
            // Esperar un momento para que el servidor termine de procesar
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Actualizar el nombre del archivo en el campo de visualización
            await cargarNombreArchivoTarifario();
            
            alert('Archivo subido correctamente');
            
            // Limpiar el input de archivo y el texto del dropzone
            input.value = '';
            document.getElementById('nombreArchivoTarifario').textContent = '';
            
            ocultarModalTarifario();
        } catch (error) {
            console.error('Error completo:', error);
            alert('Error al subir el archivo: ' + error.message);
        }
    }

    // Modal lógica
    function mostrarModalCuantia() {
        // Limpiar formulario y resetear título al abrir para crear nueva
        if (!document.getElementById('cuantiaId').value) {
            document.getElementById('formCuantia').reset();
            document.getElementById('tituloModalCuantia').textContent = 'Agregar Cuantía';
        }
        document.getElementById('modalCuantia').classList.remove('hidden');
    }
    function ocultarModalCuantia() {
        document.getElementById('modalCuantia').classList.add('hidden');
        document.getElementById('formCuantia').reset();
        document.getElementById('tituloModalCuantia').textContent = 'Agregar Cuantía';
    }

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') ocultarModalCuantia();
    });

    let tipoCalculadora = 'determinada';
    let tabCuantiaActual = 'gastos_administrativos';
    
    // Variables de paginación
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 6;
    let allData = [];

    // Función para cambiar entre tabs de cuantía
    function cambiarTabCuantia(tab) {
        tabCuantiaActual = tab;
        currentPage = 1; // Resetear a página 1 al cambiar de tab
        
        // Actualizar estilos de los botones
        const tabs = ['gastos_administrativos', 'honorarios_arbitros', 'honorarios_tribunales', 'honorarios_secretarios'];
        const tabBtns = {
            'gastos_administrativos': 'btnGastosAdmin',
            'honorarios_arbitros': 'btnArbitroUnico',
            'honorarios_tribunales': 'btnTribunalArbitral',
            'honorarios_secretarios': 'btnSecretarioArbitral'
        };
        
        tabs.forEach(t => {
            const btn = document.getElementById(tabBtns[t]);
            if (t === tab) {
                btn.classList.add('text-black', 'border-black');
                btn.classList.remove('text-gray-400', 'border-transparent');
            } else {
                btn.classList.remove('text-black', 'border-black');
                btn.classList.add('text-gray-400', 'border-transparent');
            }
        });
        
        // Cargar datos de la tabla correspondiente
        cargarDatosCuantia(tab);
    }

    // Guardar cuantía en la tabla correspondiente
    document.getElementById('formCuantia').onsubmit = async function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const datos = Object.fromEntries(formData.entries());
        const id = datos.id;
        
        // Validar rango mínimo y máximo
        const rangoMin = parseFloat(datos.rango_min);
        const rangoMax = parseFloat(datos.rango_max);
        
        if (isNaN(rangoMin) || isNaN(rangoMax)) {
            alert('El rango mínimo y máximo deben ser valores numéricos válidos');
            return;
        }
        
        if (rangoMin < 0 || rangoMax < 0) {
            alert('El rango mínimo y máximo deben ser valores positivos');
            return;
        }
        
        if (rangoMin >= rangoMax) {
            alert('El rango mínimo debe ser menor que el rango máximo');
            return;
        }
        
        // Validar monto base y monto máximo
        const montoBase = parseFloat(datos.monto_base);
        const montoMax = parseFloat(datos.monto_max);
        
        if (!isNaN(montoBase) && !isNaN(montoMax) && montoBase > montoMax) {
            alert('El monto base no puede ser mayor que el monto máximo');
            return;
        }
        
        // Eliminar id de datos si está vacío
        if (!id) delete datos.id;
        
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const url = id ? `/api/${tabCuantiaActual}/${id}` : `/api/${tabCuantiaActual}`;
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al guardar cuantía');
            }
            
            alert('Cuantía guardada exitosamente');
            ocultarModalCuantia();
            e.target.reset();
            cargarDatosCuantia(tabCuantiaActual);
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al guardar la cuantía');
        }
    };

    // Cargar datos de cuantía con paginación
    async function cargarDatosCuantia(tabla) {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/${tabla}?per_page=1000`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Error al cargar datos');
            
            const datos = await response.json();
            allData = Array.isArray(datos) ? datos : [];
            
            // Calcular paginación
            lastPage = Math.ceil(allData.length / perPage) || 1;
            if (currentPage > lastPage) currentPage = lastPage;
            
            renderizarTablaCuantias();
            renderPagination();
        } catch (error) {
            console.error('Error:', error);
            allData = [];
            renderizarTablaCuantias();
            renderPagination();
        }
    }

    // Renderizar filas en la tabla con paginación
    function renderizarTablaCuantias() {
        const tbody = document.getElementById('tablaCuantias');
        tbody.innerHTML = '';
        
        if (allData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">No hay datos registrados</td></tr>';
            return;
        }
        
        // Calcular rango de datos para la página actual
        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, allData.length);
        const paginatedData = allData.slice(startIndex, endIndex);
        
        paginatedData.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-50';
            tr.innerHTML = `
                <td class="px-4 py-3 text-left">${item.escala}</td>
                <td class="px-4 py-3 text-left">${parseFloat(item.rango_min).toLocaleString()}</td>
                <td class="px-4 py-3 text-left">${parseFloat(item.rango_max).toLocaleString()}</td>
                <td class="px-4 py-3 text-left">${item.porcentaje}%</td>
                <td class="px-4 py-3 text-left">${item.monto_max ? parseFloat(item.monto_max).toLocaleString() : 'N/A'}</td>
                <td class="px-4 py-3 text-left">${item.regla || 'N/A'}</td>
                <td class="px-4 py-3 text-left">${item.monto_base ? parseFloat(item.monto_base).toLocaleString() : 'N/A'}</td>
                <td class="px-4 py-3 text-center">
                    <button onclick="editarCuantia(${item.id})" class="text-black-500 hover:text-black-700 mr-2">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button onclick="eliminarCuantia(${item.id})" class="text-red-500 hover:text-red-700">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Renderizar controles de paginación
    function renderPagination() {
        const container = document.getElementById('paginationControls');
        if (!container) return;
        
        container.innerHTML = '';

        const prevDisabled = currentPage <= 1;
        const prevBtn = `<button ${prevDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage-1})" class="px-3 py-2 text-sm text-black ${prevDisabled ? 'cursor-not-allowed' : ''}">&larr; Anterior</button>`;

        const nextDisabled = currentPage >= lastPage;
        const nextBtn = `<button ${nextDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage+1})" class="px-3 py-2 text-sm text-black ${nextDisabled ? 'cursor-not-allowed' : ''}">Siguiente &rarr;</button>`;

        let pagesHtml = '';
        
        // Mostrar más páginas para que el usuario pueda ver el rango completo
        const maxPagesToShow = 7;
        let start = Math.max(1, currentPage - 3);
        let end = Math.min(lastPage, currentPage + 3);
        
        // Ajustar el inicio y fin para mostrar siempre maxPagesToShow páginas si es posible
        if (end - start + 1 < maxPagesToShow) {
            if (start === 1) {
                end = Math.min(lastPage, maxPagesToShow);
            } else if (end === lastPage) {
                start = Math.max(1, lastPage - maxPagesToShow + 1);
            }
        }

        // Mostrar primera página y puntos suspensivos si es necesario
        if (start > 1) {
            pagesHtml += `<button onclick="goToPage(1)" class="mx-1 px-2 py-1 text-sm text-black">1</button>`;
            if (start > 2) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
        }

        // Mostrar páginas en el rango
        for (let p = start; p <= end; p++) {
            if (p === currentPage) {
                pagesHtml += `<button class="mx-1 px-2 py-1 text-sm bg-gray-200 rounded text-black">${p}</button>`;
            } else {
                pagesHtml += `<button onclick="goToPage(${p})" class="mx-1 px-2 py-1 text-sm text-black">${p}</button>`;
            }
        }

        // Mostrar última página y puntos suspensivos si es necesario
        if (end < lastPage) {
            if (end < lastPage - 1) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
            pagesHtml += `<button onclick="goToPage(${lastPage})" class="mx-1 px-2 py-1 text-sm text-black">${lastPage}</button>`;
        }

        container.innerHTML = `
            <div class="flex items-center justify-between w-full">
                <div>${prevBtn}</div>
                <div class="flex items-center">${pagesHtml}</div>
                <div>${nextBtn}</div>
            </div>
        `;
    }

    function goToPage(page) {
        if (page < 1) page = 1;
        if (page > lastPage) page = lastPage;
        currentPage = page;
        renderizarTablaCuantias();
        renderPagination();
    }

    // Editar cuantía
    async function editarCuantia(id) {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/${tabCuantiaActual}/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Error al cargar cuantía');
            
            const cuantia = await response.json();
            
            // Rellenar el formulario con los datos
            document.getElementById('cuantiaId').value = cuantia.id;
            document.getElementById('cuantiaEscala').value = cuantia.escala;
            document.getElementById('cuantiaRangoMin').value = cuantia.rango_min;
            document.getElementById('cuantiaRangoMax').value = cuantia.rango_max;
            document.getElementById('cuantiaPorcentaje').value = cuantia.porcentaje;
            document.getElementById('cuantiaMontoMax').value = cuantia.monto_max || '';
            document.getElementById('cuantiaMontoBase').value = cuantia.monto_base || '';
            document.getElementById('cuantiaRegla').value = cuantia.regla || '';
            
            // Cambiar el título del modal
            document.getElementById('tituloModalCuantia').textContent = 'Editar Cuantía';
            
            // Mostrar el modal
            mostrarModalCuantia();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar la cuantía para editar');
        }
    }

    // Eliminar cuantía
    async function eliminarCuantia(id) {
        if (!confirm('¿Está seguro de eliminar esta cuantía?')) return;
        
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/${tabCuantiaActual}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Error al eliminar cuantía');
            
            alert('Cuantía eliminada exitosamente');
            cargarDatosCuantia(tabCuantiaActual);
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar la cuantía');
        }
    }
    let contadorGastosAdmin = 1;
    let contadorTribunal = 1;

    function cambiarTipo(tipo) {
        tipoCalculadora = tipo;
        // Tabs visuales
        document.getElementById('btnDeterminada').classList.toggle('text-black', tipo === 'determinada');
        document.getElementById('btnDeterminada').classList.toggle('border-black', tipo === 'determinada');
        document.getElementById('btnDeterminada').classList.toggle('text-gray-400', tipo !== 'determinada');
        document.getElementById('btnDeterminada').classList.toggle('border-transparent', tipo !== 'determinada');
        document.getElementById('btnIndeterminada').classList.toggle('text-black', tipo === 'indeterminada');
        document.getElementById('btnIndeterminada').classList.toggle('border-black', tipo === 'indeterminada');
        document.getElementById('btnIndeterminada').classList.toggle('text-gray-400', tipo !== 'indeterminada');
        document.getElementById('btnIndeterminada').classList.toggle('border-transparent', tipo !== 'indeterminada');
        // Mostrar/ocultar secciones
        document.getElementById('seccionDeterminada').classList.toggle('hidden', tipo !== 'determinada');
        document.getElementById('seccionIndeterminada').classList.toggle('hidden', tipo !== 'indeterminada');
    }

    function agregarRangoGastosAdmin() {
        const container = document.getElementById('gastosAdminRangos');
        const nuevoRango = document.createElement('div');
        nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
        nuevoRango.innerHTML = `
        <div class="flex items-center">
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        </div>
        <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
        <span class="flex items-center justify-center">-</span>
        <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
    `;
        container.appendChild(nuevoRango);
        contadorGastosAdmin++;
    }

    function agregarRangoTribunal() {
        const container = document.getElementById('tribunalRangos');
        const nuevoRango = document.createElement('div');
        nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
        nuevoRango.innerHTML = `
        <div class="flex items-center">
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        </div>
        <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
        <span class="flex items-center justify-center">-</span>
        <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
    `;
        container.appendChild(nuevoRango);
        contadorTribunal++;
    }

    function eliminarRango(elemento) {
        elemento.parentElement.remove();
    }

    function guardarCambios() {
        // Recolectar y validar datos de gastos administrativos
        const gastosAdmin = [];
        let errorRango = null;
        let numeroRango = 0;
        
        document.querySelectorAll('#gastosAdminRangos .grid').forEach(rango => {
            numeroRango++;
            const inputs = rango.querySelectorAll('input');
            const min = parseFloat(inputs[0].value);
            const max = parseFloat(inputs[1].value);
            const porcentaje = parseFloat(inputs[2].value);
            const regla = inputs[3].value;
            const montoMaximo = parseFloat(inputs[4].value);
            const montoBase = parseFloat(inputs[5]?.value); // Si existe un sexto input
            
            if (isNaN(min) || isNaN(max)) {
                errorRango = `Gastos Administrativos - Rango ${numeroRango}: Ingresa valores numéricos válidos en los rangos`;
            } else if (min < 0 || max < 0) {
                errorRango = `Gastos Administrativos - Rango ${numeroRango}: Los valores deben ser positivos`;
            } else if (min >= max) {
                errorRango = `Gastos Administrativos - Rango ${numeroRango}: El rango mínimo (${min}) debe ser menor que el rango máximo (${max})`;
            } else if (!isNaN(montoBase) && !isNaN(montoMaximo) && montoBase > montoMaximo) {
                errorRango = `Gastos Administrativos - Rango ${numeroRango}: El monto base (${montoBase}) no puede ser mayor que el monto máximo (${montoMaximo})`;
            }
            
            gastosAdmin.push({
                rangoMin: inputs[0].value,
                rangoMax: inputs[1].value,
                porcentaje: inputs[2].value,
                regla: inputs[3].value,
                montoMaximo: inputs[4].value
            });
        });

        // Recolectar y validar datos del tribunal
        const tribunal = [];
        numeroRango = 0;
        
        document.querySelectorAll('#tribunalRangos .grid').forEach(rango => {
            numeroRango++;
            const inputs = rango.querySelectorAll('input');
            const min = parseFloat(inputs[0].value);
            const max = parseFloat(inputs[1].value);
            const porcentaje = parseFloat(inputs[2].value);
            const regla = inputs[3].value;
            const montoMaximo = parseFloat(inputs[4].value);
            const montoBase = parseFloat(inputs[5]?.value); // Si existe un sexto input
            
            if (isNaN(min) || isNaN(max)) {
                errorRango = errorRango || `Tribunal Arbitral - Rango ${numeroRango}: Ingresa valores numéricos válidos en los rangos`;
            } else if (min < 0 || max < 0) {
                errorRango = errorRango || `Tribunal Arbitral - Rango ${numeroRango}: Los valores deben ser positivos`;
            } else if (min >= max) {
                errorRango = errorRango || `Tribunal Arbitral - Rango ${numeroRango}: El rango mínimo (${min}) debe ser menor que el rango máximo (${max})`;
            } else if (!isNaN(montoBase) && !isNaN(montoMaximo) && montoBase > montoMaximo) {
                errorRango = errorRango || `Tribunal Arbitral - Rango ${numeroRango}: El monto base (${montoBase}) no puede ser mayor que el monto máximo (${montoMaximo})`;
            }
            
            tribunal.push({
                rangoMin: inputs[0].value,
                rangoMax: inputs[1].value,
                porcentaje: inputs[2].value,
                regla: inputs[3].value,
                montoMaximo: inputs[4].value
            });
        });

        if (errorRango) {
            alert(errorRango);
            return;
        }

        const datos = {
            tipo: tipoCalculadora,
            tipoCambio: document.getElementById('tipoCambio').value,
            gastosAdministrativos: gastosAdmin,
            tribunalArbitral: tribunal
        };

        // Enviar datos al servidor
        guardarDatosCalculadora(datos);
    }

    async function guardarDatosCalculadora(datos) {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch('/api/calculadora/configuracion', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) throw new Error('Error al guardar configuración');

            alert('Configuración guardada exitosamente');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar la configuración');
        }
    }

    async function downloadTarifario() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            
            if (!token) {
                alert('No se encontró token de autenticación. Por favor, inicia sesión nuevamente.');
                return;
            }
            
            // Obtener el nombre original del archivo más reciente
            const responseInfo = await fetch(`/api/tarifario?t=${Date.now()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });
            
            if (!responseInfo.ok) {
                const errorText = await responseInfo.text();
                console.error('Error al obtener info del tarifario:', errorText);
                throw new Error('No se pudo obtener la información del tarifario');
            }
            
            const data = await responseInfo.json();
            let nombreDescarga = 'Tarifario.pdf';
            
            if (Array.isArray(data) && data.length > 0) {
                const ultimo = data[0];
                if (ultimo && ultimo.nombre_original) {
                    nombreDescarga = ultimo.nombre_original;
                }
            } else {
                alert('No hay ningún tarifario disponible para descargar');
                return;
            }
            
            // Descargar el archivo
            const response = await fetch(`/api/tarifario/download?t=${Date.now()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });
            
            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                let errorMsg = 'Error al descargar el tarifario';
                
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    errorMsg = errorData.error || errorData.message || errorMsg;
                } else {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    if (errorText) {
                        errorMsg = errorText;
                    }
                }
                
                throw new Error(errorMsg);
            }
            
            const blob = await response.blob();
            
            // Verificar que el blob tiene contenido
            if (blob.size === 0) {
                throw new Error('El archivo descargado está vacío');
            }
            
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = nombreDescarga;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            console.log('Archivo descargado exitosamente:', nombreDescarga);
        } catch (error) {
            console.error('Error completo:', error);
            alert('Error al descargar el tarifario: ' + error.message);
        }
    }

    async function cargarNombreArchivoTarifario() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            // Agregar timestamp para evitar caché
            const response = await fetch(`/api/tarifario?t=${Date.now()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Cache-Control': 'no-cache'
                }
            });
            if (response.ok) {
                const data = await response.json();
                const inputDisplay = document.querySelector('.border.rounded-lg.px-3.py-2.w-full.bg-gray-50');
                // Tomar el primer registro (el más reciente)
                let ultimo = null;
                if (Array.isArray(data) && data.length > 0) {
                    ultimo = data[0];
                }
                if (ultimo && (ultimo.nombre_original || ultimo.archivo_adjunto)) {
                    const nombre = ultimo.nombre_original ? ultimo.nombre_original : (ultimo.archivo_adjunto ? ultimo.archivo_adjunto.split('/').pop() : '');
                    if (inputDisplay) {
                        inputDisplay.value = `Archivo: ${nombre}`;
                    }
                } else {
                    if (inputDisplay) {
                        inputDisplay.value = 'No hay tarifario subido aún';
                    }
                }
            }
        } catch (error) {
            console.error('Error al cargar nombre de archivo:', error);
            const inputDisplay = document.querySelector('.border.rounded-lg.px-3.py-2.w-full.bg-gray-50');
            if (inputDisplay) {
                inputDisplay.value = 'No hay tarifario subido aún';
            }
        }
    }

    // Cargar configuración inicial al cargar la página
    document.addEventListener('DOMContentLoaded', async () => {
        await cargarNombreArchivoTarifario();
        await cargarDatosCuantia('gastos_administrativos');
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch('/api/calculadora/configuracion', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Error al cargar configuración');
            const config = await response.json();
            cargarConfiguracion(config);
        } catch (error) {
            console.error('Error:', error);
        }
    });

    function cargarConfiguracion(config) {
        if (config.tipo) {
            cambiarTipo(config.tipo);
        }
        if (config.tipoCambio) {
            document.getElementById('tipoCambio').value = config.tipoCambio;
        }

        // Cargar gastos administrativos
        if (config.gastosAdministrativos && config.gastosAdministrativos.length > 0) {
            const container = document.getElementById('gastosAdminRangos');
            container.innerHTML = ''; // Limpiar rangos existentes
            config.gastosAdministrativos.forEach(gasto => {
                const nuevoRango = document.createElement('div');
                nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
                nuevoRango.innerHTML = `
                <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="text" value="${gasto.rangoMin}" class="border rounded-lg px-3 py-2">
                <span class="flex items-center justify-center">-</span>
                <input type="text" value="${gasto.rangoMax}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.porcentaje}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.regla}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.montoMaximo}" class="border rounded-lg px-3 py-2">
            `;
                container.appendChild(nuevoRango);
            });
        }

        // Cargar tribunal arbitral
        if (config.tribunalArbitral && config.tribunalArbitral.length > 0) {
            const container = document.getElementById('tribunalRangos');
            container.innerHTML = ''; // Limpiar rangos existentes
            config.tribunalArbitral.forEach(tribunal => {
                const nuevoRango = document.createElement('div');
                nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
                nuevoRango.innerHTML = `
                <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="text" value="${tribunal.rangoMin}" class="border rounded-lg px-3 py-2">
                <span class="flex items-center justify-center">-</span>
                <input type="text" value="${tribunal.rangoMax}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.porcentaje}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.regla}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.montoMaximo}" class="border rounded-lg px-3 py-2">
            `;
                container.appendChild(nuevoRango);
            });
        }
    }
</script>
@endsection